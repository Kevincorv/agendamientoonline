<?php

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre AS rol
             FROM usuarios u
             LEFT JOIN roles r ON u.rol_id = r.id
             WHERE u.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function obtenerPorEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre AS rol
             FROM usuarios u
             JOIN roles r ON u.rol_id = r.id
             WHERE u.email = :email AND u.activo = 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function obtenerTodos(): array
    {
        return $this->db->query(
            "SELECT u.*, r.nombre AS rol_nombre, r.nombre AS rol
             FROM usuarios u
             LEFT JOIN roles r ON u.rol_id = r.id
             ORDER BY u.nombre ASC"
        )->fetchAll();
    }

    public function obtenerRoles(): array
    {
        return $this->db->query(
            "SELECT id, nombre, descripcion FROM roles ORDER BY id ASC"
        )->fetchAll();
    }

    public function crear(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol_id)
             VALUES (:nombre, :apellido, :email, :password, :rol_id)"
        );
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $stmt->execute($data);
    }

    public function actualizar(int $id, array $data): bool
    {
        $campos = "nombre = :nombre, email = :email, rol_id = :rol_id";
        if (isset($data['password'])) {
            $campos .= ", password = :password";
        }
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET $campos WHERE id = :id"
        );
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET activo = 0 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function desbloquear(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET locked_until = NULL, login_attempts = 0 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function updateLoginAttempts(int $id, int $intentos, ?string $lockedUntil = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET login_attempts = :intentos, locked_until = :locked WHERE id = :id"
        );
        return $stmt->execute([
            ':intentos' => $intentos,
            ':locked'   => $lockedUntil,
            ':id'       => $id,
        ]);
    }

    public function resetLoginAttempts(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET login_attempts = 0, locked_until = NULL WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function updateLastLogin(int $id, ?string $ip = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET last_login = NOW(), last_ip = :ip WHERE id = :id"
        );
        return $stmt->execute([':ip' => $ip, ':id' => $id]);
    }
}