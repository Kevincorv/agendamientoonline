<?php

class AuditoriaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Uso estático desde cualquier parte del código:
     * AuditoriaModel::registrar(['accion' => 'crear', 'tabla' => 'citas', ...])
     */
    public static function registrar(array $datos): void
    {
        try {
            $db = Database::getInstance()->getConnection();

            $userId   = $_SESSION['usuario_id'] ?? null;
            $userName = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : null;
            $ip       = self::getIp();
            $ua       = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $stmt = $db->prepare("
                INSERT INTO auditoria
                    (usuario_id, usuario_nombre, accion, tabla, registro_id,
                     descripcion, datos_antes, datos_despues, ip, user_agent, created_at)
                VALUES
                    (:uid, :uname, :accion, :tabla, :reg_id,
                     :desc, :antes, :despues, :ip, :ua, NOW())
            ");
            $stmt->execute([
                ':uid'     => $userId,
                ':uname'   => $userName,
                ':accion'  => $datos['accion'],
                ':tabla'   => $datos['tabla']         ?? null,
                ':reg_id'  => $datos['registro_id']   ?? null,
                ':desc'    => $datos['descripcion']   ?? null,
                ':antes'   => isset($datos['datos_antes'])
                              ? json_encode($datos['datos_antes'],   JSON_UNESCAPED_UNICODE)
                              : null,
                ':despues' => isset($datos['datos_despues'])
                              ? json_encode($datos['datos_despues'], JSON_UNESCAPED_UNICODE)
                              : null,
                ':ip'      => $ip,
                ':ua'      => $ua,
            ]);
        } catch (\Throwable $e) {
            error_log('[Auditoria] Error: ' . $e->getMessage());
        }
    }

    public function getLogs(array $filtros = [], int $pagina = 1, int $porPagina = 50): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filtros['accion'])) {
            $where[]  = 'a.accion = :accion';
            $params[':accion'] = $filtros['accion'];
        }
        if (!empty($filtros['tabla'])) {
            $where[]  = 'a.tabla = :tabla';
            $params[':tabla'] = $filtros['tabla'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[]  = 'DATE(a.created_at) >= :desde';
            $params[':desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[]  = 'DATE(a.created_at) <= :hasta';
            $params[':hasta'] = $filtros['fecha_hasta'];
        }
        if (!empty($filtros['ip'])) {
            $where[]  = 'a.ip LIKE :ip';
            $params[':ip'] = '%' . $filtros['ip'] . '%';
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($pagina - 1) * $porPagina;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM auditoria a WHERE $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT a.*
            FROM auditoria a
            WHERE $whereStr
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();

        return [
            'datos'      => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total'      => $total,
            'pagina'     => $pagina,
            'por_pagina' => $porPagina,
            'paginas'    => (int) ceil($total / $porPagina),
        ];
    }

    public function getLog(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM auditoria WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($log) {
            $log['datos_antes']   = $log['datos_antes']
                                    ? json_decode($log['datos_antes'],   true) : null;
            $log['datos_despues'] = $log['datos_despues']
                                    ? json_decode($log['datos_despues'], true) : null;
        }
        return $log;
    }

    private static function getIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', $_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }
}