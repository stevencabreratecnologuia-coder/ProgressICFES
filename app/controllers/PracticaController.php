<?php

declare(strict_types=1);

class PracticaController extends Controller
{
    private const CANTIDADES_PERMITIDAS = [5, 10, 15, 20];

    // Pantalla para elegir materia, tema y cantidad de preguntas.
    public function index(): void
    {
        $this->requireAuthentication();

        $materias = (new Materia())->obtenerTodas();
        $temas = (new Tema())->obtenerTodosAgrupados();

        if (($this->currentUser()['rol'] ?? '') === 'admin') {
            $preguntas = (new Pregunta())->obtenerTodas();
            $tiposPregunta = (new TipoPregunta())->obtenerTodos();
            $mensajes = $_SESSION['admin_mensajes'] ?? [];
            unset($_SESSION['admin_mensajes']);

            $this->render('Practica/admin', [
                'pageTitle' => 'Gestión de práctica | ProgressICFES',
                'activePage' => 'practica',
                'materias' => $materias,
                'temas' => $temas,
                'preguntas' => $preguntas,
                'tiposPregunta' => $tiposPregunta,
                'mensajes' => $mensajes,
            ]);

            return;
        }

        $errors = $_SESSION['practica_errors'] ?? [];
        unset($_SESSION['practica_errors']);

        $this->render('practica/index', [
            'pageTitle' => 'Práctica | ProgressICFES',
            'activePage' => 'practica',
            'materias' => $materias,
            'temas' => $temas,
            'errors' => $errors,
            'resumen' => (new Progreso())->obtenerResumenPractica(
                (int) ($this->currentUser()['id'] ?? 0)
            ),
        ]);
    }

    // Recibe la selección, busca las preguntas, crea el registro
    // de la sesión y arranca la práctica.
    public function iniciar(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $materiaId = (int) ($_POST['materia_id'] ?? 0);

        $temaId = ($_POST['tema_id'] ?? '') !== ''
            ? (int) $_POST['tema_id']
            : null;

        $cantidad = (int) ($_POST['cantidad'] ?? 0);

        if (
            $materiaId <= 0
            || !in_array($cantidad, self::CANTIDADES_PERMITIDAS, true)
        ) {
            $_SESSION['practica_errors'] = [
                'general' => 'Selecciona una materia y una cantidad válida de preguntas.',
            ];

            $this->redirect('practica');
            return;
        }

        $usuarioId = (int) $this->currentUser()['id'];

        // Buscamos la materia seleccionada para conocer su nombre.
        $materiaModel = new Materia();
        $materias = $materiaModel->obtenerTodas();

        $nombreMateria = 'Práctica';

        foreach ($materias as $materia) {
            if ((int) $materia['id'] === $materiaId) {
                $nombreMateria = (string) $materia['nombre'];
                break;
            }
        }

        $preguntaModel = new Pregunta();

        // Por defecto no hay nivel de Inglés.
        $nivelIngles = null;

        /*
         * SI LA MATERIA ES INGLÉS:
         *
         * Calculamos automáticamente el nivel del estudiante
         * según sus últimas respuestas.
         *
         * Ejemplo:
         * 0% - 39%   = A1
         * 40% - 59%  = A2
         * 60% - 74%  = B1
         * 75% - 89%  = B2
         * 90% - 100% = C1
         */
        if (mb_strtolower($nombreMateria) === 'inglés') {
            $progresoModel = new Progreso();

            $nivelIngles = $progresoModel->obtenerNivelIngles(
                $usuarioId
            );

            /*
             * TEMPORALMENTE se obtienen las preguntas como antes.
             *
             * En el siguiente paso modificaremos Pregunta.php para
             * que aquí se obtengan solamente preguntas del nivel:
             *
             * A1, A2, B1, B2 o C1.
             */
            $preguntas = $preguntaModel->obtenerAleatorias(
                $temaId,
                $materiaId,
                $cantidad
            );
        } else {
            /*
             * Las demás materias siguen funcionando exactamente
             * igual que antes.
             */
            $preguntas = $preguntaModel->obtenerAleatorias(
                $temaId,
                $materiaId,
                $cantidad
            );
        }

        if ($preguntas === []) {
            $_SESSION['practica_errors'] = [
                'general' => 'Todavía no hay preguntas disponibles para esa selección. Prueba con otra materia o tema.',
            ];

            $this->redirect('practica');
            return;
        }

        // Cada sesión de práctica queda registrada como un "simulacro"
        // privado de tipo "practica", para poder verla luego en Progreso.

        $simulacroModel = new Simulacro();

        $tituloPractica = 'Práctica libre - ' . $nombreMateria;

        // Si es Inglés, agregamos el nivel al título.
        if ($nivelIngles !== null) {
            $tituloPractica .= ' - Nivel ' . $nivelIngles['nivel'];
        }

        $simulacroId = $simulacroModel->crear(
            $usuarioId,
            $tituloPractica,
            'practica',
            $materiaId,
            0
        );

        foreach ($preguntas as $pregunta) {
            $simulacroModel->agregarPregunta(
                $simulacroId,
                (int) $pregunta['id']
            );
        }

        $_SESSION['practica_actual'] = [
            'simulacro_id' => $simulacroId,
            'materia_id' => $materiaId,
            'tema_id' => $temaId,
            'preguntas' => $preguntas,
            'nivel_ingles' => $nivelIngles,
            'inicio' => time(),
        ];

        $this->redirect('practica/preguntas');
    }

