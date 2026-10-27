<?php

declare(strict_types=1);

class ResultadoSimulacro extends Model
{
    public function crear(int $estudianteId, int $simulacroId, int $puntaje, int $puntajeIcfes, int $tiempoUsadoSeg): int
    {
        $sql = "
            INSERT INTO resultados_simulacro
                (estudiante_id, simulacro_id, puntaje, puntaje_estimado_icfes, fecha_realizacion, tiempo_usado_seg)
            VALUES
                (:estudiante_id, :simulacro_id, :puntaje, :puntaje_icfes, NOW(), :tiempo_usado_seg)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'estudiante_id' => $estudianteId,
            'simulacro_id' => $simulacroId,
            'puntaje' => $puntaje,
            'puntaje_icfes' => $puntajeIcfes,
            'tiempo_usado_seg' => $tiempoUsadoSeg,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Solo devuelve el resultado si pertenece al estudiante que lo pide.
    public function obtenerPorId(int $id, int $estudianteId): ?array
    {
        $sql = "
            SELECT r.id, r.puntaje, r.puntaje_estimado_icfes, r.fecha_realizacion, r.tiempo_usado_seg,
                   s.titulo, s.tiempo_limite_min, m.nombre AS materia_nombre
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            LEFT JOIN materias m ON m.id = s.materia_id
            WHERE r.id = :id AND r.estudiante_id = :estudiante_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'estudiante_id' => $estudianteId]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    /**
     * Últimos simulacros presentados por el estudiante (excluye la práctica libre).
     *
     * @return array<int, array<string, mixed>>
     */
    public function obtenerRecientes(int $estudianteId, int $limite = 3): array
    {
        $sql = "
            SELECT r.id, r.puntaje, r.puntaje_estimado_icfes, r.fecha_realizacion,
                   s.titulo
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            WHERE r.estudiante_id = :estudiante_id
              AND s.tipo <> 'practica'
            ORDER BY r.fecha_realizacion DESC, r.id DESC
            LIMIT " . max(1, $limite) . "
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['estudiante_id' => $estudianteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen rápido: cuántos simulacros ha presentado, mejor y promedio ICFES estimado.
     *
     * @return array{total:int, mejor:int, promedio:int}
     */
    public function obtenerResumen(int $estudianteId): array
    {
        $sql = "
            SELECT COUNT(*) AS total,
                   COALESCE(MAX(r.puntaje_estimado_icfes), 0) AS mejor,
                   COALESCE(ROUND(AVG(r.puntaje_estimado_icfes)), 0) AS promedio
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            WHERE r.estudiante_id = :estudiante_id
              AND s.tipo <> 'practica'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['estudiante_id' => $estudianteId]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($fila['total'] ?? 0),
            'mejor' => (int) ($fila['mejor'] ?? 0),
            'promedio' => (int) ($fila['promedio'] ?? 0),
        ];
    }
}
