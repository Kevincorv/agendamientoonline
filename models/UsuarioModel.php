<?php

namespace App\Models;

use App\Core\Database;
use App\Traits\Auditable;
use PDO;

class UsuarioModel
{
    use Auditable;

    private PDO    $db;
    protected string $auditTabla = 'usuarios';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             LEFT JOIN roles r ON r.id = u.rol_id
             WHERE u.email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             LEFT JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll(): array
    {
        return $this->db->query(
            "SELECT u.id, u.nombre, u.email, u.rol_id, r.nombre AS rol_nombre,
                    u.last_login, u.last_ip, u.login_attempts, u.locked_until,
                    u.created_at
             FROM usuarios u
             LEFT JOIN roles r ON r.id = u.rol_id
             ORDER BY u.nombre ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, email, password, rol_id, created_at)
            VALUES (:nombre, :email, :password, :rol_id, NOW())
        ");
        $stmt->execute([
            ':nombre'   => $datos['nombre'],
            ':email'    => $datos['email'],
            ':password' => password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            ':rol_id'   => $datos['rol_id'] ?? null,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->auditarCrear($id, $datos);
        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $antes = $this->findById($id);
        $campos = [];
        $params = [':id' => $id];

        if (!empty($datos['nombre'])) {
            $campos[] = 'nombre = :nombre';
            $params[':nombre'] = $datos['nombre'];
        }
        if (!empty($datos['email'])) {
            $campos[] = 'email = :email';
            $params[':email'] = $datos['email'];
        }
        if (!empty($datos['password'])) {
            $campos[] = 'password = :password';
            $params[':password'] = password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        if (isset($datos['rol_id'])) {
            $campos[] = 'rol_id = :rol_id';
            $params[':rol_id'] = $datos['rol_id'];
        }

        if (empty($campos)) return false;

        $stmt = $this->db->prepare("UPDATE usuarios SET " . implode(',', $campos) . " WHERE id = :id");
        $result = $stmt->execute($params);
        if ($result) $this->auditarEditar($id, $antes ?? [], $datos);
        return $result;
    }

    public function eliminar(int $id): bool
    {
        $antes = $this->findById($id);
        $this->auditarEliminar($id, $antes ?? []);
        return $this->db->prepare("DELETE FROM usuarios WHERE id = :id")
                        ->execute([':id' => $id]);
    }

    public function updateLoginAttempts(int $id, int $intentos, ?string $lockedUntil = null): void
    {
        $this->db->prepare(
            "UPDATE usuarios SET login_attempts = :a, locked_until = :l WHERE id = :id"
        )->execute([':a' => $intentos, ':l' => $lockedUntil, ':id' => $id]);
    }

    public function resetLoginAttempts(int $id): void
    {
        $this->db->prepare(
            "UPDATE usuarios SET login_attempts = 0, locked_until = NULL WHERE id = :id"
        )->execute([':id' => $id]);
    }

    public function updateLastLogin(int $id, string $ip): void
    {
        $this->db->prepare(
            "UPDATE usuarios SET last_login = NOW(), last_ip = :ip WHERE id = :id"
        )->execute([':ip' => $ip, ':id' => $id]);
    }

    public function desbloquear(int $id): void
    {
        $this->db->prepare(
            "UPDATE usuarios SET login_attempts = 0, locked_until = NULL WHERE id = :id"
        )->execute([':id' => $id]);
    }
}