<?php

declare(strict_types=1);

class SimulacroController extends Controller
{
    /**
     * Lista los simulacros según el rol.
     *
     * ADMIN:
     * - Ve todos los simulacros.
     *
     * PROFESOR:
     * - Ve únicamente sus simulacros de su materia.
     *
     * ESTUDIANTE:
     * - Ve los simulacros disponibles para presentar.
     */
    public function index(): void
    {
        $this->requireAuthentication();

        $usuario = $this->currentUser();
        $rol = $usuario['rol'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($rol === 'admin') {
            $simulacros = (new Simulacro())->obtenerTodosAdmin();

            $mensajes = $_SESSION['admin_mensajes'] ?? [];
            unset($_SESSION['admin_mensajes']);

            $this->render('simulacro/admin', [
                'pageTitle' => 'Gestión de simulacros | ProgressICFES',
                'activePage' => 'simulacros',
                'simulacros' => $simulacros,
                'mensajes' => $mensajes,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PROFESOR
        |--------------------------------------------------------------------------
        |
        | El profesor no utiliza el panel general de simulacros.
        | Se envía a su gestión personalizada.
        |
        */

        if ($rol === 'profesor') {
            (new ProfesorController())->simulacros();
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ESTUDIANTE
        |--------------------------------------------------------------------------
        */

        $estudianteId = (int) ($usuario['id'] ?? 0);

        // Solo se listan los simulacros que el estudiante todavía no ha presentado.
        $simulacros = (new Simulacro())->obtenerDisponibles($estudianteId);

        $errors = $_SESSION['simulacro_errors'] ?? [];
        unset($_SESSION['simulacro_errors']);
        $resultadoModel = new ResultadoSimulacro();

        $this->render('simulacro/index', [
            'pageTitle' => 'Simulacros | ProgressICFES',
            'activePage' => 'simulacros',
            'simulacros' => $simulacros,
            'errors' => $errors,
            'historial' => $resultadoModel->obtenerRecientes($estudianteId, 3),
            'resumen' => $resultadoModel->obtenerResumen($estudianteId),
        ]);
    }

    /**
     * Inicia un simulacro.
     */
    public function iniciar(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('simulacros');
            return;
        }

        $simulacroId = (int) ($_POST['simulacro_id'] ?? 0);

        $simulacroModel = new Simulacro();

        $simulacro = $simulacroId > 0
            ? $simulacroModel->obtenerPorId($simulacroId)
            : null;

        if ($simulacro === null) {
            $_SESSION['simulacro_errors'] = [
                'general' => 'Ese simulacro ya no está disponible.'
            ];

            $this->redirect('simulacros');
            return;
        }

        // Cada simulacro se presenta una sola vez por estudiante.
        if ($simulacroModel->yaPresentado($simulacroId, (int) ($this->currentUser()['id'] ?? 0))) {
            $_SESSION['simulacro_errors'] = [
                'general' => 'Ya presentaste este simulacro.'
            ];

            $this->redirect('simulacros');
            return;
        }

        $preguntas = $simulacroModel->obtenerPreguntas($simulacroId);

        if ($preguntas === []) {
            $_SESSION['simulacro_errors'] = [
                'general' => 'Ese simulacro todavía no tiene preguntas cargadas.'
            ];

            $this->redirect('simulacros');
            return;
        }

        $_SESSION['simulacro_actual'] = [
            'simulacro_id' => $simulacroId,
            'titulo' => $simulacro['titulo'],
            'tiempo_limite_min' => (int) $simulacro['tiempo_limite_min'],
            'preguntas' => $preguntas,
            'inicio' => time(),
        ];

        $this->redirect('simulacro/preguntas');
    }

    /**
     * Muestra las preguntas del simulacro.
     */
    public function preguntas(): void
    {
        $this->requireAuthentication();

        $sesion = $_SESSION['simulacro_actual'] ?? null;

        if ($sesion === null) {
            $this->redirect('simulacros');
            return;
        }

        $this->render('simulacro/preguntas', [
            'pageTitle' => 'Simulacro | ProgressICFES',
            'activePage' => 'simulacros',
            'titulo' => $sesion['titulo'],
            'preguntas' => $sesion['preguntas'],
            'tiempoLimiteMin' => $sesion['tiempo_limite_min'],
        ]);
    }

    /**
     * Corrige y guarda el simulacro.
     */
    public function responder(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('simulacros');
            return;
        }

        $sesion = $_SESSION['simulacro_actual'] ?? null;

        if ($sesion === null) {
            $this->redirect('simulacros');
            return;
        }

        $respuestasEnviadas = $_POST['respuestas'] ?? [];

        $correctas = 0;
        $respuestasParaGuardar = [];

        foreach ($sesion['preguntas'] as $pregunta) {
            $opcionSeleccionadaRaw =
                $respuestasEnviadas[$pregunta['id']] ?? null;

            $opcionSeleccionada =
                $opcionSeleccionadaRaw !== null &&
                $opcionSeleccionadaRaw !== ''
                ? strtolower(trim((string) $opcionSeleccionadaRaw))
                : null;

            $respuestaCorrecta =
                strtolower(trim((string) $pregunta['respuesta_correcta']));

            $esCorrecta =
                $opcionSeleccionada !== null &&
                $opcionSeleccionada === $respuestaCorrecta;

            if ($esCorrecta) {
                $correctas++;
            }

            $respuestasParaGuardar[] = [
                'pregunta_id' => (int) $pregunta['id'],
                'opcion_seleccionada' => $opcionSeleccionada,
                'es_correcta' => $esCorrecta,
            ];
        }

        $total = count($sesion['preguntas']);

        $tiempoUsadoSeg = max(
            0,
            time() - (int) $sesion['inicio']
        );

        $puntajeIcfes = $total > 0
            ? (int) round(($correctas / $total) * 500)
            : 0;

        $usuarioId = (int) $this->currentUser()['id'];

        /*
        |--------------------------------------------------------------------------
        | GUARDAR RESULTADO
        |--------------------------------------------------------------------------
        */

        $resultadoModel = new ResultadoSimulacro();

        $resultadoId = $resultadoModel->crear(
            $usuarioId,
            (int) $sesion['simulacro_id'],
            $correctas,
            $puntajeIcfes,
            $tiempoUsadoSeg
        );

        /*
        |--------------------------------------------------------------------------
        | GUARDAR RESPUESTAS
        |--------------------------------------------------------------------------
        */

        $respuestaModel = new RespuestaEstudiante();

        foreach ($respuestasParaGuardar as $respuesta) {
            $respuestaModel->guardar(
                $resultadoId,
                $respuesta['pregunta_id'],
                $respuesta['opcion_seleccionada'],
                $respuesta['es_correcta']
            );
        }

        $planRefuerzo = null;

        $erroresPlan = [];
        $detalleResultado = (new RespuestaEstudiante())->obtenerPorResultado($resultadoId);

        foreach ($detalleResultado as $item) {
            if (!empty($item['es_correcta'])) {
                continue;
            }

            $erroresPlan[] = [
                'pregunta_id' => (int) ($item['pregunta_id'] ?? 0),
                'tema' => (string) ($item['tema_nombre'] ?? 'No disponible'),
                'dificultad' => (string) ($item['dificultad'] ?? 'No disponible'),
                'enunciado' => (string) ($item['enunciado'] ?? ''),
                'respuesta_estudiante' => $item['opcion_seleccionada'] !== null
                    ? strtoupper((string) $item['opcion_seleccionada'])
                    : 'Sin respuesta',
                'explicacion' => (string) ($item['explicacion'] ?? ''),
            ];
        }

        if ($erroresPlan !== []) {
            try {
                $usuarioCompleto = (new Usuario())->buscarPorId($usuarioId);
                $grado = (int) ($usuarioCompleto['grado'] ?? 0);
                $materiaPlan = (string) ($detalleResultado[0]['materia_nombre'] ?? '');

                $planRefuerzo = (new EstudianteIAService())->generarPlanRefuerzo(
                    $usuarioId,
                    $grado,
                    $materiaPlan,
                    'simulacro',
                    $erroresPlan
                );
            } catch (Throwable $e) {
                $planRefuerzo = [
                    'error' => 'No fue posible generar el plan automático en este momento. Tu resultado sí fue guardado correctamente.',
                ];
            }
        }

        $_SESSION['simulacro_plan_refuerzo'] = [
            'resultado_id' => $resultadoId,
            'plan' => $planRefuerzo,
        ];

        unset($_SESSION['simulacro_actual']);

        $this->redirect(
            'simulacro/resultado?id=' . $resultadoId
        );
    }

    /**
     * Muestra el resultado del simulacro.
     */
    public function resultado(): void
    {
        $this->requireAuthentication();

        $resultadoId = (int) ($_GET['id'] ?? 0);

        $usuarioId = (int) $this->currentUser()['id'];

        $resultado = $resultadoId > 0
            ? (new ResultadoSimulacro())
            ->obtenerPorId($resultadoId, $usuarioId)
            : null;

        if ($resultado === null) {
            $this->redirect('simulacros');
            return;
        }

        $detalle =
            (new RespuestaEstudiante())
            ->obtenerPorResultado($resultadoId);

        $totalPreguntasPrompt = count($detalle);
        $correctasPrompt = (int) $resultado['puntaje'];
        $porcentajePrompt = $totalPreguntasPrompt > 0
            ? (int) round(($correctasPrompt / $totalPreguntasPrompt) * 100)
            : 0;

        $promptIA = $this->construirPromptIA(
            (string) $resultado['titulo'],
            $correctasPrompt,
            $totalPreguntasPrompt,
            $porcentajePrompt,
            (int) $resultado['puntaje_estimado_icfes'],
            $detalle
        );

        $planRefuerzo = null;
        $planSesion = $_SESSION['simulacro_plan_refuerzo'] ?? null;
        if (is_array($planSesion) && (int) ($planSesion['resultado_id'] ?? 0) === $resultadoId) {
            $planRefuerzo = $planSesion['plan'] ?? null;
            unset($_SESSION['simulacro_plan_refuerzo']);
        }

        $this->render('simulacro/resultado', [
            'pageTitle' => 'Resultado del simulacro | ProgressICFES',
            'activePage' => 'simulacros',
            'resultado' => $resultado,
            'detalle' => $detalle,
            'planRefuerzo' => $planRefuerzo,
            'promptIA' => $promptIA,
        ]);
    }

    /**
     * Arma un texto listo para copiar y pegar en una IA (ProgressIA u otra),
     * resumiendo cómo le fue al estudiante en este simulacro.
     */
    private function construirPromptIA(
        string $titulo,
        int $correctas,
        int $total,
        int $porcentaje,
        int $puntajeIcfes,
        array $detalle
    ): string {
        $lineas = [];
        $lineas[] = "Acabo de presentar el simulacro \"{$titulo}\" en ProgressICFES.";
        $lineas[] = "Resultado: {$correctas} de {$total} correctas ({$porcentaje}%).";
        $lineas[] = "Puntaje estimado ICFES: {$puntajeIcfes}/500.";
        $lineas[] = '';

        $errores = array_values(array_filter($detalle, static fn(array $item): bool => empty($item['es_correcta'])));

        if ($errores === []) {
            $lineas[] = 'No tuve preguntas incorrectas esta vez.';
            $lineas[] = 'Dame 2 o 3 ejercicios un poco más difíciles para seguir practicando.';
        } else {
            $porTema = [];
            foreach ($errores as $error) {
                $tema = (string) ($error['tema_nombre'] ?? 'No disponible');
                $porTema[$tema] = ($porTema[$tema] ?? 0) + 1;
            }

            $lineas[] = 'Estas fueron mis materias/temas con errores:';
            foreach ($porTema as $tema => $cantidad) {
                $lineas[] = "- {$tema}: {$cantidad} error(es)";
            }
            $lineas[] = '';

            $lineas[] = 'Estas son las preguntas que respondí mal:';
            $lineas[] = '';

            foreach ($errores as $indice => $error) {
                $numero = $indice + 1;
                $respuesta = $error['opcion_seleccionada'] !== null
                    ? strtoupper((string) $error['opcion_seleccionada'])
                    : 'Sin respuesta';
                $lineas[] = "{$numero}. Materia: {$error['materia_nombre']} · Tema: {$error['tema_nombre']}";
                $lineas[] = "   Pregunta: {$error['enunciado']}";
                $lineas[] = "   Mi respuesta: {$respuesta}";
                $lineas[] = '';
            }

            $lineas[] = 'Ayúdame a entender por qué fallé estas preguntas, explícamelo paso a paso sin darme';
            $lineas[] = 'de una vez la respuesta, y dame un plan corto de 3 días para reforzar estos temas.';
        }

        return implode("\n", $lineas);
    }
}
