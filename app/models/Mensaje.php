<?php

declare(strict_types=1);

class Mensaje extends Model
{
    /**
     * Guardar un mensaje
     */
    public function guardar(
        int $conversacionId,
        string $rol,
        string $mensaje,
        int $tokens = 0
    ): int {

        $sql = "

            INSERT INTO mensajes
            (
                conversacion_id,
                rol,
                mensaje,
                tokens
            )

            VALUES
            (
                :conversacion_id,
                :rol,
                :mensaje,
                :tokens
            )

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':conversacion_id' => $conversacionId,
            ':rol' => $rol,
            ':mensaje' => $mensaje,
            ':tokens' => $tokens

        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Obtener historial completo
     */
    public function obtenerHistorial(
        int $conversacionId
    ): array {

        $sql = "

            SELECT *

            FROM mensajes

            WHERE conversacion_id = :id

            ORDER BY fecha ASC

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':id' => $conversacionId

        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener último mensaje
     */
    public function ultimoMensaje(
        int $conversacionId
    ): ?array {

        $sql = "

            SELECT *

            FROM mensajes

            WHERE conversacion_id = :id

            ORDER BY fecha DESC

            LIMIT 1

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':id' => $conversacionId

        ]);

        $mensaje = $stmt->fetch(PDO::FETCH_ASSOC);

        return $mensaje ?: null;
    }

    /**
     * Contar mensajes
     */
    public function contarMensajes(
        int $conversacionId
    ): int {

        $sql = "

            SELECT COUNT(*) total

            FROM mensajes

            WHERE conversacion_id = :id

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':id' => $conversacionId

        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Eliminar todos los mensajes
     */
    public function eliminarPorConversacion(
        int $conversacionId
    ): bool {

        $sql = "

            DELETE

            FROM mensajes

            WHERE conversacion_id = :id

        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':id' => $conversacionId

        ]);
    }

    /**
     * Total de tokens utilizados
     */
    public function obtenerTokens(
        int $conversacionId
    ): int {

        $sql = "

            SELECT
                SUM(tokens)

            FROM mensajes

            WHERE conversacion_id = :id

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':id' => $conversacionId

        ]);

        return (int)$stmt->fetchColumn();
    }
}
