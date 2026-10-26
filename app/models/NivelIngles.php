<?php

declare(strict_types=1);

class NivelIngles extends Model
{
    public function obtenerTodos(): array
    {
        $stmt = $this->db->query("
            SELECT id, nombre, orden
            FROM niveles_ingles
            ORDER BY orden ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nombre, orden
            FROM niveles_ingles
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $nivel = $stmt->fetch(PDO::FETCH_ASSOC);

        return $nivel ?: null;
    }
}
