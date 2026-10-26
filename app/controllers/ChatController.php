<?php

declare(strict_types=1);

class ChatController extends Controller
{
    /**
     * Muestra ProgressIA
     */
    public function index(): void
    {
        $this->requireAuthentication();

        $usuario = $this->currentUser();

        if (!$usuario) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $usuarioId = (int) (
            $usuario['id']
            ?? $usuario['usuario_id']
            ?? 0
        );

        if ($usuarioId <= 0) {
            http_response_code(500);
            exit('No se pudo identificar al usuario.');
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener conversaciones del estudiante
        |--------------------------------------------------------------------------
        */

        $conversacionModel = new Conversacion();

        $conversaciones = $conversacionModel
            ->obtenerPorUsuario($usuarioId);

        $esProfesor = (($usuario['rol'] ?? '') === 'profesor');
        $contextoIA = null;
        $materia = null;
        $borrador = $_SESSION['profesor_ia_borrador'] ?? null;
        $errorProfesorIA = $_SESSION['profesor_ia_error'] ?? '';
        $exitoProfesorIA = $_SESSION['profesor_ia_exito'] ?? '';
        unset($_SESSION['profesor_ia_error'], $_SESSION['profesor_ia_exito']);

        if ($esProfesor) {
            try {
                $usuarioCompleto = (new Usuario())->buscarPorId($usuarioId);
                $materiaId = (int) ($usuarioCompleto['materia_id'] ?? 0);
                $materia = $materiaId > 0 ? (new Materia())->obtenerPorId($materiaId) : null;
                if ($materiaId > 0) {
                    $contextoIA = (new ProfesorIAService())->obtenerContextoProfesor($usuarioId);
                }
            } catch (Throwable $e) {
                $errorProfesorIA = $e->getMessage();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Mostrar vista
        |--------------------------------------------------------------------------
        */

        $this->render('chat/index', [
            'pageTitle' => 'ProgressIA',
            'activePage' => 'chat',
            'conversaciones' => $conversaciones,
            'esProfesor' => $esProfesor,
            'contextoIA' => $contextoIA,
            'materia' => $materia,
            'borrador' => $borrador,
            'errorProfesorIA' => $errorProfesorIA,
            'exitoProfesorIA' => $exitoProfesorIA,
        ]);
    }

    /**
     * Procesa un mensaje enviado desde ProgressIA
     */
    public function enviar(): void
    {
        $this->requireAuthentication();

        header('Content-Type: application/json; charset=utf-8');

        try {

            $mensaje = trim($_POST['mensaje'] ?? '');

            if ($mensaje === '') {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => 'Debes escribir un mensaje.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            /*
            |--------------------------------------------------------------------------
            | Usuario autenticado
            |--------------------------------------------------------------------------
            */

            $usuario = $this->currentUser();

            if (!$usuario) {

                http_response_code(401);

                echo json_encode([
                    'success' => false,
                    'message' => 'La sesión ha expirado.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $usuarioId = (int) (
                $usuario['id']
                ?? $usuario['usuario_id']
                ?? 0
            );

            if ($usuarioId <= 0) {

                throw new Exception(
                    'No se pudo identificar al usuario.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Conversación actual
            |--------------------------------------------------------------------------
            */

            $conversacionId = null;

            if (isset($_POST['conversacion_id'])) {

                $valorConversacion = trim(
                    (string) $_POST['conversacion_id']
                );

                if ($valorConversacion !== '') {

                    $conversacionId = (int) $valorConversacion;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Procesar conversación
            |--------------------------------------------------------------------------
            */

            $conversationService = new ConversationService();

            $resultado = $conversationService->procesar(
                $usuarioId,
                $mensaje,
                $conversacionId
            );

            /*
            |--------------------------------------------------------------------------
            | Respuesta
            |--------------------------------------------------------------------------
            */

            echo json_encode([

                'success' => true,

                'conversacion_id' =>
                $resultado['conversacion_id'],

                'respuesta' =>
                $resultado['respuesta']

            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([

                'success' => false,

                'message' => $e->getMessage()

            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * Orienta al estudiante sobre una pregunta que respondió mal.
     *
     * El servidor vuelve a validar que la pregunta pertenezca al resultado
     * del estudiante. El cliente nunca puede enviar una pregunta arbitraria
     * para obtener su respuesta.
     */
    public function orientarPregunta(): void
    {
        $this->requireAuthentication();

        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'La sesión de seguridad expiró. Recarga la página e inténtalo nuevamente.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $usuario = $this->currentUser();

            if (!$usuario) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'La sesión ha expirado.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $usuarioId = (int) $usuario['id'];
            $preguntaId = (int) ($_POST['pregunta_id'] ?? 0);
            $resultadoId = (int) ($_POST['resultado_id'] ?? 0);
            $origen = strtolower(trim((string) ($_POST['origen'] ?? '')));
            $mensaje = trim((string) ($_POST['mensaje'] ?? ''));

            if ($preguntaId <= 0 || !in_array($origen, ['practica', 'simulacro'], true)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'La pregunta o el tipo de resultado no son válidos.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $pregunta = null;
            $respuestaEstudiante = null;
            $esCorrecta = null;

            if ($origen === 'simulacro') {
                if ($resultadoId <= 0) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se encontró el resultado del simulacro.',
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $respuestaModel = new RespuestaEstudiante();
                $fila = $respuestaModel->obtenerPreguntaDeResultado(
                    $resultadoId,
                    $preguntaId,
                    $usuarioId
                );

                if ($fila === null) {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Esa pregunta no pertenece a uno de tus resultados.',
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $pregunta = $fila;
                $respuestaEstudiante = $fila['opcion_seleccionada'] ?? null;
                $esCorrecta = (bool) $fila['es_correcta'];
            } else {
                $sesionResultado = $_SESSION['practica_resultado'] ?? null;

                if (!is_array($sesionResultado) || !isset($sesionResultado['detalle']) || !is_array($sesionResultado['detalle'])) {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se encontró el resultado de esta práctica.',
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                foreach ($sesionResultado['detalle'] as $item) {
                    $itemPregunta = $item['pregunta'] ?? null;

                    if (is_array($itemPregunta) && (int) ($itemPregunta['id'] ?? 0) === $preguntaId) {
                        $pregunta = $itemPregunta;
                        $respuestaEstudiante = $item['opcion_seleccionada'] ?? null;
                        $esCorrecta = (bool) ($item['es_correcta'] ?? false);
                        break;
                    }
                }

                if ($pregunta === null) {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Esa pregunta no pertenece a tu práctica actual.',
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            if ($esCorrecta === true) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'ProgressIA solo ofrece esta orientación para preguntas incorrectas o sin respuesta.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $usuarioCompleto = (new Usuario())->buscarPorId($usuarioId);
            $grado = (int) ($usuarioCompleto['grado'] ?? 0);

            $tema = '';
            $materia = '';

            if (isset($pregunta['tema_nombre'])) {
                $tema = (string) $pregunta['tema_nombre'];
            }

            if (isset($pregunta['materia_nombre'])) {
                $materia = (string) $pregunta['materia_nombre'];
            }

            if ($tema === '' || $materia === '') {
                $preguntaCompleta = (new Pregunta())->obtenerPorId($preguntaId);

                if ($preguntaCompleta !== null) {
                    $tema = $tema !== '' ? $tema : (string) ($preguntaCompleta['tema_nombre'] ?? '');
                    $materia = $materia !== '' ? $materia : (string) ($preguntaCompleta['materia_nombre'] ?? '');
                }
            }

            if ($mensaje === '') {
                $mensaje = 'Ayúdame a entender esta pregunta que respondí mal. No me des directamente la respuesta; guíame paso a paso para que yo pueda encontrarla.';
            }

            $contexto = [
                'grado' => $grado > 0 ? (string) $grado : 'no disponible',
                'materia' => $materia !== '' ? $materia : 'no disponible',
                'tema' => $tema !== '' ? $tema : 'no disponible',
                'dificultad' => (string) ($pregunta['dificultad'] ?? 'no disponible'),
                'enunciado' => (string) ($pregunta['enunciado'] ?? ''),
                'opcion_seleccionada' => $respuestaEstudiante !== null && $respuestaEstudiante !== ''
                    ? strtoupper((string) $respuestaEstudiante)
                    : 'Sin respuesta',
                'respuesta_correcta' => (string) ($pregunta['respuesta_correcta'] ?? ''),
                'explicacion' => (string) ($pregunta['explicacion'] ?? ''),
            ];

            $historial = [
                [
                    'role' => 'system',
                    'content' => PromptBuilder::orientacionPreguntaIncorrecta($contexto),
                ],
                [
                    'role' => 'user',
                    'content' => $mensaje,
                ],
            ];

            $respuestaIA = (new OpenAIService())->preguntarConHistorial($historial);

            echo json_encode([
                'success' => true,
                'respuesta' => $respuestaIA,
            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * Devuelve el historial completo de una conversación (para verla en detalle)
     */
    public function cargar(): void
    {
        $this->requireAuthentication();

        header('Content-Type: application/json; charset=utf-8');

        try {

            $usuario = $this->currentUser();

            if (!$usuario) {

                http_response_code(401);

                echo json_encode([
                    'success' => false,
                    'message' => 'La sesión ha expirado.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $usuarioId = (int) (
                $usuario['id']
                ?? $usuario['usuario_id']
                ?? 0
            );

            $conversacionId = (int) ($_POST['conversacion_id'] ?? 0);

            if ($conversacionId <= 0) {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => 'Conversación inválida.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $conversacionModel = new Conversacion();
            $conversacion = $conversacionModel->buscar($conversacionId);

            if ($conversacion === null || (int) $conversacion['usuario_id'] !== $usuarioId) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' => 'Esa conversación no existe o no te pertenece.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $mensajeModel = new Mensaje();
            $mensajes = $mensajeModel->obtenerHistorial($conversacionId);

            echo json_encode([

                'success' => true,

                'conversacion_id' => $conversacionId,

                'titulo' => $conversacion['titulo'],

                'mensajes' => array_map(
                    static fn(array $mensaje): array => [
                        'rol' => $mensaje['rol'],
                        'mensaje' => $mensaje['mensaje'],
                    ],
                    $mensajes
                ),

            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([

                'success' => false,

                'message' => $e->getMessage()

            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * Elimina una conversación completa (y sus mensajes) del estudiante que la pide
     */
    public function eliminarConversacion(): void
    {
        $this->requireAuthentication();

        header('Content-Type: application/json; charset=utf-8');

        try {

            $usuario = $this->currentUser();

            if (!$usuario) {

                http_response_code(401);

                echo json_encode([
                    'success' => false,
                    'message' => 'La sesión ha expirado.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $usuarioId = (int) (
                $usuario['id']
                ?? $usuario['usuario_id']
                ?? 0
            );

            $conversacionId = (int) ($_POST['conversacion_id'] ?? 0);

            if ($conversacionId <= 0) {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => 'Conversación inválida.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $conversacionModel = new Conversacion();
            $conversacion = $conversacionModel->buscar($conversacionId);

            if ($conversacion === null || (int) $conversacion['usuario_id'] !== $usuarioId) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' => 'Esa conversación no existe o no te pertenece.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            (new Mensaje())->eliminarPorConversacion($conversacionId);
            $conversacionModel->eliminar($conversacionId);

            echo json_encode([

                'success' => true,

            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([

                'success' => false,

                'message' => $e->getMessage()

            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
}
