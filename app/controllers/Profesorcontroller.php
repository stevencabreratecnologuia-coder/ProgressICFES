<?php

declare(strict_types=1);

class ProfesorController extends Controller
{
    private function contexto(): ?array
    {
        $this->requireAuthentication();

        if (($this->currentUser()['rol'] ?? '') !== 'profesor') {
            $this->redirect('');
            return null;
        }

        $usuario = (new Usuario())->buscarPorId((int) $this->currentUser()['id']);

        if ($usuario === null) {
            $this->redirect('auth/login');
            return null;
        }

        $materiaId = isset($usuario['materia_id']) && $usuario['materia_id'] !== null
            ? (int) $usuario['materia_id']
            : 0;

        $materia = $materiaId > 0
            ? (new Materia())->obtenerPorId($materiaId)
            : null;

        return [
            'usuario' => $usuario,
            'materia' => $materia,
            'materiaId' => $materiaId,
        ];
    }

    public function index(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materia = $ctx['materia'];
        $materiaId = (int) $ctx['materiaId'];

        $progreso = new Progreso();

        $rendimientoPractica = $materiaId > 0
            ? $progreso->obtenerRendimientoMateria($materiaId, 'practica')
            : [
                'total' => 0,
                'correctas' => 0,
                'porcentaje' => 0,
                'estudiantes' => 0
            ];

        $rendimientoSimulacro = $materiaId > 0
            ? $progreso->obtenerRendimientoMateria($materiaId, 'simulacro')
            : [
                'total' => 0,
                'correctas' => 0,
                'porcentaje' => 0,
                'estudiantes' => 0
            ];

        $estudiantes = $materiaId > 0
            ? $progreso->obtenerEstudiantesMateria($materiaId)
            : [];

        // Agrupar los estudiantes de la materia del profesor por grado
        // y calcular el promedio del rendimiento individual de cada grupo.
        $estudiantesPorGrado = [];

        foreach ($estudiantes as $estudiante) {
            $grado = (int) ($estudiante['grado'] ?? 0);

            if ($grado < 6 || $grado > 11) {
                continue;
            }

            if (!isset($estudiantesPorGrado[$grado])) {
                $estudiantesPorGrado[$grado] = [
                    'grado' => $grado,
                    'estudiantes' => [],
                    'promedio' => 0,
                ];
            }

            $estudiantesPorGrado[$grado]['estudiantes'][] = $estudiante;
        }

        foreach ($estudiantesPorGrado as &$grupo) {
            $cantidad = count($grupo['estudiantes']);

            if ($cantidad === 0) {
                $grupo['promedio'] = 0;
                continue;
            }

            $suma = 0;

            foreach ($grupo['estudiantes'] as $estudiante) {
                $suma += (int) $estudiante['porcentaje_practica'];
            }

            $grupo['promedio'] = (int) round($suma / $cantidad);
        }

        unset($grupo);

        krsort($estudiantesPorGrado);

        $temas = $materiaId > 0
            ? (new Tema())->obtenerPorMateria($materiaId)
            : [];

        $preguntas = $materiaId > 0
            ? (new Pregunta())->obtenerTodas($materiaId)
            : [];

        $simulacros = $materiaId > 0
            ? (new Simulacro())->obtenerPorProfesor(
                (int) $ctx['usuario']['id'],
                $materiaId
            )
            : [];

        $temasRendimiento = $materiaId > 0
            ? $progreso->obtenerRendimientoTemas($materiaId)
            : [];

        $preguntasFalladas = $materiaId > 0
            ? $progreso->obtenerPreguntasMasFalladas($materiaId, 5)
            : [];

        $this->render('dashboard/profesor', [
            'pageTitle' => 'Dashboard del profesor | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $materia,
            'usuario' => $ctx['usuario'],
            'rendimientoPractica' => $rendimientoPractica,
            'rendimientoSimulacro' => $rendimientoSimulacro,
            'estudiantes' => $estudiantes,
            'estudiantesPorGrado' => $estudiantesPorGrado,
            'temas' => $temas,
            'preguntas' => $preguntas,
            'simulacros' => $simulacros,
            'temasRendimiento' => $temasRendimiento,
            'preguntasFalladas' => $preguntasFalladas,
        ]);
    }