    // Muestra el cuestionario generado.
    public function preguntas(): void
    {
        $this->requireAuthentication();

        $sesionPractica = $_SESSION['practica_actual'] ?? null;

        if ($sesionPractica === null) {
            $this->redirect('practica');
            return;
        }

        $this->render('practica/preguntas', [
            'pageTitle' => 'Práctica | ProgressICFES',
            'activePage' => 'practica',
            'preguntas' => $sesionPractica['preguntas'],
            'nivelIngles' => $sesionPractica['nivel_ingles'] ?? null,
        ]);
    }

    // Corrige las respuestas enviadas, las guarda en la base de datos
    // y muestra el resultado.
    public function responder(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $sesionPractica = $_SESSION['practica_actual'] ?? null;

        if ($sesionPractica === null) {
            $this->redirect('practica');
            return;
        }

        $respuestasEnviadas = $_POST['respuestas'] ?? [];
        $detalle = [];
        $correctas = 0;
        $respuestasParaGuardar = [];

        foreach ($sesionPractica['preguntas'] as $pregunta) {
            $opcionSeleccionadaRaw =
                $respuestasEnviadas[$pregunta['id']] ?? null;

            $opcionSeleccionada =
                $opcionSeleccionadaRaw !== null
                && $opcionSeleccionadaRaw !== ''
                ? strtolower(
                    trim((string) $opcionSeleccionadaRaw)
                )
                : null;

            $respuestaCorrecta = strtolower(
                trim((string) $pregunta['respuesta_correcta'])
            );

            $esCorrecta =
                $opcionSeleccionada !== null
                && $opcionSeleccionada === $respuestaCorrecta;

            if ($esCorrecta) {
                $correctas++;
            }

            $detalle[] = [
                'pregunta' => $pregunta,
                'opcion_seleccionada' => $opcionSeleccionada,
                'es_correcta' => $esCorrecta,
            ];

            $respuestasParaGuardar[] = [
                'pregunta_id' => (int) $pregunta['id'],
                'opcion_seleccionada' => $opcionSeleccionada,
                'es_correcta' => $esCorrecta,
            ];
        }

        $total = count($sesionPractica['preguntas']);

        $tiempoUsadoSeg = max(
            0,
            time() - (int) (
                $sesionPractica['inicio'] ?? time()
            )
        );

        $puntajeIcfes = $total > 0
            ? (int) round(($correctas / $total) * 500)
            : 0;

        $usuarioId = (int) $this->currentUser()['id'];

        $resultadoModel = new ResultadoSimulacro();

        $resultadoId = $resultadoModel->crear(
            $usuarioId,
            (int) $sesionPractica['simulacro_id'],
            $correctas,
            $puntajeIcfes,
            $tiempoUsadoSeg
        );

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
        foreach ($detalle as $item) {
            if (!empty($item['es_correcta'])) {
                continue;
            }

            $pregunta = $item['pregunta'] ?? [];
            $preguntaCompleta = (new Pregunta())->obtenerPorId((int) ($pregunta['id'] ?? 0));

            $erroresPlan[] = [
                'pregunta_id' => (int) ($pregunta['id'] ?? 0),
                'tema' => (string) ($preguntaCompleta['tema_nombre'] ?? 'No disponible'),
                'dificultad' => (string) ($pregunta['dificultad'] ?? 'No disponible'),
                'enunciado' => (string) ($pregunta['enunciado'] ?? ''),
                'respuesta_estudiante' => $item['opcion_seleccionada'] !== null
                    ? strtoupper((string) $item['opcion_seleccionada'])
                    : 'Sin respuesta',
                'explicacion' => (string) ($pregunta['explicacion'] ?? ''),
            ];
        }

        $materiaPromptIA = '';
        foreach ($sesionPractica['preguntas'] as $preguntaMateria) {
            $preguntaCompletaMateria = (new Pregunta())->obtenerPorId((int) ($preguntaMateria['id'] ?? 0));
            if ($preguntaCompletaMateria !== null) {
                $materiaPromptIA = (string) ($preguntaCompletaMateria['materia_nombre'] ?? '');
                if ($materiaPromptIA !== '') {
                    break;
                }
            }
        }

        if ($erroresPlan !== []) {
            try {
                $usuarioCompleto = (new Usuario())->buscarPorId($usuarioId);
                $grado = (int) ($usuarioCompleto['grado'] ?? 0);
                $materiaPlan = '';

                foreach ($preguntas = $sesionPractica['preguntas'] as $preguntaPlan) {
                    $preguntaCompleta = (new Pregunta())->obtenerPorId((int) ($preguntaPlan['id'] ?? 0));
                    if ($preguntaCompleta !== null) {
                        $materiaPlan = (string) ($preguntaCompleta['materia_nombre'] ?? '');
                        if ($materiaPlan !== '') {
                            break;
                        }
                    }
                }

                $planRefuerzo = (new EstudianteIAService())->generarPlanRefuerzo(
                    $usuarioId,
                    $grado,
                    $materiaPlan,
                    'practica',
                    $erroresPlan
                );
            } catch (Throwable $e) {
                $planRefuerzo = [
                    'error' => 'No fue posible generar el plan automático en este momento. Tus resultados sí fueron guardados correctamente.',
                ];
            }
        }

        $porcentajeFinal = $total > 0
            ? (int) round(($correctas / $total) * 100)
            : 0;

        $_SESSION['practica_resultado'] = [
            'total' => $total,
            'correctas' => $correctas,
            'porcentaje' => $porcentajeFinal,
            'detalle' => $detalle,
            'nivel_ingles' => $sesionPractica['nivel_ingles'] ?? null,
            'plan_refuerzo' => $planRefuerzo,
            'prompt_ia' => $this->construirPromptIA(
                $materiaPromptIA,
                $correctas,
                $total,
                $porcentajeFinal,
                $erroresPlan
            ),
        ];

        unset($_SESSION['practica_actual']);

        $this->redirect('practica/resultado');
    }

