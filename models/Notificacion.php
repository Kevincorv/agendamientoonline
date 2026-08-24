<?php

class Notificacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerNoLeidas(?int $usuarioId = null, int $limite = 20): array
    {
        $sql = "SELECT * FROM notificaciones WHERE leido = 0";
        $params = [];
        if ($usuarioId !== null) {
            $sql .= " AND (usuario_id = :uid OR usuario_id IS NULL)";
            $params[':uid'] = $usuarioId;
        }
        $sql .= " ORDER BY created_at DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarNoLeidas(?int $usuarioId = null): int
    {
        $sql = "SELECT COUNT(*) FROM notificaciones WHERE leido = 0";
        $params = [];
        if ($usuarioId !== null) {
            $sql .= " AND (usuario_id = :uid OR usuario_id IS NULL)";
            $params[':uid'] = $usuarioId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function marcarLeidas(): bool
    {
        return $this->db->exec("UPDATE notificaciones SET leido = 1 WHERE leido = 0") !== false;
    }

    public function marcarLeida(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE notificaciones SET leido = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function crear(string $titulo, ?string $mensaje = null, string $tipo = 'info', ?int $usuarioId = null): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo) VALUES (:uid, :titulo, :mensaje, :tipo)"
        );
        $stmt->execute([
            ':uid'     => $usuarioId,
            ':titulo'  => $titulo,
            ':mensaje' => $mensaje,
            ':tipo'    => $tipo,
        ]);
    }
}
