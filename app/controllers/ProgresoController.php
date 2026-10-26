<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Progreso.php';

class ProgresoController extends Controller
{
    public function index(): void
    {
        $this->requireAuthentication();

        $estudianteId = (int) $this->currentUser()['id'];

        $progresoModel = new Progreso();

        $porMateriaPractica = $this->aplicarColores(
            $progresoModel->obtenerPorMateria(
                $estudianteId,
                'practica'
            )
        );

        $porMateriaSimulacro = $this->aplicarColores(
            $progresoModel->obtenerPorMateria(
                $estudianteId,
                'simulacro'
            )
        );

        $nivelIngles = $this->calcularNivelIngles(
            $porMateriaPractica,
            $porMateriaSimulacro
        );

        $historialSimulacros =
            $progresoModel->obtenerHistorialSimulacros(
                $estudianteId
            );

        $resumen = $this->calcularResumen(
            $porMateriaPractica,
            $historialSimulacros
        );

        $this->render('progreso/index', [
            'pageTitle' => 'Progreso | ProgressICFES',

            'activePage' => 'progreso',

            'porMateriaPractica' => $porMateriaPractica,

            'porMateriaSimulacro' => $porMateriaSimulacro,

            'nivelIngles' => $nivelIngles,

            'historialSimulacros' => $historialSimulacros,

            'resumen' => $resumen,
        ]);
    }

    private function aplicarColores(array $filas): array
    {
        $nombresYColores = [
            1 => [
                'clase' => 'is-matematicas',
                'nombre' => 'Matemáticas',
            ],

            2 => [
                'clase' => 'is-lectura',
                'nombre' => 'Lectura Crítica',
            ],

            3 => [
                'clase' => 'is-ciencias',
                'nombre' => 'Ciencias Naturales',
            ],

            4 => [
                'clase' => 'is-sociales',
                'nombre' => 'Sociales y Ciudadanas',
            ],

            5 => [
                'clase' => 'is-ingles',
                'nombre' => 'Inglés',
            ],
        ];

        foreach ($filas as &$fila) {
            $materiaId = (int) $fila['materia_id'];

            $info = $nombresYColores[$materiaId] ?? null;

            $fila['clase_color'] = $info['clase'] ?? '';

            $fila['materia'] =
                $info['nombre'] ?? $fila['materia'];
        }

        unset($fila);

        return $filas;
    }

    private function calcularNivelIngles(
        array $porMateriaPractica,
        array $porMateriaSimulacro
    ): array {
        $total = 0;

        $correctas = 0;

        foreach ($porMateriaPractica as $fila) {
            if ((int) $fila['materia_id'] === 5) {
                $total += (int) $fila['total'];

                $correctas += (int) $fila['correctas'];
            }
        }

        foreach ($porMateriaSimulacro as $fila) {
            if ((int) $fila['materia_id'] === 5) {
                $total += (int) $fila['total'];

                $correctas += (int) $fila['correctas'];
            }
        }

        $porcentaje = $total > 0
            ? (int) round(($correctas / $total) * 100)
            : 0;

        if ($porcentaje < 40) {
            return [
                'nivel' => 'A1',
                'proximo_nivel' => 'A2',
                'porcentaje' => $porcentaje,
                'total' => $total,
                'correctas' => $correctas,
                'mensaje' =>
                'Sigue practicando las bases del Inglés para avanzar al nivel A2.',
            ];
        }

        if ($porcentaje < 60) {
            return [
                'nivel' => 'A2',
                'proximo_nivel' => 'B1',
                'porcentaje' => $porcentaje,
                'total' => $total,
                'correctas' => $correctas,
                'mensaje' =>
                'Vas avanzando. Refuerza tus conocimientos para alcanzar el nivel B1.',
            ];
        }

        if ($porcentaje < 75) {
            return [
                'nivel' => 'B1',
                'proximo_nivel' => 'B2',
                'porcentaje' => $porcentaje,
                'total' => $total,
                'correctas' => $correctas,
                'mensaje' =>
                'Tienes un nivel intermedio. Sigue practicando para llegar a B2.',
            ];
        }

        if ($porcentaje < 90) {
            return [
                'nivel' => 'B2',
                'proximo_nivel' => 'C1',
                'porcentaje' => $porcentaje,
                'total' => $total,
                'correctas' => $correctas,
                'mensaje' =>
                'Tienes un buen dominio del Inglés. Estás cerca del nivel C1.',
            ];
        }

        return [
            'nivel' => 'C1',
            'proximo_nivel' => null,
            'porcentaje' => $porcentaje,
            'total' => $total,
            'correctas' => $correctas,
            'mensaje' =>
            'Excelente trabajo. Has alcanzado el nivel más alto configurado.',
        ];
    }

    private function calcularResumen(
        array $porMateriaPractica,
        array $historialSimulacros
    ): array {
        $totalPreguntasPractica = array_sum(
            array_column(
                $porMateriaPractica,
                'total'
            )
        );

        $totalCorrectasPractica = array_sum(
            array_column(
                $porMateriaPractica,
                'correctas'
            )
        );

        $mejorPuntaje = 0;

        foreach ($historialSimulacros as $intento) {
            $mejorPuntaje = max(
                $mejorPuntaje,
                (int) $intento['puntaje_estimado_icfes']
            );
        }

        return [
            'preguntas_practica' => $totalPreguntasPractica,

            'porcentaje_practica' => $totalPreguntasPractica > 0
                ? (int) round(
                    ($totalCorrectasPractica / $totalPreguntasPractica) * 100
                )
                : 0,

            'intentos_simulacro' => count(
                $historialSimulacros
            ),

            'mejor_puntaje_icfes' => $mejorPuntaje,
        ];
    }
}
