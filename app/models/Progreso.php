<?php

declare(strict_types=1);

class Progreso extends Model
{
    // Porcentaje de aciertos por materia. $modo = 'practica' o 'simulacro'.

    /** @return array<int, array{materia_id:int, materia:string, total:int, correctas:int, porcentaje:int}> */
    public function obtenerPorMateria(int $estudianteId, string $modo): array
    {
        $condicionTipo = $modo === 'practica' ? "s.tipo = 'practica'" : "s.tipo <> 'practica'";

        $sql = "
            SELECT m.id AS materia_id, m.nombre AS materia,
                   COUNT(re.id) AS total,
                   SUM(re.es_correcta) AS correctas
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            INNER JOIN respuestas_estudiante re ON re.resultado_id = r.id
            INNER JOIN preguntas p ON p.id = re.pregunta_id
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN materias m ON m.id = t.materia_id
            WHERE r.estudiante_id = :estudiante_id AND {$condicionTipo}
            GROUP BY m.id
            ORDER BY m.id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'estudiante_id' => $estudianteId,
        ]);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];

        foreach ($filas as $fila) {
            $total = (int) $fila['total'];
            $correctas = (int) $fila['correctas'];

            $resultado[] = [
                'materia_id' => (int) $fila['materia_id'],
                'materia' => (string) $fila['materia'],
                'total' => $total,
                'correctas' => $correctas,
                'porcentaje' => $total > 0
                    ? (int) round(($correctas / $total) * 100)
                    : 0,
            ];
        }

        return $resultado;
    }

    // Resumen de la práctica libre del estudiante: sesiones, preguntas resueltas y % de aciertos.

    /** @return array{sesiones:int, preguntas:int, correctas:int, porcentaje:int} */
    public function obtenerResumenPractica(int $estudianteId): array
    {
        $sql = "
            SELECT COUNT(DISTINCT r.id) AS sesiones,
                   COUNT(re.id) AS preguntas,
                   COALESCE(SUM(re.es_correcta), 0) AS correctas
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id AND s.tipo = 'practica'
            LEFT JOIN respuestas_estudiante re ON re.resultado_id = r.id
            WHERE r.estudiante_id = :estudiante_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['estudiante_id' => $estudianteId]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $preguntas = (int) ($fila['preguntas'] ?? 0);
        $correctas = (int) ($fila['correctas'] ?? 0);

        return [
            'sesiones' => (int) ($fila['sesiones'] ?? 0),
            'preguntas' => $preguntas,
            'correctas' => $correctas,
            'porcentaje' => $preguntas > 0 ? (int) round(($correctas / $preguntas) * 100) : 0,
        ];
    }

    // Últimos intentos de simulacro (no incluye práctica libre) para mostrar un historial simple.

    /** @return array<int, array<string, mixed>> */
    public function obtenerHistorialSimulacros(int $estudianteId, int $limite = 10): array
    {
        $sql = "
            SELECT r.id, r.puntaje, r.puntaje_estimado_icfes, r.fecha_realizacion, r.tiempo_usado_seg,
                   s.titulo
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id AND s.tipo <> 'practica'
            WHERE r.estudiante_id = :estudiante_id
            ORDER BY r.fecha_realizacion DESC
            LIMIT :limite
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(
            'estudiante_id',
            $estudianteId,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            'limite',
            $limite,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rendimiento de TODOS los estudiantes en una materia
    // para el dashboard del profesor.

    // $modo = 'practica' o 'simulacro'.

    /** @return array{total:int, correctas:int, porcentaje:int, estudiantes:int} */
    public function obtenerRendimientoMateria(int $materiaId, string $modo): array
    {
        $condicionTipo = $modo === 'practica'
            ? "s.tipo = 'practica'"
            : "s.tipo <> 'practica'";

        $sql = "
            SELECT COUNT(re.id) AS total,
                   SUM(re.es_correcta) AS correctas,
                   COUNT(DISTINCT r.estudiante_id) AS estudiantes
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            INNER JOIN respuestas_estudiante re ON re.resultado_id = r.id
            INNER JOIN preguntas p ON p.id = re.pregunta_id
            INNER JOIN temas t ON t.id = p.tema_id
            WHERE t.materia_id = :materia_id AND {$condicionTipo}
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'materia_id' => $materiaId,
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $total = (int) ($fila['total'] ?? 0);
        $correctas = (int) ($fila['correctas'] ?? 0);

        return [
            'total' => $total,
            'correctas' => $correctas,
            'porcentaje' => $total > 0
                ? (int) round(($correctas / $total) * 100)
                : 0,
            'estudiantes' => (int) ($fila['estudiantes'] ?? 0),
        ];
    }

    // Estudiantes matriculados en una materia, con su grado y su rendimiento resumido
    // en práctica libre dentro de esa materia (para el panel del profesor).

    /** @return array<int, array{id:int, nombre:string, apellido:string, correo:string, grado:int, total_practica:int, porcentaje_practica:int}> */
    public function obtenerEstudiantesMateria(int $materiaId): array
    {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.correo,
                u.grado,
                COUNT(t.id) AS total,
                COALESCE(
                    SUM(
                        CASE
                            WHEN t.id IS NOT NULL AND re.es_correcta = 1 THEN 1
                            ELSE 0
                        END
                    ),
                    0
                ) AS correctas
            FROM usuarios u

            LEFT JOIN resultados_simulacro r
                ON r.estudiante_id = u.id

            LEFT JOIN simulacros s
                ON s.id = r.simulacro_id
                AND s.tipo = 'practica'

            LEFT JOIN respuestas_estudiante re
                ON re.resultado_id = r.id

            LEFT JOIN preguntas p
                ON p.id = re.pregunta_id

            LEFT JOIN temas t
                ON t.id = p.tema_id
                AND t.materia_id = :materia_id_join

            WHERE
                u.rol = 'estudiante'
                AND u.materia_id = :materia_id
                AND u.grado BETWEEN 6 AND 11

            GROUP BY
                u.id,
                u.nombre,
                u.apellido,
                u.correo,
                u.grado

            ORDER BY
                u.grado DESC,
                u.nombre,
                u.apellido
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'materia_id_join' => $materiaId,
            'materia_id' => $materiaId,
        ]);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];

        foreach ($filas as $fila) {
            $total = (int) $fila['total'];
            $correctas = (int) $fila['correctas'];

            $resultado[] = [
                'id' => (int) $fila['id'],
                'nombre' => (string) $fila['nombre'],
                'apellido' => (string) $fila['apellido'],
                'correo' => (string) $fila['correo'],
                'grado' => (int) $fila['grado'],
                'total_practica' => $total,
                'porcentaje_practica' => $total > 0
                    ? (int) round(($correctas / $total) * 100)
                    : 0,
            ];
        }

        return $resultado;
    }

    // Rendimiento por tema dentro de una materia
    // (práctica + simulacro juntos),
    // para que el profesor vea qué temas debe reforzar.

    /** @return array<int, array{tema_id:int, tema:string, total:int, correctas:int, porcentaje:int}> */
    public function obtenerRendimientoTemas(int $materiaId): array
    {
        $sql = "
            SELECT t.id AS tema_id, t.nombre AS tema,
                   COUNT(re.id) AS total,
                   SUM(re.es_correcta) AS correctas
            FROM temas t
            LEFT JOIN preguntas p ON p.tema_id = t.id
            LEFT JOIN respuestas_estudiante re ON re.pregunta_id = p.id
            WHERE t.materia_id = :materia_id
            GROUP BY t.id, t.nombre
            ORDER BY t.nombre
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'materia_id' => $materiaId,
        ]);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];

        foreach ($filas as $fila) {
            $total = (int) $fila['total'];
            $correctas = (int) $fila['correctas'];

            $resultado[] = [
                'tema_id' => (int) $fila['tema_id'],
                'tema' => (string) $fila['tema'],
                'total' => $total,
                'correctas' => $correctas,
                'porcentaje' => $total > 0
                    ? (int) round(($correctas / $total) * 100)
                    : 0,
            ];
        }

        return $resultado;
    }

    // Preguntas con más respuestas incorrectas dentro de una materia
    // para el panel del profesor.

    /** @return array<int, array{id:int, enunciado:string, total_respuestas:int, fallos:int, porcentaje_fallo:int}> */
    public function obtenerPreguntasMasFalladas(
        int $materiaId,
        int $limite = 5
    ): array {
        $sql = "
            SELECT p.id, p.enunciado,
                   COUNT(re.id) AS total_respuestas,
                   SUM(
                       CASE
                           WHEN re.es_correcta = 0 THEN 1
                           ELSE 0
                       END
                   ) AS fallos
            FROM preguntas p
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN respuestas_estudiante re ON re.pregunta_id = p.id
            WHERE t.materia_id = :materia_id
            GROUP BY p.id, p.enunciado
            HAVING total_respuestas > 0
            ORDER BY fallos DESC, total_respuestas DESC
            LIMIT :limite
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(
            'materia_id',
            $materiaId,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            'limite',
            $limite,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];

        foreach ($filas as $fila) {
            $totalRespuestas = (int) $fila['total_respuestas'];
            $fallos = (int) $fila['fallos'];

            $resultado[] = [
                'id' => (int) $fila['id'],
                'enunciado' => (string) $fila['enunciado'],
                'total_respuestas' => $totalRespuestas,
                'fallos' => $fallos,
                'porcentaje_fallo' => $totalRespuestas > 0
                    ? (int) round(($fallos / $totalRespuestas) * 100)
                    : 0,
            ];
        }

        return $resultado;
    }

    // Obtiene el nivel actual de Inglés del estudiante
    // usando sus últimas 30 respuestas de la materia Inglés.

    /** @return array{nivel:string, porcentaje:int, total:int, correctas:int} */
    public function obtenerNivelIngles(int $estudianteId): array
    {
        $sql = "
            SELECT re.es_correcta
            FROM resultados_simulacro r
            INNER JOIN simulacros s ON s.id = r.simulacro_id
            INNER JOIN respuestas_estudiante re ON re.resultado_id = r.id
            INNER JOIN preguntas p ON p.id = re.pregunta_id
            INNER JOIN temas t ON t.id = p.tema_id
            INNER JOIN materias m ON m.id = t.materia_id
            WHERE r.estudiante_id = :estudiante_id
            AND m.nombre = 'Inglés'
            ORDER BY r.fecha_realizacion DESC
            LIMIT 30
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'estudiante_id' => $estudianteId,
        ]);

        $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = count($respuestas);

        // Si todavía no ha respondido preguntas de Inglés,
        // comienza en A1.
        if ($total === 0) {
            return [
                'nivel' => 'A1',
                'porcentaje' => 0,
                'total' => 0,
                'correctas' => 0,
            ];
        }

        $correctas = 0;

        foreach ($respuestas as $respuesta) {
            if ((int) $respuesta['es_correcta'] === 1) {
                $correctas++;
            }
        }

        $porcentaje = (int) round(
            ($correctas / $total) * 100
        );

        // Nivel por defecto.
        $nivel = 'A1';

        if ($porcentaje >= 90) {
            $nivel = 'C1';
        } elseif ($porcentaje >= 75) {
            $nivel = 'B2';
        } elseif ($porcentaje >= 60) {
            $nivel = 'B1';
        } elseif ($porcentaje >= 40) {
            $nivel = 'A2';
        }

        return [
            'nivel' => $nivel,
            'porcentaje' => $porcentaje,
            'total' => $total,
            'correctas' => $correctas,
        ];
    }
}
