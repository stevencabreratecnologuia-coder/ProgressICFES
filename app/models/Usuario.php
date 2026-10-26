<?php

declare(strict_types=1);

class Usuario extends Model
{
    public function findForLogin(string $correo): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'correo' => $correo
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function emailExists(string $correo): bool
    {
        $sql = "SELECT id FROM usuarios WHERE correo = :correo LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'correo' => $correo
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function createStudent(array $data): bool
    {
        $sql = "
            INSERT INTO usuarios
            (
                nombre,
                apellido,
                correo,
                password,
                rol,
                estado,
                grado
            )
            VALUES
            (
                :nombre,
                :apellido,
                :correo,
                :password,
                :rol,
                :estado,
                :grado
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => trim($data['nombres']),

            'apellido' => trim($data['apellidos']),

            'correo' => strtolower(
                trim($data['correo'])
            ),

            'password' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),

            'rol' => 'estudiante',

            'estado' => 'activo',

            'grado' => (int) $data['grado']
        ]);
    }

    private function ensureDocumentoIdentidadColumn(): void
    {
        $stmt = $this->db->query("SHOW COLUMNS FROM usuarios LIKE 'documento_identidad'");

        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            $this->db->exec("ALTER TABLE usuarios ADD COLUMN documento_identidad VARCHAR(50) NULL AFTER correo");
        }
    }

    public function createTeacherRequest(array $data): bool
    {
        $this->ensureDocumentoIdentidadColumn();

        $sql = "
            INSERT INTO usuarios
            (
                nombre,
                apellido,
                correo,
                documento_identidad,
                password,
                rol,
                estado,
                materia_id
            )
            VALUES
            (
                :nombre,
                :apellido,
                :correo,
                :documento_identidad,
                :password,
                :rol,
                :estado,
                :materia_id
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => trim($data['nombres']),

            'apellido' => trim($data['apellidos']),

            'correo' => strtolower(
                trim($data['correo'])
            ),

            'documento_identidad' => trim(
                $data['documento_identidad']
            ),

            'password' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),

            'rol' => 'profesor',

            'estado' => 'pendiente',

            'materia_id' => (int) $data['materia_id']
        ]);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE id = :id LIMIT 1"
        );

        $stmt->execute([
            'id' => $id
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function actualizarDatos(
        int $id,
        string $nombre,
        string $apellido
    ): bool {
        $sql = "
            UPDATE usuarios
            SET
                nombre = :nombre,
                apellido = :apellido
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'id' => $id,
        ]);
    }

    public function actualizarPassword(
        int $id,
        string $passwordHasheada
    ): bool {
        $sql = "
            UPDATE usuarios
            SET password = :password
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'password' => $passwordHasheada,
            'id' => $id,
        ]);
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.correo,
                u.rol,
                u.estado,
                u.fecha_registro,
                u.materia_id,
                u.grado,
                m.nombre AS materia_nombre
            FROM usuarios u
            LEFT JOIN materias m
                ON m.id = u.materia_id
            ORDER BY u.fecha_registro DESC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSolicitudesProfesorPendientes(): array
    {
        $this->ensureDocumentoIdentidadColumn();

        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.correo,
                u.documento_identidad,
                u.fecha_registro,
                u.materia_id,
                m.nombre AS materia_nombre
            FROM usuarios u
            LEFT JOIN materias m
                ON m.id = u.materia_id
            WHERE
                u.rol = 'profesor'
                AND u.estado = 'pendiente'
            ORDER BY u.fecha_registro ASC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aprobarSolicitudProfesor(int $id): bool
    {
        $sql = "
            UPDATE usuarios
            SET estado = 'activo'
            WHERE
                id = :id
                AND rol = 'profesor'
                AND estado = 'pendiente'
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    public function rechazarSolicitudProfesor(int $id): bool
    {
        $sql = "
            UPDATE usuarios
            SET estado = 'rechazado'
            WHERE
                id = :id
                AND rol = 'profesor'
                AND estado = 'pendiente'
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    public function actualizarRol(
        int $id,
        string $rol
    ): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET rol = :rol WHERE id = :id"
        );

        return $stmt->execute([
            'rol' => $rol,
            'id' => $id
        ]);
    }

    public function actualizarMateria(
        int $id,
        ?int $materiaId
    ): bool {
        $stmt = $this->db->prepare(
            "
            UPDATE usuarios
            SET materia_id = :materia_id
            WHERE id = :id
            "
        );

        return $stmt->execute([
            'materia_id' => $materiaId,
            'id' => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM usuarios WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
