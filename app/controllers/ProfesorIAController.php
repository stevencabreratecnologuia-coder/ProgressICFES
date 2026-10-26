<?php

declare(strict_types=1);

class ProfesorIAController extends Controller
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

        $materiaId = (int) ($usuario['materia_id'] ?? 0);
        $materia = $materiaId > 0 ? (new Materia())->obtenerPorId($materiaId) : null;

        return [
            'usuario' => $usuario,
            'materiaId' => $materiaId,
            'materia' => $materia,
        ];
    }

    public function index(): void
    {
        $this->requireAuthentication();
        $this->redirect('chat');
    }

    public function generar(): void
    {
        $ctx = $this->contexto();
        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['profesor_ia_error'] = 'La sesión del formulario expiró. Inténtalo nuevamente.';
            $this->redirect('chat');
            return;
        }

        try {
            $ia = new ProfesorIAService();
            $resultado = $ia->generar(
                (int) $ctx['usuario']['id'],
                [
                    'modo' => (string) ($_POST['modo'] ?? 'simulacro'),
                    'grado' => (int) ($_POST['grado'] ?? 0),
                    'tema_id' => (int) ($_POST['tema_id'] ?? 0),
                    'dificultad' => (string) ($_POST['dificultad'] ?? 'auto'),
                    'tipo_simulacro' => (string) ($_POST['tipo_simulacro'] ?? 'refuerzo'),
                    'cantidad' => (int) ($_POST['cantidad'] ?? 10),
                    'instruccion' => (string) ($_POST['instruccion'] ?? ''),
                ]
            );

            $_SESSION['profesor_ia_borrador'] = $resultado;
            $_SESSION['profesor_ia_exito'] = 'ProgressIA generó un borrador. Revísalo antes de publicarlo.';
        } catch (Throwable $e) {
            $_SESSION['profesor_ia_error'] = $e->getMessage();
        }

        $this->redirect('chat');
    }

    public function publicar(): void
    {
        $ctx = $this->contexto();
        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['profesor_ia_error'] = 'La sesión del formulario expiró. Inténtalo nuevamente.';
            $this->redirect('chat');
            return;
        }

        $borrador = $_SESSION['profesor_ia_borrador'] ?? null;

        if (!is_array($borrador) || !isset($borrador['preguntas']) || !is_array($borrador['preguntas'])) {
            $_SESSION['profesor_ia_error'] = 'No hay un borrador de ProgressIA para publicar.';
            $this->redirect('chat');
            return;
        }

        try {
            $preguntaModel = new Pregunta();
            $temaModel = new Tema();
            $tipoModel = new TipoPregunta();
            $simulacroModel = new Simulacro();

            $profesorId = (int) $ctx['usuario']['id'];
            $materiaId = (int) $ctx['materiaId'];
            $modo = (string) ($borrador['_meta']['modo'] ?? 'simulacro');

            $temasPermitidos = [];
            foreach ($temaModel->obtenerPorMateria($materiaId) as $tema) {
                $temasPermitidos[(int) $tema['id']] = true;
            }

            $tiposPermitidos = [];
            foreach ($tipoModel->obtenerTodos() as $tipo) {
                $tiposPermitidos[(int) $tipo['id']] = true;
            }

            $preguntasPublicadas = [];

            foreach ($borrador['preguntas'] as $indice => $pregunta) {
                $temaId = (int) ($pregunta['tema_id'] ?? 0);
                $tipoPreguntaId = (int) ($pregunta['tipo_pregunta_id'] ?? 0);
                $respuestaCorrecta = strtolower(trim((string) ($pregunta['respuesta_correcta'] ?? '')));
                $dificultad = strtolower(trim((string) ($pregunta['dificultad'] ?? 'media')));

                if (!isset($temasPermitidos[$temaId]) || !isset($tiposPermitidos[$tipoPreguntaId])) {
                    throw new Exception('La pregunta ' . ($indice + 1) . ' intenta usar un tema o tipo no autorizado.');
                }

                if (!in_array($respuestaCorrecta, ['a', 'b', 'c', 'd'], true)) {
                    throw new Exception('La respuesta correcta de la pregunta ' . ($indice + 1) . ' no es válida.');
                }

                if (!in_array($dificultad, ['facil', 'media', 'dificil'], true)) {
                    throw new Exception('La dificultad de la pregunta ' . ($indice + 1) . ' no es válida.');
                }

                $datos = [
                    'profesor_id' => $profesorId,
                    'tema_id' => $temaId,
                    'tipo_pregunta_id' => $tipoPreguntaId,
                    'enunciado' => trim((string) ($pregunta['enunciado'] ?? '')),
                    'opcion_a' => trim((string) ($pregunta['opcion_a'] ?? '')),
                    'opcion_b' => trim((string) ($pregunta['opcion_b'] ?? '')),
                    'opcion_c' => trim((string) ($pregunta['opcion_c'] ?? '')),
                    'opcion_d' => trim((string) ($pregunta['opcion_d'] ?? '')),
                    'respuesta_correcta' => $respuestaCorrecta,
                    'explicacion' => trim((string) ($pregunta['explicacion'] ?? '')),
                    'dificultad' => $dificultad,
                ];

                if (
                    $datos['enunciado'] === ''
                    || $datos['opcion_a'] === ''
                    || $datos['opcion_b'] === ''
                    || $datos['opcion_c'] === ''
                    || $datos['opcion_d'] === ''
                ) {
                    throw new Exception('La pregunta ' . ($indice + 1) . ' está incompleta.');
                }

                $preguntasPublicadas[] = $preguntaModel->crear($datos);
            }

            if ($modo === 'practica') {
                unset($_SESSION['profesor_ia_borrador']);
                $_SESSION['profesor_ia_exito'] = count($preguntasPublicadas) . ' preguntas generadas por IA fueron agregadas al banco de tu materia.';
                $this->redirect('chat');
                return;
            }

            $titulo = trim((string) ($_POST['titulo'] ?? $borrador['titulo'] ?? 'Simulacro generado por ProgressIA'));
            $tiempo = (int) ($_POST['tiempo_limite_min'] ?? $borrador['tiempo_limite_min'] ?? 15);

            if ($titulo === '' || $tiempo <= 0) {
                throw new Exception('El título y el tiempo del simulacro son obligatorios.');
            }

            $simulacroId = $simulacroModel->crear(
                $profesorId,
                $titulo,
                'por_materia',
                $materiaId,
                $tiempo
            );

            $simulacroModel->reemplazarPreguntas($simulacroId, $preguntasPublicadas);

            unset($_SESSION['profesor_ia_borrador']);
            $_SESSION['profesor_ia_exito'] = 'Simulacro publicado correctamente. Las preguntas quedaron asociadas a tu materia y el simulacro ya puede ser utilizado por los estudiantes.';
        } catch (Throwable $e) {
            $_SESSION['profesor_ia_error'] = 'No fue posible publicar el contenido: ' . $e->getMessage();
        }

        $this->redirect('chat');
    }

    public function descartar(): void
    {
        $ctx = $this->contexto();
        if ($ctx === null) {
            return;
        }

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('chat');
            return;
        }

        unset($_SESSION['profesor_ia_borrador']);
        $_SESSION['profesor_ia_exito'] = 'El borrador fue descartado.';
        $this->redirect('chat');
    }
}