    // Muestra el resultado final con la retroalimentación por pregunta.
    public function resultado(): void
    {
        $this->requireAuthentication();

        $resultado = $_SESSION['practica_resultado'] ?? null;

        if ($resultado === null) {
            $this->redirect('practica');
            return;
        }

        $this->render('practica/resultado', [
            'pageTitle' => 'Resultado | ProgressICFES',
            'activePage' => 'practica',
            'resultado' => $resultado,
        ]);
    }

    /**
     * Arma un texto listo para copiar y pegar en una IA (ProgressIA u otra),
     * resumiendo cómo le fue al estudiante en esta práctica.
     */
    private function construirPromptIA(
        string $materia,
        int $correctas,
        int $total,
        int $porcentaje,
        array $erroresPlan
    ): string {
        $lineas = [];
        $lineas[] = 'Acabo de terminar una práctica de ' . ($materia !== '' ? $materia : 'ICFES') . ' en ProgressICFES.';
        $lineas[] = "Resultado: {$correctas} de {$total} correctas ({$porcentaje}%).";
        $lineas[] = '';

        if ($erroresPlan === []) {
            $lineas[] = 'No tuve preguntas incorrectas esta vez.';
            $lineas[] = 'Dame 2 o 3 ejercicios un poco más difíciles del mismo tema para seguir practicando.';
        } else {
            $lineas[] = 'Estas son las preguntas que respondí mal:';
            $lineas[] = '';

            foreach ($erroresPlan as $indice => $error) {
                $numero = $indice + 1;
                $lineas[] = "{$numero}. Tema: {$error['tema']} (dificultad: {$error['dificultad']})";
                $lineas[] = "   Pregunta: {$error['enunciado']}";
                $lineas[] = "   Mi respuesta: {$error['respuesta_estudiante']}";
                $lineas[] = '';
            }

            $lineas[] = 'Ayúdame a entender por qué fallé estas preguntas, explícamelo paso a paso sin darme';
            $lineas[] = 'de una vez la respuesta, y dame un plan corto de 3 días para reforzar estos temas.';
        }

        return implode("\n", $lineas);
    }
}
