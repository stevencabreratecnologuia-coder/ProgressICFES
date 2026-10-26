<?php

declare(strict_types=1);

class AdminController extends Controller
{
    private const ROLES_PERMITIDOS = ['admin'];
    private const DIFICULTADES = ['facil', 'media', 'dificil'];

    // Panel principal: estadísticas generales.
    public function index(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $this->redirect('practica/admin');
    }

    // Página consolidada de gestión de Práctica: materias, temas, tipos de pregunta y banco de preguntas.
    // Un profesor solo ve/gestiona sus propias preguntas; materias/temas/tipos son de solo lectura para él.
    public function gestionPractica(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        $esAdmin = $this->esAdmin();

        // Un profesor solo debe ver preguntas de SU materia actual, aunque
        // haya creado preguntas de otra materia en el pasado (p. ej. antes
        // de que se le asignara materia, o si cambió de materia).
        $miMateriaId = null;
        if (!$esAdmin) {
            $miMateriaId = (int) ((new Usuario())->buscarPorId((int) $this->currentUser()['id'])['materia_id'] ?? 0);
            $miMateriaId = $miMateriaId > 0 ? $miMateriaId : -1; // -1 = sin materia -> no debe ver ninguna
        }

        $materias = (new Materia())->obtenerTodas();
        $temas = (new Tema())->obtenerTodosAgrupados();
        $tiposPregunta = (new TipoPregunta())->obtenerTodos();
        // Se filtra SOLO por materia (no por profesor_id), para que un profesor
        // vea automáticamente todas las preguntas de su materia, sin importar
        // quién las haya creado. Así, si se crea un profesor nuevo, ve el banco
        // de su materia de inmediato, sin necesidad de reasignar nada.
        $preguntas = (new Pregunta())->obtenerTodas($miMateriaId);

        $mensajes = $_SESSION['admin_mensajes'] ?? [];
        unset($_SESSION['admin_mensajes']);

        $this->render('Practica/admin', [
            'pageTitle' => 'Gestión de Práctica | ProgressICFES',
            'activePage' => 'practica',
            'esAdmin' => $esAdmin,
            'materias' => $materias,
            'temas' => $temas,
            'tiposPregunta' => $tiposPregunta,
            'preguntas' => $preguntas,
            'mensajes' => $mensajes,
        ]);
    }

    // --- Usuarios ---

    public function usuarios(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $usuarioModel = new Usuario();

        $usuarios = $usuarioModel->obtenerTodos();

        $solicitudesPendientes =
            $usuarioModel->obtenerSolicitudesProfesorPendientes();

        $materias = (new Materia())->obtenerTodas();

        $mensajes = $_SESSION['admin_mensajes'] ?? [];

        unset($_SESSION['admin_mensajes']);

        $this->render('admin/usuarios', [
            'pageTitle' => 'Usuarios | ProgressICFES',
            'activePage' => 'usuarios',
            'usuarios' => $usuarios,
            'solicitudesPendientes' => $solicitudesPendientes,
            'materias' => $materias,
            'usuarioActualId' => $this->currentUser()['id'],
            'mensajes' => $mensajes,
        ]);
    }

    public function aprobarSolicitudProfesor(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('usuarios');
            return;
        }

        $usuarioId = (int) ($_POST['usuario_id'] ?? 0);

        if ($usuarioId <= 0) {
            $_SESSION['admin_mensajes'] = [
                'error' => 'Solicitud no válida.'
            ];

            $this->redirect('usuarios');
            return;
        }

        $usuarioModel = new Usuario();

        if ($usuarioModel->aprobarSolicitudProfesor($usuarioId)) {
            $_SESSION['admin_mensajes'] = [
                'exito' =>
                'La solicitud del profesor fue aprobada correctamente.'
            ];
        } else {
            $_SESSION['admin_mensajes'] = [
                'error' =>
                'No fue posible aprobar la solicitud.'
            ];
        }

