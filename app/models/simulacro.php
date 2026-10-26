<?php

declare(strict_types=1);

class Simulacro extends Model
{
    // Solo lista simulacros "de verdad" (no las sesiones de práctica libre,
    // que también usan esta tabla por dentro pero son privadas de cada estudiante).
    // Si se pasa el id del estudiante, se ocultan los simulacros que ya presentó.
    /** @return array<int, array<string, mixed>> */
    public function obtenerDisponibles(int $estudianteId = 0): array
    {
        $sql = "
            SELECT s.id, s.titulo, s.tipo, s.tiempo_limite_min, s.fecha_creacion,
                   m.nombre AS materia_nombre,
                   COUNT(sp.id) AS num_preguntas
            FROM simulacros s
            LEFT JOIN materias m ON m.id = s.materia_id
            LEFT JOIN simulacro_preguntas sp ON sp.simulacro_id = s.id
            WHERE s.tipo <> 'practica'
              AND NOT EXISTS (
                  SELECT 1
                  FROM resultados_simulacro r
                  WHERE r.simulacro_id = s.id
                    AND r.estudiante_id = :estudiante_id
              )
            GROUP BY s.id
            HAVING num_preguntas > 0
            ORDER BY s.fecha_creacion DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['estudiante_id' => $estudianteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Indica si el estudiante ya presentó este simulacro.
    public function yaPresentado(int $simulacroId, int $estudianteId): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM resultados_simulacro
            WHERE simulacro_id = :simulacro_id AND estudiante_id = :estudiante_id
            LIMIT 1
        ");
        $stmt->execute([
            'simulacro_id' => $simulacroId,
            'estudiante_id' => $estudianteId,
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "
            SELECT s.id, s.titulo, s.tipo, s.tiempo_limite_min, s.materia_id, s.profesor_id,
                   m.nombre AS materia_nombre
            FROM simulacros s
            LEFT JOIN materias m ON m.id = s.materia_id
            WHERE s.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $simulacro = $stmt->fetch(PDO::FETCH_ASSOC);

        return $simulacro ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public function obtenerPreguntas(int $id): array
    {
        $sql = "
            SELECT p.id, p.tema_id, p.enunciado, p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                   p.respuesta_correcta, p.explicacion, p.dificultad
            FROM simulacro_preguntas sp
            INNER JOIN preguntas p ON p.id = sp.pregunta_id
            WHERE sp.simulacro_id = :simulacro_id
            ORDER BY sp.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['simulacro_id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea el registro "contenedor" de un intento: sirve tanto para un simulacro real
    // creado por un docente como para una sesión de práctica libre (tipo = 'practica').
    public function crear(int $profesorId, string $titulo, string $tipo, ?int $materiaId, int $tiempoLimiteMin): int
    {
        $sql = "
            INSERT INTO simulacros (profesor_id, titulo, tipo, materia_id, tiempo_limite_min)
            VALUES (:profesor_id, :titulo, :tipo, :materia_id, :tiempo_limite_min)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'profesor_id' => $profesorId,
            'titulo' => $titulo,
            'tipo' => $tipo,
            'materia_id' => $materiaId,
            'tiempo_limite_min' => $tiempoLimiteMin,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function agregarPregunta(int $simulacroId, int $preguntaId): void
    {
        $sql = "INSERT INTO simulacro_preguntas (simulacro_id, pregunta_id) VALUES (:simulacro_id, :pregunta_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'simulacro_id' => $simulacroId,
            'pregunta_id' => $preguntaId,
        ]);
    }

    // --- Gestión (panel de admin) ---

    // Simulacros creados por un profesor específico, dentro de su materia
    // (usado por el panel de progreso/dashboard del profesor).
    /** @return array<int, array<string, mixed>> */
    public function obtenerPorProfesor(int $profesorId, int $materiaId): array
    {
        $sql = "
            SELECT s.id, s.titulo, s.tipo, s.tiempo_limite_min, s.materia_id, s.fecha_creacion,
                   m.nombre AS materia_nombre,
                   COUNT(sp.id) AS num_preguntas
            FROM simulacros s
            LEFT JOIN materias m ON m.id = s.materia_id
            LEFT JOIN simulacro_preguntas sp ON sp.simulacro_id = s.id
            WHERE s.profesor_id = :profesor_id AND s.materia_id = :materia_id AND s.tipo <> 'practica'
            GROUP BY s.id
            ORDER BY s.fecha_creacion DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'profesor_id' => $profesorId,
            'materia_id' => $materiaId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lista todos los simulacros reales (no práctica), incluso sin preguntas todavía,
    // para poder editarlos o borrarlos desde el panel.
    /** @return array<int, array<string, mixed>> */
    public function obtenerTodosAdmin(?int $profesorId = null): array
    {
        $sql = "
            SELECT s.id, s.titulo, s.tipo, s.tiempo_limite_min, s.materia_id,
                   m.nombre AS materia_nombre,
                   COUNT(sp.id) AS num_preguntas
            FROM simulacros s
            LEFT JOIN materias m ON m.id = s.materia_id
            LEFT JOIN simulacro_preguntas sp ON sp.simulacro_id = s.id
            WHERE s.tipo <> 'practica'
        ";

        $parametros = [];

        if ($profesorId !== null) {
            $sql .= " AND s.profesor_id = :profesor_id ";
            $parametros['profesor_id'] = $profesorId;
        }

        $sql .= " GROUP BY s.id ORDER BY s.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<int, int> */
    public function obtenerPreguntaIds(int $simulacroId): array
    {
        $stmt = $this->db->prepare("SELECT pregunta_id FROM simulacro_preguntas WHERE simulacro_id = :id");
        $stmt->execute(['id' => $simulacroId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function actualizar(int $id, string $titulo, string $tipo, ?int $materiaId, int $tiempoLimiteMin): void
    {
        $sql = "
            UPDATE simulacros SET
                titulo = :titulo,
                tipo = :tipo,
                materia_id = :materia_id,
                tiempo_limite_min = :tiempo_limite_min
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'tipo' => $tipo,
            'materia_id' => $materiaId,
            'tiempo_limite_min' => $tiempoLimiteMin,
            'id' => $id,
        ]);
    }

    // Reemplaza por completo la lista de preguntas asociadas a un simulacro.
    /** @param array<int, int> $preguntaIds */
    public function reemplazarPreguntas(int $simulacroId, array $preguntaIds): void
    {
        $stmt = $this->db->prepare("DELETE FROM simulacro_preguntas WHERE simulacro_id = :id");
        $stmt->execute(['id' => $simulacroId]);

        foreach ($preguntaIds as $preguntaId) {
            $this->agregarPregunta($simulacroId, (int) $preguntaId);
        }
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM simulacros WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
