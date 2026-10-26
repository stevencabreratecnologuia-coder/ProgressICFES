<?php

declare(strict_types=1);

class ConversationService
{
    private Conversacion $conversacionModel;
    private Mensaje $mensajeModel;
    private OpenAIService $openAI;

    public function __construct()
    {
        $this->conversacionModel = new Conversacion();
        $this->mensajeModel = new Mensaje();
        $this->openAI = new OpenAIService();
    }

    /**
     * Procesa el mensaje del usuario
     */
    public function procesar(
        int $usuarioId,
        string $mensajeUsuario,
        ?int $conversacionId = null
    ): array {

        /*
        -----------------------------
        Crear conversación si no existe
        -----------------------------
        */

        if ($conversacionId === null) {

            $titulo = mb_substr(
                trim($mensajeUsuario),
                0,
                60
            );

            $conversacionId = $this->conversacionModel->crear(
                $usuarioId,
                $titulo
            );
        }

        /*
        -----------------------------
        Guardar mensaje usuario
        -----------------------------
        */

        $this->mensajeModel->guardar(
            $conversacionId,
            'user',
            $mensajeUsuario
        );

        /*
        -----------------------------
        Obtener historial
        -----------------------------
        */

        $historialDB = $this->mensajeModel
            ->obtenerHistorial($conversacionId);

        $historial = [];

        $usuario = (new Usuario())->buscarPorId($usuarioId);
        $rol = strtolower((string) ($usuario['rol'] ?? 'estudiante'));

        if ($rol === 'profesor') {
            $promptSistema = PromptBuilder::profesor();

            try {
                $contextoProfesor = (new ProfesorIAService())->obtenerContextoProfesor($usuarioId);
                $promptSistema .= "\n\nCONTEXTO REAL DEL PROFESOR ENTREGADO POR EL BACKEND (fuente de verdad; no inventes):\n" .
                    json_encode($contextoProfesor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $promptSistema .= "\n\nEl backend no pudo cargar el contexto académico completo del profesor. No inventes datos ni estudiantes.";
            }
        } elseif ($rol === 'admin') {
            $promptSistema = PromptBuilder::maestro() . "\n\nCONTEXTO ESPECÍFICO: estás atendiendo a un administrador autenticado. Respeta únicamente las funciones administrativas autorizadas por el backend y no inventes datos.";
        } else {
            $promptSistema = PromptBuilder::estudiante();

            if (is_array($usuario)) {
                $grado = (int) ($usuario['grado'] ?? 0);
                $materiaId = (int) ($usuario['materia_id'] ?? 0);
                $promptSistema .= "\n\nCONTEXTO BÁSICO REAL DEL ESTUDIANTE: grado=" . ($grado > 0 ? $grado : 'no disponible') . ", materia_id=" . ($materiaId > 0 ? $materiaId : 'no disponible') . ". No inventes información académica que el backend no haya proporcionado.";
            }
        }

        $historial[] = [
            'role' => 'system',
            'content' => $promptSistema
        ];

        foreach ($historialDB as $mensaje) {

            $historial[] = [

                'role' => $mensaje['rol'],

                'content' => $mensaje['mensaje']

            ];
        }

        /*
        -----------------------------
        Preguntar a OpenAI
        -----------------------------
        */

        $respuestaIA = $this->openAI
            ->preguntarConHistorial($historial);

        /*
        -----------------------------
        Guardar respuesta
        -----------------------------
        */

        $this->mensajeModel->guardar(

            $conversacionId,

            'assistant',

            $respuestaIA

        );

        /*
        -----------------------------
        Actualizar actividad
        -----------------------------
        */

        $this->conversacionModel
            ->actualizarActividad(
                $conversacionId
            );

        return [

            'conversacion_id' => $conversacionId,

            'respuesta' => $respuestaIA

        ];
    }
}
