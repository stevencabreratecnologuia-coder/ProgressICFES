<?php

declare(strict_types=1);

class RespuestaEstudiante extends Model
{
    public function guardar(int $resultadoId, int $preguntaId, ?string $opcionSeleccionada, bool $esCorrecta): void
    {
        $sql = "
            INSERT INTO respuestas_estudiante (resultado_id, pregunta_id, opcion_seleccionada, es_correcta)
            VALUES (:resultado_id, :pregunta_id, :opcion_seleccionada, :es_correcta)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'resultado_id' => $resultadoId,
            'pregunta_id' => $preguntaId,
            'opcion_seleccionada' => $opcionSeleccionada,
            'es_correcta' => $esCorrecta ? 1 : 0,
        ]);
    }

    /**
     * Obtiene una respuesta concreta junto con su pregunta, validando que
     * el resultado pertenezca al estudiante indicado.
     */
    public function obtenerPreguntaDeResultado(
        int $resultadoId,
        int $preguntaId,
        int $estudianteId
    ): ?array {
        $sql = "
            SELECT
                re.resultado_id,
                re.pregunta_id,
                re.opcion_seleccionada,
                re.es_correcta,
                p.id,
                p.tema_id,
                p.enunciado,
                p.opcion_a,
                p.opcion_b,
                p.opcion_c,
                p.opcion_d,
                p.respuesta_correcta,
                p.explicacion,
                p.dificultad,
                t.nombre AS tema_nombre,
                t.materia_id,
                m.nombre AS materia_nombre
            FROM respuestas_estudiante re
            INNER JOIN resultados_simulacro r
                ON r.id = re.resultado_id
            INNER JOIN preguntas p
                ON p.id = re.pregunta_id
            INNER JOIN temas t
                ON t.id = p.tema_id
            INNER JOIN materias m
                ON m.id = t.materia_id
            WHERE
                re.resultado_id = :resultado_id
                AND re.pregunta_id = :pregunta_id
                AND r.estudiante_id = :estudiante_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'resultado_id' => $resultadoId,
            'pregunta_id' => $preguntaId,
            'estudiante_id' => $estudianteId,
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public function obtenerPorResultado(int $resultadoId): array
    {
        $sql = "
            SELECT re.opcion_seleccionada, re.es_correcta,
                   p.id AS pregunta_id, p.tema_id, p.enunciado, p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                   p.respuesta_correcta, p.explicacion, p.dificultad,
                   t.nombre AS tema_nombre, m.nombre AS materia_nombre
            FROM respuestas_estudiante re
            INNER JOIN preguntas p ON p.id = re.pregunta_id
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN materias m ON m.id = t.materia_id
            WHERE re.resultado_id = :resultado_id
            ORDER BY re.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['resultado_id' => $resultadoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
