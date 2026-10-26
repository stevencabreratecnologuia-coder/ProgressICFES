<?php

declare(strict_types=1);

class Conversacion extends Model
{
    /**
     * Crear una conversación
     */
    public function crear(
        int $usuarioId,
        string $titulo,
        ?string $materia = null
    ): int {

        $sql = "
            INSERT INTO conversaciones
            (
                usuario_id,
                titulo,
                materia
            )
            VALUES
            (
                :usuario_id,
                :titulo,
                :materia
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':titulo' => $titulo,
            ':materia' => $materia
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Obtener todas las conversaciones
     * de un estudiante
     */
    public function obtenerPorUsuario(int $usuarioId): array
    {
        $sql = "
            SELECT *
            FROM conversaciones
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_actualizacion DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar una conversación
     */
    public function buscar(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM conversaciones
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $conversacion = $stmt->fetch(PDO::FETCH_ASSOC);

        return $conversacion ?: null;
    }

    /**
     * Buscar una conversación
     * perteneciente a un usuario específico
     */
    public function buscarPorUsuario(
        int $id,
        int $usuarioId
    ): ?array {

        $sql = "
            SELECT *
            FROM conversaciones
            WHERE id = :id
            AND usuario_id = :usuario_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $usuarioId
        ]);

        $conversacion = $stmt->fetch(PDO::FETCH_ASSOC);

        return $conversacion ?: null;
    }

    /**
     * Cambiar título
     */
    public function actualizarTitulo(
        int $id,
        string $titulo
    ): bool {

        $sql = "
            UPDATE conversaciones
            SET titulo = :titulo
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':titulo' => $titulo,
            ':id' => $id
        ]);
    }

    /**
     * Actualizar fecha de actividad
     */
    public function actualizarActividad(
        int $id
    ): bool {

        $sql = "
            UPDATE conversaciones
            SET fecha_actualizacion = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    /**
     * Eliminar conversación y sus mensajes
     */
    public function eliminar(
        int $id
    ): bool {

        try {

            $this->db->beginTransaction();

            /*
             * Primero eliminamos los mensajes
             */
            $sqlMensajes = "
                DELETE FROM mensajes
                WHERE conversacion_id = :conversacion_id
            ";

            $stmtMensajes = $this->db->prepare($sqlMensajes);

            $stmtMensajes->execute([
                ':conversacion_id' => $id
            ]);

            /*
             * Después eliminamos la conversación
             */
            $sqlConversacion = "
                DELETE FROM conversaciones
                WHERE id = :id
            ";

            $stmtConversacion = $this->db->prepare($sqlConversacion);

            $stmtConversacion->execute([
                ':id' => $id
            ]);

            $eliminada = $stmtConversacion->rowCount() > 0;

            $this->db->commit();

            return $eliminada;
        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}
