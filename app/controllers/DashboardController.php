<?php

declare(strict_types=1);

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuthentication();

        $rol = $this->currentUser()['rol'] ?? '';

        if ($rol === 'admin') {
            $estadisticas = (new Admin())->obtenerEstadisticasGenerales();

            $this->render('dashboard/admin', [
                'pageTitle' => 'Inicio | ProgressICFES',
                'activePage' => 'inicio',
                'estadisticas' => $estadisticas,
            ]);

            return;
        }

        if ($rol === 'profesor') {
            (new ProfesorController())->index();

            return;
        }

        $materias = (new Materia())->obtenerTodas();

        $materiaSugerida = $this->obtenerMateriaSugerida(
            $materias,
            (int) ($this->currentUser()['id'] ?? 0)
        );

        $this->render('dashboard/index', [
            'pageTitle' => 'Inicio | ProgressICFES',
            'activePage' => 'inicio',
            'materias' => array_map(
                fn(array $materia): array =>
                $materia + $this->iconoMateria($materia['nombre']),
                $materias
            ),
            'materiaSugerida' => $materiaSugerida,
        ]);
    }

    public function profesor(): void
    {
        $this->requireAuthentication();

        if (($this->currentUser()['rol'] ?? '') !== 'admin') {
            $this->redirect('');
            return;
        }

        $materia = null;

        $rendimientoPractica = [
            'total' => 0,
            'correctas' => 0,
            'porcentaje' => 0,
            'estudiantes' => 0,
        ];

        $rendimientoSimulacro = [
            'total' => 0,
            'correctas' => 0,
            'porcentaje' => 0,
            'estudiantes' => 0,
        ];

        $this->render('dashboard/profesor', [
            'pageTitle' => 'Vista de profesor | ProgressICFES',
            'activePage' => '',
            'materia' => $materia,
            'rendimientoPractica' => $rendimientoPractica,
            'rendimientoSimulacro' => $rendimientoSimulacro,
            'esVistaAdmin' => true,
        ]);
    }

    public function estudiante(): void
    {
        $this->requireAuthentication();

        if (($this->currentUser()['rol'] ?? '') !== 'admin') {
            $this->redirect('');
            return;
        }

        $materias = (new Materia())->obtenerTodas();

        $materiaSugerida = $materias !== []
            ? $materias[0]
            : null;

        $this->render('dashboard/index', [
            'pageTitle' => 'Vista de estudiante | ProgressICFES',
            'activePage' => '',
            'materias' => array_map(
                fn(array $materia): array =>
                $materia + $this->iconoMateria($materia['nombre']),
                $materias
            ),
            'materiaSugerida' => $materiaSugerida,
            'esVistaAdmin' => true,
        ]);
    }

    private function iconoMateria(string $nombre): array
    {
        $nombre = strtolower($nombre);

        if (str_contains($nombre, 'matemat')) {
            return [
                'clase' => 'matematicas',
                'icono' => 'matematicas',
            ];
        }

        if (str_contains($nombre, 'lectura')) {
            return [
                'clase' => 'lectura',
                'icono' => 'lectura',
            ];
        }

        if (str_contains($nombre, 'cienc')) {
            return [
                'clase' => 'ciencias',
                'icono' => 'ciencias',
            ];
        }

        if (
            str_contains($nombre, 'social') ||
            str_contains($nombre, 'ciudad')
        ) {
            return [
                'clase' => 'sociales',
                'icono' => 'sociales',
            ];
        }

        if (str_contains($nombre, 'ingl')) {
            return [
                'clase' => 'ingles',
                'icono' => 'ingles',
            ];
        }

        return [
            'clase' => '',
            'icono' => 'materia',
        ];
    }

    private function obtenerMateriaSugerida(
        array $materias,
        int $estudianteId
    ): ?array {
        if ($materias === []) {
            return null;
        }

        $rendimiento = (new Progreso())->obtenerPorMateria(
            $estudianteId,
            'practica'
        );

        if ($rendimiento !== []) {
            usort(
                $rendimiento,
                static fn(array $a, array $b): int =>
                $a['porcentaje'] <=> $b['porcentaje']
            );

            $peorMateriaId = (int) $rendimiento[0]['materia_id'];

            foreach ($materias as $materia) {
                if ((int) $materia['id'] === $peorMateriaId) {
                    return $materia;
                }
            }
        }

        $indice = (((int) date('N')) - 1) % count($materias);

        return $materias[$indice];
    }
}
