<?php

class RbacModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getPermisosUsuario(int $usuarioId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.nombre
            FROM permisos p
            INNER JOIN rol_permiso rp ON rp.permiso_id = p.id
            INNER JOIN roles r        ON r.id = rp.rol_id
            INNER JOIN usuarios u     ON u.rol_id = r.id
            WHERE u.id = :id
        ");
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRoles(): array
    {
        return $this->db->query("SELECT id, nombre, descripcion FROM roles ORDER BY id")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignarRol(int $usuarioId, int $rolId): bool
    {
        return $this->db->prepare("UPDATE usuarios SET rol_id = :rol_id WHERE id = :id")
                        ->execute([':rol_id' => $rolId, ':id' => $usuarioId]);
    }
}