        $this->redirect('usuarios');
    }


    public function rechazarSolicitudProfesor(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('usuarios');
            return;
        }

        $usuarioId = (int) ($_POST['usuario_id'] ?? 0);

        if ($usuarioId <= 0) {
            $_SESSION['admin_mensajes'] = [
                'error' => 'Solicitud no válida.'
            ];

            $this->redirect('usuarios');
            return;
        }

        $usuarioModel = new Usuario();

        if ($usuarioModel->rechazarSolicitudProfesor($usuarioId)) {
            $_SESSION['admin_mensajes'] = [
                'exito' =>
                'La solicitud del profesor fue denegada.'
            ];
        } else {
            $_SESSION['admin_mensajes'] = [
                'error' =>
                'No fue posible denegar la solicitud.'
            ];
        }

        $this->redirect('usuarios');
    }

    // Asigna la materia que dicta un profesor (solo aplica a usuarios con rol = profesor).
    public function actualizarMateriaUsuario(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('usuarios');
            return;
        }

        $id = (int) ($_POST['usuario_id'] ?? 0);
        $materiaId = isset($_POST['materia_id']) && $_POST['materia_id'] !== ''
            ? (int) $_POST['materia_id']
            : null;

        $usuario = $id > 0 ? (new Usuario())->buscarPorId($id) : null;

        if ($usuario !== null && $usuario['rol'] === 'profesor') {
            (new Usuario())->actualizarMateria($id, $materiaId);
            $_SESSION['admin_mensajes'] = ['exito' => 'Materia asignada correctamente.'];
        } else {
            $_SESSION['admin_mensajes'] = ['error' => 'Solo puedes asignar una materia a un profesor.'];
        }

        $this->redirect('usuarios');
    }

    public function cambiarRolUsuario(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('usuarios');
            return;
        }

        $id = (int) ($_POST['usuario_id'] ?? 0);
        $rol = (string) ($_POST['rol'] ?? '');

        if ($id > 0 && in_array($rol, ['estudiante', 'profesor', 'admin'], true)) {
            (new Usuario())->actualizarRol($id, $rol);
            $_SESSION['admin_mensajes'] = ['exito' => 'Rol actualizado correctamente.'];
        } else {
            $_SESSION['admin_mensajes'] = ['error' => 'No se pudo actualizar el rol.'];
        }

        $this->redirect('usuarios');
    }

    public function eliminarUsuario(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('usuarios');
            return;
        }

        $id = (int) ($_POST['usuario_id'] ?? 0);
        $usuarioActualId = $this->currentUser()['id'];

        if ($id > 0 && $id !== $usuarioActualId) {
            (new Usuario())->eliminar($id);
            $_SESSION['admin_mensajes'] = ['exito' => 'Usuario eliminado.'];
        } else {
            $_SESSION['admin_mensajes'] = ['error' => 'No puedes eliminar tu propia cuenta desde aquí.'];
        }

        $this->redirect('usuarios');
    }

    // --- Preguntas ---

    public function preguntas(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $materiaId = isset($_GET['materia_id']) && $_GET['materia_id'] !== '' ? (int) $_GET['materia_id'] : null;

        $preguntas = (new Pregunta())->obtenerTodas($materiaId);
        $materias = (new Materia())->obtenerTodas();

        $mensajes = $_SESSION['admin_mensajes'] ?? [];
        unset($_SESSION['admin_mensajes']);

        $this->render('admin/preguntas', [
            'pageTitle' => 'Preguntas | ProgressICFES',
            'activePage' => 'practica',
            'preguntas' => $preguntas,
            'materias' => $materias,
            'materiaSeleccionada' => $materiaId,
            'mensajes' => $mensajes,
        ]);
    }

    // Muestra el formulario, vacío (crear) o con datos cargados (editar, vía ?id=).
    public function formularioPregunta(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        $esAdmin = $this->esAdmin();
        $id = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : null;
        $pregunta = null;

        $miMateriaId = $esAdmin
            ? 0
            : (int) ((new Usuario())->buscarPorId((int) $this->currentUser()['id'])['materia_id'] ?? 0);

        if ($id !== null) {
            $pregunta = (new Pregunta())->obtenerPorId($id);

            if ($pregunta === null) {
                $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta ya no existe.'];
                $this->redirect('practica/admin');
                return;
            }

            // Un profesor puede ver/editar cualquier pregunta de SU materia,
            // sin importar quién la haya creado (ya no se exige que sea el autor).
            if (!$esAdmin && (int) $pregunta['materia_id'] !== $miMateriaId) {
                $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta no pertenece a tu materia.'];
                $this->redirect('practica/admin');
                return;
            }
        }

        if ($esAdmin) {
            $materias = (new Materia())->obtenerTodas();
            $temas = (new Tema())->obtenerTodosAgrupados();
        } else {
            $materias = $miMateriaId > 0 ? array_filter(
                (new Materia())->obtenerTodas(),
                static fn(array $m): bool => (int) $m['id'] === $miMateriaId
            ) : [];
            $temas = $miMateriaId > 0 ? (new Tema())->obtenerPorMateria($miMateriaId) : [];
        }

        $tiposPregunta = (new TipoPregunta())->obtenerTodos();

        $this->render('admin/pregunta_formulario', [
            'pageTitle' => ($pregunta === null ? 'Nueva pregunta' : 'Editar pregunta') . ' | ProgressICFES',
            'activePage' => 'practica',
            'pregunta' => $pregunta,
            'materias' => $materias,
            'temas' => $temas,
            'dificultades' => self::DIFICULTADES,
            'tiposPregunta' => $tiposPregunta,
            'esAdmin' => $esAdmin,
        ]);
    }

    // Recibe el formulario de crear o editar (se distingue por si viene "id" o no).
    public function guardarPregunta(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica/admin');
            return;
        }

        $esAdmin = $this->esAdmin();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
        $preguntaModel = new Pregunta();

        if ($id !== null) {
            $preguntaExistente = $preguntaModel->obtenerPorId($id);

            if ($preguntaExistente === null) {
                $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta ya no existe.'];
                $this->redirect('practica/admin');
                return;
            }

            $miMateriaIdActual = (int) ((new Usuario())->buscarPorId((int) $this->currentUser()['id'])['materia_id'] ?? 0);

            // Un profesor puede editar cualquier pregunta de SU materia,
            // sin importar quién la haya creado.
            if (!$esAdmin && (int) $preguntaExistente['materia_id'] !== $miMateriaIdActual) {
                $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta no pertenece a tu materia.'];
                $this->redirect('practica/admin');
                return;
            }
        }

        $temaId = (int) ($_POST['tema_id'] ?? 0);
        $tipoPreguntaId = (int) ($_POST['tipo_pregunta_id'] ?? 0);
        $enunciado = trim((string) ($_POST['enunciado'] ?? ''));
        $opcionA = trim((string) ($_POST['opcion_a'] ?? ''));
        $opcionB = trim((string) ($_POST['opcion_b'] ?? ''));
        $opcionC = trim((string) ($_POST['opcion_c'] ?? ''));
        $opcionD = trim((string) ($_POST['opcion_d'] ?? ''));
        $respuestaCorrecta = strtolower(trim((string) ($_POST['respuesta_correcta'] ?? '')));
        $explicacion = trim((string) ($_POST['explicacion'] ?? ''));
        $dificultad = (string) ($_POST['dificultad'] ?? 'media');

        // Un profesor solo puede guardar preguntas en un tema de su propia materia,
        // sin importar lo que llegue en el formulario (defensa contra manipular el POST).
        if (!$esAdmin) {
            $miMateriaId = (int) ((new Usuario())->buscarPorId((int) $this->currentUser()['id'])['materia_id'] ?? 0);
            $tema = $temaId > 0 ? (new Tema())->obtenerPorId($temaId) : null;

            if ($miMateriaId === 0 || $tema === null || (int) $tema['materia_id'] !== $miMateriaId) {
                $_SESSION['admin_mensajes'] = ['error' => 'Solo puedes guardar preguntas en tu propia materia.'];
                $this->redirect('practica/admin');
                return;
            }
        }

        $formularioValido = $temaId > 0
            && $tipoPreguntaId > 0
            && $enunciado !== ''
            && $opcionA !== ''
            && $opcionB !== ''
            && $opcionC !== ''
            && $opcionD !== ''
            && in_array($respuestaCorrecta, ['a', 'b', 'c', 'd'], true)
            && in_array($dificultad, self::DIFICULTADES, true);

        if (!$formularioValido) {
            $_SESSION['admin_mensajes'] = ['error' => 'Revisa el formulario: falta algún campo obligatorio o la respuesta correcta no es válida.'];
            $this->redirect($id !== null ? 'practica/admin/pregunta?id=' . $id : 'practica/admin/pregunta');
            return;
        }

        $datos = [
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
            $datos['profesor_id'] = $this->currentUser()['id'];
            $preguntaModel->crear($datos);
            $_SESSION['admin_mensajes'] = ['exito' => 'Pregunta creada correctamente.'];
        } else {
            $preguntaModel->actualizar($id, $datos);
            $_SESSION['admin_mensajes'] = ['exito' => 'Pregunta actualizada correctamente.'];
        }

        $this->redirect('practica/admin');
    }

    public function eliminarPregunta(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica/admin');
            return;
        }

        $id = (int) ($_POST['pregunta_id'] ?? 0);
        $preguntaModel = new Pregunta();
        $pregunta = $id > 0 ? $preguntaModel->obtenerPorId($id) : null;

        if ($pregunta === null) {
            $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta ya no existe.'];
            $this->redirect('practica/admin');
            return;
        }

        if (!$this->esAdmin()) {
            $miMateriaIdActual = (int) ((new Usuario())->buscarPorId((int) $this->currentUser()['id'])['materia_id'] ?? 0);

            // Un profesor puede eliminar cualquier pregunta de SU materia,
            // sin importar quién la haya creado.
            if ((int) $pregunta['materia_id'] !== $miMateriaIdActual) {
                $_SESSION['admin_mensajes'] = ['error' => 'Esa pregunta no pertenece a tu materia.'];
                $this->redirect('practica/admin');
                return;
            }
        }

        $preguntaModel->eliminar($id);
        $_SESSION['admin_mensajes'] = ['exito' => 'Pregunta eliminada.'];
        $this->redirect('practica/admin');
    }

    // --- Materias y temas ---

    public function materias(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $materias = (new Materia())->obtenerTodas();
        $temas = (new Tema())->obtenerTodosAgrupados();

        $mensajes = $_SESSION['admin_mensajes'] ?? [];
        unset($_SESSION['admin_mensajes']);

        $this->render('admin/materias', [
            'pageTitle' => 'Materias y temas | ProgressICFES',
            'activePage' => 'practica',
            'materias' => $materias,
            'temas' => $temas,
            'mensajes' => $mensajes,
        ]);
    }

    // Página propia de una materia: sus temas y sus preguntas, con formularios para agregar.
    public function detalleMateria(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $materia = $id > 0 ? (new Materia())->obtenerPorId($id) : null;

        if ($materia === null) {
            $_SESSION['admin_mensajes'] = ['error' => 'Esa materia ya no existe.'];
            $this->redirect('practica');
            return;
        }

        $temas = (new Tema())->obtenerPorMateria($id);
        $preguntas = (new Pregunta())->obtenerTodas($id);

        $mensajes = $_SESSION['admin_mensajes'] ?? [];
        unset($_SESSION['admin_mensajes']);

        $this->render('admin/materia_detalle', [
            'pageTitle' => $materia['nombre'] . ' | ProgressICFES',
            'activePage' => 'practica',
            'materia' => $materia,
            'temas' => $temas,
            'preguntas' => $preguntas,
            'mensajes' => $mensajes,
        ]);
    }

    public function crearMateria(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($nombre === '') {
            $_SESSION['admin_mensajes'] = ['error' => 'El nombre de la materia no puede estar vacío.'];
            $this->redirect('practica');
            return;
        }

        (new Materia())->crear($nombre, $descripcion);
        $_SESSION['admin_mensajes'] = ['exito' => 'Materia creada correctamente.'];
        $this->redirect('practica');
    }

    public function crearTema(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $materiaId = (int) ($_POST['materia_id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));

        if ($materiaId <= 0 || $nombre === '') {
            $_SESSION['admin_mensajes'] = ['error' => 'Selecciona una materia y escribe un nombre para el tema.'];
            $this->redirect('practica');
            return;
        }

        (new Tema())->crear($materiaId, $nombre);
        $_SESSION['admin_mensajes'] = ['exito' => 'Tema creado correctamente.'];
        $this->redirect('practica' . '?materia_id=' . $materiaId);
    }

    public function crearTipoPregunta(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($nombre === '') {
            $_SESSION['admin_mensajes'] = ['error' => 'El nombre del tipo de pregunta no puede estar vacío.'];
            $this->redirect('practica');
            return;
        }

        try {
            (new TipoPregunta())->crear($nombre, $descripcion);
            $_SESSION['admin_mensajes'] = ['exito' => 'Tipo de pregunta creado correctamente.'];
        } catch (Throwable $e) {
            $_SESSION['admin_mensajes'] = ['error' => 'No se pudo crear el tipo. Puede que ya exista.'];
        }

        $this->redirect('practica');
    }

    public function eliminarTipoPregunta(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('practica');
            return;
        }

        $id = (int) ($_POST['tipo_pregunta_id'] ?? 0);

        if ($id > 0) {
            try {
                (new TipoPregunta())->eliminar($id);
                $_SESSION['admin_mensajes'] = ['exito' => 'Tipo de pregunta eliminado.'];
            } catch (Throwable $e) {
                $_SESSION['admin_mensajes'] = ['error' => 'No puedes eliminar un tipo que tenga preguntas asociadas.'];
            }
        }

        $this->redirect('practica');
    }

    // Muestra el progreso de un usuario puntual (mismo formato que ve el propio estudiante).
    public function detalleUsuario(): void
    {
        if (!$this->esAdmin()) {
            $this->redirect('');
            return;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $usuario = $id > 0 ? (new Usuario())->buscarPorId($id) : null;

        if ($usuario === null) {
            $_SESSION['admin_mensajes'] = ['error' => 'Ese usuario ya no existe.'];
            $this->redirect('usuarios');
            return;
        }

        $progresoModel = new Progreso();
        $porMateriaPractica = $this->aplicarColores($progresoModel->obtenerPorMateria($id, 'practica'));
        $porMateriaSimulacro = $this->aplicarColores($progresoModel->obtenerPorMateria($id, 'simulacro'));
        $historialSimulacros = $progresoModel->obtenerHistorialSimulacros($id);
        $resumen = $this->calcularResumen($porMateriaPractica, $historialSimulacros);

        $this->render('admin/usuario_detalle', [
            'pageTitle' => 'Usuario | ProgressICFES',
            'activePage' => 'usuarios',
            'usuario' => $usuario,
            'porMateriaPractica' => $porMateriaPractica,
            'porMateriaSimulacro' => $porMateriaSimulacro,
            'historialSimulacros' => $historialSimulacros,
            'resumen' => $resumen,
        ]);
    }

    // Colores fijos por materia (mismos usados en Progreso), para no depender
    // del texto del nombre (que a veces viene con tildes dañadas en la BD).
    /**
     * @param array<int, array{materia_id:int, materia:string, total:int, correctas:int, porcentaje:int}> $filas
     * @return array<int, array{materia_id:int, materia:string, total:int, correctas:int, porcentaje:int, clase_color:string}>
     */
    private function aplicarColores(array $filas): array
    {
        $nombresYColores = [
            1 => ['clase' => 'is-matematicas', 'nombre' => 'Matemáticas'],
            2 => ['clase' => 'is-lectura', 'nombre' => 'Lectura Crítica'],
            3 => ['clase' => 'is-ciencias', 'nombre' => 'Ciencias Naturales'],
            4 => ['clase' => 'is-sociales', 'nombre' => 'Sociales y Ciudadanas'],
            5 => ['clase' => 'is-ingles', 'nombre' => 'Inglés'],
        ];

        foreach ($filas as &$fila) {
            $info = $nombresYColores[$fila['materia_id']] ?? null;
            $fila['clase_color'] = $info['clase'] ?? '';
            $fila['materia'] = $info['nombre'] ?? $fila['materia'];
        }

        return $filas;
    }

    /**
     * @param array<int, array{total:int, correctas:int}> $porMateriaPractica
     * @param array<int, array{puntaje_estimado_icfes:int}> $historialSimulacros
     * @return array{preguntas_practica:int, porcentaje_practica:int, intentos_simulacro:int, mejor_puntaje_icfes:int}
     */
    private function calcularResumen(array $porMateriaPractica, array $historialSimulacros): array
    {
        $totalPreguntasPractica = array_sum(array_column($porMateriaPractica, 'total'));
        $totalCorrectasPractica = array_sum(array_column($porMateriaPractica, 'correctas'));

        $mejorPuntaje = 0;
        foreach ($historialSimulacros as $intento) {
            $mejorPuntaje = max($mejorPuntaje, (int) $intento['puntaje_estimado_icfes']);
        }

        return [
            'preguntas_practica' => $totalPreguntasPractica,
            'porcentaje_practica' => $totalPreguntasPractica > 0
                ? (int) round(($totalCorrectasPractica / $totalPreguntasPractica) * 100)
                : 0,
            'intentos_simulacro' => count($historialSimulacros),
            'mejor_puntaje_icfes' => $mejorPuntaje,
        ];
    }

    // --- Simulacros ---

    public function simulacrosAdmin(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        $esAdmin = $this->esAdmin();
        $profesorId = $esAdmin ? null : (int) $this->currentUser()['id'];

        $simulacros = (new Simulacro())->obtenerTodosAdmin($profesorId);

        $mensajes = $_SESSION['admin_mensajes'] ?? [];
        unset($_SESSION['admin_mensajes']);

        $this->render('simulacro/admin', [
            'pageTitle' => 'Simulacros | ProgressICFES',
            'activePage' => 'simulacros',
            'simulacros' => $simulacros,
            'mensajes' => $mensajes,
        ]);
    }

    public function formularioSimulacro(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        $esAdmin = $this->esAdmin();
        $miId = (int) $this->currentUser()['id'];

        $id = isset($_GET['id']) && $_GET['id'] !== '' ? (int) $_GET['id'] : null;
        $simulacroModel = new Simulacro();
        $simulacro = null;
        $preguntaIdsSeleccionadas = [];

        if ($id !== null) {
            $simulacro = $simulacroModel->obtenerPorId($id);

            if ($simulacro === null) {
                $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro ya no existe.'];
                $this->redirect('simulacros/admin');
                return;
            }

            if (!$esAdmin && (int) $simulacro['profesor_id'] !== $miId) {
                $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro no te pertenece.'];
                $this->redirect('simulacros/admin');
                return;
            }

            $preguntaIdsSeleccionadas = $simulacroModel->obtenerPreguntaIds($id);
        }

        $miMateriaId = $esAdmin
            ? 0
            : (int) ((new Usuario())->buscarPorId($miId)['materia_id'] ?? 0);

        $materias = $esAdmin
            ? (new Materia())->obtenerTodas()
            : array_filter(
                (new Materia())->obtenerTodas(),
                static fn(array $m): bool => (int) $m['id'] === $miMateriaId
            );

        $materiaFiltro = $esAdmin
            ? (isset($_GET['materia_id']) && $_GET['materia_id'] !== ''
                ? (int) $_GET['materia_id']
                : (int) ($simulacro['materia_id'] ?? ($materias[0]['id'] ?? 0)))
            : $miMateriaId;

        $profesorIdFiltro = $esAdmin ? null : $miId;
        $preguntasDisponibles = $materiaFiltro > 0 ? (new Pregunta())->obtenerTodas($materiaFiltro, $profesorIdFiltro) : [];

        $this->render('admin/simulacro_formulario', [
            'pageTitle' => ($simulacro === null ? 'Nuevo simulacro' : 'Editar simulacro') . ' | ProgressICFES',
            'activePage' => 'simulacros',
            'simulacro' => $simulacro,
            'materias' => $materias,
            'materiaFiltro' => $materiaFiltro,
            'preguntasDisponibles' => $preguntasDisponibles,
            'preguntaIdsSeleccionadas' => $preguntaIdsSeleccionadas,
            'esAdmin' => $esAdmin,
        ]);
    }

    public function guardarSimulacro(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('simulacros/admin');
            return;
        }

        $esAdmin = $this->esAdmin();
        $miId = (int) $this->currentUser()['id'];

        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
        $simulacroModel = new Simulacro();

        if ($id !== null) {
            $simulacroExistente = $simulacroModel->obtenerPorId($id);

            if ($simulacroExistente === null) {
                $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro ya no existe.'];
                $this->redirect('simulacros/admin');
                return;
            }

            if (!$esAdmin && (int) $simulacroExistente['profesor_id'] !== $miId) {
                $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro no te pertenece.'];
                $this->redirect('simulacros/admin');
                return;
            }
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $tipo = (string) ($_POST['tipo'] ?? 'por_materia');
        $materiaId = isset($_POST['materia_id']) && $_POST['materia_id'] !== '' ? (int) $_POST['materia_id'] : null;
        $tiempoLimiteMin = (int) ($_POST['tiempo_limite_min'] ?? 0);
        $preguntasSeleccionadas = array_map('intval', (array) ($_POST['preguntas'] ?? []));

        // Un profesor solo puede crear/editar simulacros de su propia materia,
        // con preguntas de esa misma materia (defensa contra manipular el POST).
        if (!$esAdmin) {
            $miMateriaId = (int) ((new Usuario())->buscarPorId($miId)['materia_id'] ?? 0);

            if ($miMateriaId === 0) {
                $_SESSION['admin_mensajes'] = ['error' => 'No tienes una materia asignada.'];
                $this->redirect('simulacros/admin');
                return;
            }

            $materiaId = $miMateriaId;

            $preguntasValidas = (new Pregunta())->obtenerTodas($miMateriaId, $miId);
            $idsValidos = array_map(static fn(array $p): int => (int) $p['id'], $preguntasValidas);
            $preguntasSeleccionadas = array_values(array_intersect($preguntasSeleccionadas, $idsValidos));
        }

        $formularioValido = $titulo !== ''
            && in_array($tipo, ['completo', 'por_materia'], true)
            && $tiempoLimiteMin > 0
            && $preguntasSeleccionadas !== [];

        if (!$formularioValido) {
            $_SESSION['admin_mensajes'] = ['error' => 'Completa el título, el tiempo límite, y marca al menos una pregunta.'];
            $this->redirect($id !== null ? 'simulacros/formulario?id=' . $id : 'simulacros/formulario');
            return;
        }

        if ($id === null) {
            $nuevoId = $simulacroModel->crear($miId, $titulo, $tipo, $materiaId, $tiempoLimiteMin);
            $simulacroModel->reemplazarPreguntas($nuevoId, $preguntasSeleccionadas);
            $_SESSION['admin_mensajes'] = ['exito' => 'Simulacro creado correctamente.'];
        } else {
            $simulacroModel->actualizar($id, $titulo, $tipo, $materiaId, $tiempoLimiteMin);
            $simulacroModel->reemplazarPreguntas($id, $preguntasSeleccionadas);
            $_SESSION['admin_mensajes'] = ['exito' => 'Simulacro actualizado correctamente.'];
        }

        $this->redirect('simulacros/admin');
    }

    public function eliminarSimulacro(): void
    {
        if (!$this->esAdminOProfesor()) {
            $this->redirect('');
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('simulacros/admin');
            return;
        }

        $id = (int) ($_POST['simulacro_id'] ?? 0);
        $simulacroModel = new Simulacro();
        $simulacro = $id > 0 ? $simulacroModel->obtenerPorId($id) : null;

        if ($simulacro === null) {
            $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro ya no existe.'];
            $this->redirect('simulacros/admin');
            return;
        }

        if (!$this->esAdmin() && (int) $simulacro['profesor_id'] !== (int) $this->currentUser()['id']) {
            $_SESSION['admin_mensajes'] = ['error' => 'Ese simulacro no te pertenece.'];
            $this->redirect('simulacros/admin');
            return;
        }

        $simulacroModel->eliminar($id);
        $_SESSION['admin_mensajes'] = ['exito' => 'Simulacro eliminado.'];
        $this->redirect('simulacros/admin');
    }

    private function esAdminOProfesor(): bool
    {
        $this->requireAuthentication();

        $rol = $this->currentUser()['rol'] ?? '';

        return in_array($rol, ['admin', 'profesor'], true);
    }

    private function esAdmin(): bool
    {
        $this->requireAuthentication();

        $rol = $this->currentUser()['rol'] ?? '';

        return in_array($rol, self::ROLES_PERMITIDOS, true);
    }
}
