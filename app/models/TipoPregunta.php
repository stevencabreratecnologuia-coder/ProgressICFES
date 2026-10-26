<?php

declare(strict_types=1);

class TipoPregunta extends Model
{
    public function obtenerTodos(): array
    {
        $stmt = $this->db->query("SELECT id, nombre, descripcion FROM tipos_pregunta ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, string $descripcion = ''): int
    {
        $stmt = $this->db->prepare("INSERT INTO tipos_pregunta (nombre, descripcion) VALUES (:nombre, :descripcion)");
        $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion]);
        return (int) $this->db->lastInsertId();
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM tipos_pregunta WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
