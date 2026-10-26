<?php

declare(strict_types=1);

class Tema extends Model
{
    /** @return array<int, array{id:int, materia_id:int, nombre:string}> */
    public function obtenerTodosAgrupados(): array
    {
        $stmt = $this->db->query(
            "SELECT id, materia_id, nombre
             FROM temas
             ORDER BY materia_id, nombre"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<int, array{id:int, materia_id:int, nombre:string}> */
    public function obtenerPorMateria(int $materiaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, materia_id, nombre
             FROM temas
             WHERE materia_id = :materia_id
             ORDER BY nombre"
        );

        $stmt->execute([
            'materia_id' => $materiaId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, materia_id, nombre
             FROM temas
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->execute([
            'id' => $id
        ]);

        $tema = $stmt->fetch(PDO::FETCH_ASSOC);

        return $tema ?: null;
    }

    public function crear(int $materiaId, string $nombre): int
    {
        $sql = "
            INSERT INTO temas
                (materia_id, nombre)
            VALUES
                (:materia_id, :nombre)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'materia_id' => $materiaId,
            'nombre' => $nombre,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
