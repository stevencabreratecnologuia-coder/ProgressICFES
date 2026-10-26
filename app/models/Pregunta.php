<?php

declare(strict_types=1);

class Pregunta extends Model
{
    public function obtenerAleatorias(
        ?int $temaId,
        int $materiaId,
        int $cantidad
    ): array {
        if ($temaId !== null) {
            $sql = "
                SELECT p.id, p.tema_id, p.tipo_pregunta_id, p.enunciado,
                       p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                       p.respuesta_correcta, p.explicacion, p.dificultad,
                       tp.nombre AS tipo_pregunta_nombre
                FROM preguntas p
                LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
                WHERE p.tema_id = :tema_id
                ORDER BY RAND()
                LIMIT :cantidad
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                'tema_id',
                $temaId,
                PDO::PARAM_INT
            );
        } else {
            $sql = "
                SELECT p.id, p.tema_id, p.tipo_pregunta_id, p.enunciado,
                       p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                       p.respuesta_correcta, p.explicacion, p.dificultad,
                       tp.nombre AS tipo_pregunta_nombre
                FROM preguntas p
                INNER JOIN temas t ON t.id = p.tema_id
                LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
                WHERE t.materia_id = :materia_id
                ORDER BY RAND()
                LIMIT :cantidad
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                'materia_id',
                $materiaId,
                PDO::PARAM_INT
            );
        }

        $stmt->bindValue(
            'cantidad',
            $cantidad,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene preguntas de Inglés según el nivel del estudiante.
    // Ejemplo: A1, A2, B1, B2 o C1.
    public function obtenerAleatoriasPorNivelIngles(
        ?int $temaId,
        int $materiaId,
        int $nivelId,
        int $cantidad
    ): array {
        if ($temaId !== null) {
            $sql = "
                SELECT p.id, p.tema_id, p.tipo_pregunta_id, p.enunciado,
                       p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                       p.respuesta_correcta, p.explicacion, p.dificultad,
                       p.nivel_ingles_id,
                       tp.nombre AS tipo_pregunta_nombre
                FROM preguntas p
                LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
                WHERE p.tema_id = :tema_id
                AND p.nivel_ingles_id = :nivel_id
                ORDER BY RAND()
                LIMIT :cantidad
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                'tema_id',
                $temaId,
                PDO::PARAM_INT
            );
        } else {
            $sql = "
                SELECT p.id, p.tema_id, p.tipo_pregunta_id, p.enunciado,
                       p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                       p.respuesta_correcta, p.explicacion, p.dificultad,
                       p.nivel_ingles_id,
                       tp.nombre AS tipo_pregunta_nombre
                FROM preguntas p
                INNER JOIN temas t ON t.id = p.tema_id
                LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
                WHERE t.materia_id = :materia_id
                AND p.nivel_ingles_id = :nivel_id
                ORDER BY RAND()
                LIMIT :cantidad
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                'materia_id',
                $materiaId,
                PDO::PARAM_INT
            );
        }

        $stmt->bindValue(
            'nivel_id',
            $nivelId,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            'cantidad',
            $cantidad,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca el ID de un nivel de Inglés usando su nombre.
    // Ejemplo: "A1" devuelve el ID correspondiente en niveles_ingles.
    public function obtenerNivelInglesPorNombre(
        string $nombreNivel
    ): ?array {
        $sql = "
            SELECT id, nombre, orden
            FROM niveles_ingles
            WHERE nombre = :nombre
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'nombre' => strtoupper(trim($nombreNivel)),
        ]);

        $nivel = $stmt->fetch(PDO::FETCH_ASSOC);

        return $nivel ?: null;
    }

    public function obtenerTodas(
        ?int $materiaId = null,
        ?int $profesorId = null
    ): array {
        $sql = "
            SELECT p.id, p.tema_id, p.tipo_pregunta_id, p.enunciado,
                   p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                   p.respuesta_correcta, p.explicacion, p.dificultad,
                   p.profesor_id,
                   p.nivel_ingles_id,
                   t.nombre AS tema_nombre,
                   t.materia_id,
                   m.nombre AS materia_nombre,
                   tp.nombre AS tipo_pregunta_nombre
            FROM preguntas p
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN materias m ON m.id = t.materia_id
            LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
        ";

        $condiciones = [];
        $parametros = [];

        if ($materiaId !== null) {
            $condiciones[] = "t.materia_id = :materia_id";

            $parametros['materia_id'] = $materiaId;
        }

        if ($profesorId !== null) {
            $condiciones[] = "p.profesor_id = :profesor_id";

            $parametros['profesor_id'] = $profesorId;
        }

        if ($condiciones !== []) {
            $sql .= " WHERE " . implode(
                " AND ",
                $condiciones
            );
        }

        $sql .= "
            ORDER BY m.nombre, t.nombre, p.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*,
                   t.nombre AS tema_nombre,
                   t.materia_id,
                   m.nombre AS materia_nombre,
                   tp.nombre AS tipo_pregunta_nombre
            FROM preguntas p
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN materias m ON m.id = t.materia_id
            LEFT JOIN tipos_pregunta tp ON tp.id = p.tipo_pregunta_id
            WHERE p.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);

        return $pregunta ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "
            INSERT INTO preguntas (
                profesor_id,
                tema_id,
                tipo_pregunta_id,
                enunciado,
                opcion_a,
                opcion_b,
                opcion_c,
                opcion_d,
                respuesta_correcta,
                explicacion,
                dificultad,
                nivel_ingles_id
            )
            VALUES (
                :profesor_id,
                :tema_id,
                :tipo_pregunta_id,
                :enunciado,
                :opcion_a,
                :opcion_b,
                :opcion_c,
                :opcion_d,
                :respuesta_correcta,
                :explicacion,
                :dificultad,
                :nivel_ingles_id
            )
        ";

        $stmt = $this->db->prepare($sql);

        $nivelInglesId = $datos['nivel_ingles_id'] ?? null;

        $stmt->execute([
            'profesor_id' => $datos['profesor_id'],
            'tema_id' => $datos['tema_id'],
            'tipo_pregunta_id' => $datos['tipo_pregunta_id'],
            'enunciado' => $datos['enunciado'],
            'opcion_a' => $datos['opcion_a'],
            'opcion_b' => $datos['opcion_b'],
            'opcion_c' => $datos['opcion_c'],
            'opcion_d' => $datos['opcion_d'],
            'respuesta_correcta' => $datos['respuesta_correcta'],
            'explicacion' => $datos['explicacion'],
            'dificultad' => $datos['dificultad'],
            'nivel_ingles_id' => $nivelInglesId,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizar(
        int $id,
        array $datos
    ): void {
        $sql = "
            UPDATE preguntas SET
                tema_id = :tema_id,
                tipo_pregunta_id = :tipo_pregunta_id,
                enunciado = :enunciado,
                opcion_a = :opcion_a,
                opcion_b = :opcion_b,
                opcion_c = :opcion_c,
                opcion_d = :opcion_d,
                respuesta_correcta = :respuesta_correcta,
                explicacion = :explicacion,
                dificultad = :dificultad,
                nivel_ingles_id = :nivel_ingles_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tema_id' => $datos['tema_id'],
            'tipo_pregunta_id' => $datos['tipo_pregunta_id'],
            'enunciado' => $datos['enunciado'],
            'opcion_a' => $datos['opcion_a'],
            'opcion_b' => $datos['opcion_b'],
            'opcion_c' => $datos['opcion_c'],
            'opcion_d' => $datos['opcion_d'],
            'respuesta_correcta' => $datos['respuesta_correcta'],
            'explicacion' => $datos['explicacion'],
            'dificultad' => $datos['dificultad'],
            'nivel_ingles_id' => $datos['nivel_ingles_id'] ?? null,
            'id' => $id,
        ]);
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM preguntas WHERE id = :id"
        );

        $stmt->execute([
            'id' => $id,
        ]);
    }
}