    public function estudiantes(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        $estudiantes = $materiaId > 0
            ? (new Progreso())->obtenerEstudiantesMateria($materiaId)
            : [];

        $this->render('profesor/estudiantes', [
            'pageTitle' => 'Mis estudiantes | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'estudiantes' => $estudiantes,
        ]);
    }

    public function estudiante(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        $materiaId = (int) $ctx['materiaId'];

        $usuario = $id > 0
            ? (new Usuario())->buscarPorId($id)
            : null;

        if (
            $usuario === null
            || $usuario['rol'] !== 'estudiante'
            || (int) ($usuario['materia_id'] ?? 0) !== $materiaId
        ) {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'Ese estudiante no pertenece a tu materia.'
            ];

            $this->redirect('profesor/estudiantes');
            return;
        }

        $progreso = new Progreso();

        $porMateriaPractica = $progreso->obtenerPorMateria(
            $id,
            'practica'
        );

        $porMateriaSimulacro = $progreso->obtenerPorMateria(
            $id,
            'simulacro'
        );

        $historial = $progreso->obtenerHistorialSimulacros($id);

        $this->render('profesor/estudiante', [
            'pageTitle' => 'Rendimiento del estudiante | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'usuario' => $usuario,
            'porMateriaPractica' => $porMateriaPractica,
            'porMateriaSimulacro' => $porMateriaSimulacro,
            'historialSimulacros' => $historial,
        ]);
    }

    public function preguntas(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        $preguntas = $materiaId > 0
            ? (new Pregunta())->obtenerTodas($materiaId)
            : [];

        $temas = $materiaId > 0
            ? (new Tema())->obtenerPorMateria($materiaId)
            : [];

        $tiposPregunta = (new TipoPregunta())->obtenerTodos();

        $mensajes = $_SESSION['profesor_mensajes'] ?? [];
        unset($_SESSION['profesor_mensajes']);

        $this->render('profesor/preguntas', [
            'pageTitle' => 'Banco de preguntas | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'preguntas' => $preguntas,
            'temas' => $temas,
            'tiposPregunta' => $tiposPregunta,
            'mensajes' => $mensajes,
        ]);
    }

    public function formularioPregunta(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        if ($materiaId <= 0) {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'Primero debes tener una materia asignada.'
            ];

            $this->redirect('');
            return;
        }

        $id = isset($_GET['id']) && $_GET['id'] !== ''
            ? (int) $_GET['id']
            : null;

        $pregunta = null;

        if ($id !== null) {
            $pregunta = (new Pregunta())->obtenerPorId($id);

            if (
                $pregunta === null
                || (int) $pregunta['materia_id'] !== $materiaId
            ) {
                $_SESSION['profesor_mensajes'] = [
                    'error' => 'La pregunta no pertenece a tu materia o ya no existe.'
                ];

                $this->redirect('profesor/preguntas');
                return;
            }
        }

        $this->render('profesor/pregunta_formulario', [
            'pageTitle' => ($pregunta === null ? 'Nueva pregunta' : 'Editar pregunta') . ' | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'pregunta' => $pregunta,
            'temas' => (new Tema())->obtenerPorMateria($materiaId),
            'tiposPregunta' => (new TipoPregunta())->obtenerTodos(),
            'dificultades' => [
                'facil',
                'media',
                'dificil'
            ],
        ]);
    }

    public function guardarPregunta(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('profesor/preguntas');
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        $id = isset($_POST['id']) && $_POST['id'] !== ''
            ? (int) $_POST['id']
            : null;

        $temaId = (int) ($_POST['tema_id'] ?? 0);
        $tipoPreguntaId = (int) ($_POST['tipo_pregunta_id'] ?? 0);

        $enunciado = trim((string) ($_POST['enunciado'] ?? ''));
        $opcionA = trim((string) ($_POST['opcion_a'] ?? ''));
        $opcionB = trim((string) ($_POST['opcion_b'] ?? ''));
        $opcionC = trim((string) ($_POST['opcion_c'] ?? ''));
        $opcionD = trim((string) ($_POST['opcion_d'] ?? ''));

        $respuestaCorrecta = strtolower(
            trim((string) ($_POST['respuesta_correcta'] ?? ''))
        );

        $explicacion = trim(
            (string) ($_POST['explicacion'] ?? '')
        );

        $dificultad = (string) (
            $_POST['dificultad'] ?? 'media'
        );

        $tema = $temaId > 0
            ? (new Tema())->obtenerPorId($temaId)
            : null;

        $preguntaModel = new Pregunta();

        $preguntaExistente = $id !== null
            ? $preguntaModel->obtenerPorId($id)
            : null;

        $valido =
            $materiaId > 0
            && $tema !== null
            && (int) $tema['materia_id'] === $materiaId
            && $tipoPreguntaId > 0
            && $enunciado !== ''
            && $opcionA !== ''
            && $opcionB !== ''
            && $opcionC !== ''
            && $opcionD !== ''
            && in_array(
                $respuestaCorrecta,
                ['a', 'b', 'c', 'd'],
                true
            )
            && in_array(
                $dificultad,
                ['facil', 'media', 'dificil'],
                true
            )
            && (
                $id === null
                || (
                    $preguntaExistente !== null
                    && (int) $preguntaExistente['materia_id'] === $materiaId
                )
            );

        if (!$valido) {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'Revisa los datos. El tema debe pertenecer a tu materia.'
            ];

            $this->redirect(
                $id !== null
                    ? 'profesor/pregunta?id=' . $id
                    : 'profesor/pregunta'
            );

            return;
        }

        $datos = [
            'profesor_id' => (int) $ctx['usuario']['id'],
            'tema_id' => $temaId,
            'tipo_pregunta_id' => $tipoPreguntaId,
            'enunciado' => $enunciado,
            'opcion_a' => $opcionA,
            'opcion_b' => $opcionB,
            'opcion_c' => $opcionC,
            'opcion_d' => $opcionD,
            'respuesta_correcta' => $respuestaCorrecta,
            'explicacion' => $explicacion,
            'dificultad' => $dificultad,
        ];

        if ($id === null) {
            $preguntaModel->crear($datos);

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Pregunta creada correctamente.'
            ];
        } else {
            $preguntaModel->actualizar($id, $datos);

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Pregunta actualizada correctamente.'
            ];
        }

        $this->redirect('profesor/preguntas');
    }

    public function eliminarPregunta(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('profesor/preguntas');
            return;
        }

        $id = (int) ($_POST['pregunta_id'] ?? 0);

        $pregunta = $id > 0
            ? (new Pregunta())->obtenerPorId($id)
            : null;

        if (
            $pregunta !== null
            && (int) $pregunta['materia_id'] === (int) $ctx['materiaId']
        ) {
            (new Pregunta())->eliminar($id);

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Pregunta eliminada correctamente.'
            ];
        } else {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'No puedes eliminar una pregunta de otra materia.'
            ];
        }

        $this->redirect('profesor/preguntas');
    }

    public function simulacros(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        $simulacros = $materiaId > 0
            ? (new Simulacro())->obtenerPorProfesor(
                (int) $ctx['usuario']['id'],
                $materiaId
            )
            : [];

        $mensajes = $_SESSION['profesor_mensajes'] ?? [];
        unset($_SESSION['profesor_mensajes']);

        $this->render('profesor/simulacros', [
            'pageTitle' => 'Mis simulacros | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'simulacros' => $simulacros,
            'mensajes' => $mensajes,
        ]);
    }

    public function formularioSimulacro(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        $materiaId = (int) $ctx['materiaId'];

        if ($materiaId <= 0) {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'Primero debes tener una materia asignada.'
            ];

            $this->redirect('');
            return;
        }

        $id = isset($_GET['id']) && $_GET['id'] !== ''
            ? (int) $_GET['id']
            : null;

        $simulacroModel = new Simulacro();
        $simulacro = null;
        $preguntaIds = [];

        if ($id !== null) {
            $simulacro = $simulacroModel->obtenerPorId($id);

            if (
                $simulacro === null
                || (int) ($simulacro['profesor_id'] ?? 0)
                !== (int) $ctx['usuario']['id']
                || (int) ($simulacro['materia_id'] ?? 0)
                !== $materiaId
            ) {
                $_SESSION['profesor_mensajes'] = [
                    'error' => 'Ese simulacro no pertenece a tu materia.'
                ];

                $this->redirect('profesor/simulacros');
                return;
            }

            $preguntaIds = $simulacroModel->obtenerPreguntaIds($id);
        }

        $this->render('profesor/simulacro_formulario', [
            'pageTitle' => ($simulacro === null ? 'Nuevo simulacro' : 'Editar simulacro') . ' | ProgressICFES',
            'activePage' => 'inicio',
            'materia' => $ctx['materia'],
            'simulacro' => $simulacro,
            'preguntas' => (new Pregunta())->obtenerTodas($materiaId),
            'preguntaIds' => $preguntaIds,
        ]);
    }

    public function guardarSimulacro(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('profesor/simulacros');
            return;
        }

        $profesorId = (int) $ctx['usuario']['id'];
        $materiaId = (int) $ctx['materiaId'];

        $id = isset($_POST['id']) && $_POST['id'] !== ''
            ? (int) $_POST['id']
            : null;

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $tiempo = (int) ($_POST['tiempo_limite_min'] ?? 0);

        $preguntaIds = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        (array) ($_POST['preguntas'] ?? [])
                    ),
                    static fn(int $v): bool => $v > 0
                )
            )
        );

        $preguntasDisponibles = (new Pregunta())->obtenerTodas(
            $materiaId
        );

        $permitidas = array_map(
            static fn(array $p): int => (int) $p['id'],
            $preguntasDisponibles
        );

        $todasPertenecen =
            count(array_diff($preguntaIds, $permitidas)) === 0;

        $simulacroModel = new Simulacro();

        $existente = $id !== null
            ? $simulacroModel->obtenerPorId($id)
            : null;

        $propio = $existente !== null
            && (int) ($existente['profesor_id'] ?? 0) === $profesorId
            && (int) ($existente['materia_id'] ?? 0) === $materiaId;

        if (
            $titulo === ''
            || $tiempo <= 0
            || $preguntaIds === []
            || !$todasPertenecen
            || ($id !== null && !$propio)
        ) {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'Completa el título, el tiempo y selecciona al menos una pregunta de tu materia.'
            ];

            $this->redirect(
                $id !== null
                    ? 'profesor/simulacro?id=' . $id
                    : 'profesor/simulacro'
            );

            return;
        }

        if ($id === null) {
            $id = $simulacroModel->crear(
                $profesorId,
                $titulo,
                'por_materia',
                $materiaId,
                $tiempo
            );

            $simulacroModel->reemplazarPreguntas(
                $id,
                $preguntaIds
            );

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Simulacro creado correctamente.'
            ];
        } else {
            $simulacroModel->actualizar(
                $id,
                $titulo,
                'por_materia',
                $materiaId,
                $tiempo
            );

            $simulacroModel->reemplazarPreguntas(
                $id,
                $preguntaIds
            );

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Simulacro actualizado correctamente.'
            ];
        }

        $this->redirect('profesor/simulacros');
    }

    public function eliminarSimulacro(): void
    {
        $ctx = $this->contexto();

        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('profesor/simulacros');
            return;
        }

        $id = (int) ($_POST['simulacro_id'] ?? 0);

        $simulacro = $id > 0
            ? (new Simulacro())->obtenerPorId($id)
            : null;

        if (
            $simulacro !== null
            && (int) ($simulacro['profesor_id'] ?? 0)
            === (int) $ctx['usuario']['id']
            && (int) ($simulacro['materia_id'] ?? 0)
            === (int) $ctx['materiaId']
        ) {
            (new Simulacro())->eliminar($id);

            $_SESSION['profesor_mensajes'] = [
                'exito' => 'Simulacro eliminado correctamente.'
            ];
        } else {
            $_SESSION['profesor_mensajes'] = [
                'error' => 'No puedes eliminar ese simulacro.'
            ];
        }

        $this->redirect('profesor/simulacros');
    }
}
