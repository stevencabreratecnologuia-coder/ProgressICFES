<?php

declare(strict_types=1);

class Admin extends Model
{
    /** @return array<string, int> */
    public function obtenerEstadisticasGenerales(): array
    {
        $totalEstudiantes = (int) $this->db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'estudiante'")->fetchColumn();
        $totalDocentes = (int) $this->db->query("SELECT COUNT(*) FROM usuarios WHERE rol IN ('profesor', 'admin')")->fetchColumn();
        $totalPreguntas = (int) $this->db->query("SELECT COUNT(*) FROM preguntas")->fetchColumn();
        $totalMaterias = (int) $this->db->query("SELECT COUNT(*) FROM materias")->fetchColumn();
        $totalSimulacros = (int) $this->db->query("SELECT COUNT(*) FROM simulacros WHERE tipo <> 'practica'")->fetchColumn();

        $totalIntentosPractica = (int) $this->db->query("
            SELECT COUNT(*) FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            WHERE s.tipo = 'practica'
        ")->fetchColumn();

        $totalIntentosSimulacro = (int) $this->db->query("
            SELECT COUNT(*) FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            WHERE s.tipo <> 'practica'
        ")->fetchColumn();

        $promedioGeneral = (int) ($this->db->query("
            SELECT ROUND(AVG(es_correcta) * 100) FROM respuestas_estudiante
        ")->fetchColumn() ?: 0);

        return [
            'total_estudiantes' => $totalEstudiantes,
            'total_docentes' => $totalDocentes,
            'total_preguntas' => $totalPreguntas,
            'total_materias' => $totalMaterias,
            'total_simulacros' => $totalSimulacros,
            'total_intentos_practica' => $totalIntentosPractica,
            'total_intentos_simulacro' => $totalIntentosSimulacro,
            'promedio_general' => $promedioGeneral,
        ];
    }
}
