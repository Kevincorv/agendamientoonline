<?php

class SesionModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearSesion(int $usuarioId): void
    {
        $token = bin2hex(random_bytes(32));
        $ip    = $this->getIp();
        $ua    = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->db->prepare("
            INSERT INTO sesiones (usuario_id, session_token, ip, user_agent, created_at, last_activity, activa)
            VALUES (:uid, :token, :ip, :ua, NOW(), NOW(), 1)
        ")->execute([':uid' => $usuarioId, ':token' => $token, ':ip' => $ip, ':ua' => $ua]);
    }

    public function getSesionesUsuario(int $usuarioId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, ip, user_agent, created_at, last_activity, activa
            FROM sesiones WHERE usuario_id = :uid ORDER BY created_at DESC LIMIT 20
        ");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', $_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }
}