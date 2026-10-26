<?php

declare(strict_types=1);

class Materia extends Model
{
    /** @return array<int, array{id:int, nombre:string, descripcion:string}> */
    public function obtenerTodas(): array
    {
        $stmt = $this->db->query("SELECT id, nombre, descripcion FROM materias ORDER BY nombre");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, nombre, descripcion FROM materias WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        $materia = $stmt->fetch(PDO::FETCH_ASSOC);

        return $materia ?: null;
    }

    public function crear(string $nombre, string $descripcion): int
    {
        $sql = "INSERT INTO materias (nombre, descripcion) VALUES (:nombre, :descripcion)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
