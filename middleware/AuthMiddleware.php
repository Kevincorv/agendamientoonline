<?php

class AuthMiddleware
{
    // ── Autenticación básica ───────────────────────────────
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debés iniciar sesión para continuar.'];
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }
    }

    // ── Verificar rol (sistema anterior — se mantiene compatible) ──
    public static function requireRole(string ...$roles): void
    {
        self::requireAuth();
        if (!in_array($_SESSION['rol'] ?? '', $roles)) {
            http_response_code(403);
            AuditoriaModel::registrar([
                'accion'      => 'acceso_denegado',
                'descripcion' => 'Sin rol suficiente para: ' . ($_SERVER['REQUEST_URI'] ?? ''),
            ]);
            die('<h1>403 - Acceso denegado</h1>');
        }
    }

    // ── Verificar permiso RBAC (sistema nuevo) ─────────────
    public static function requirePermiso(string $permiso): void
    {
        self::requireAuth();
        if (!self::tienePermiso($permiso)) {
            AuditoriaModel::registrar([
                'accion'      => 'acceso_denegado',
                'descripcion' => "Sin permiso '$permiso' para: " . ($_SERVER['REQUEST_URI'] ?? ''),
            ]);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No tenés permisos para realizar esta acción.'];
            header('Location: ' . APP_URL . '/admin/dashboard');
            exit;
        }
    }

    // ── Verificar si tiene un permiso ──────────────────────
    public static function tienePermiso(string $permiso): bool
    {
        // Admin por rol antiguo tiene todo
        if (($_SESSION['rol'] ?? '') === 'administrador') return true;

        // Verificar por permisos RBAC cargados en sesión
        $permisos = $_SESSION['user_permisos'] ?? [];
        return in_array($permiso, $permisos, true);
    }

    // ── Verificar si tiene alguno de los permisos ──────────
    public static function tieneAlgunPermiso(array $permisos): bool
    {
        foreach ($permisos as $p) {
            if (self::tienePermiso($p)) return true;
        }
        return false;
    }

    // ── Redirigir si ya está autenticado ───────────────────
    public static function redirectIfAuthenticated(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $rol = $_SESSION['rol'] ?? '';
            if ($rol === 'medico') {
                header('Location: ' . APP_URL . '/medico/dashboard');
            } else {
                header('Location: ' . APP_URL . '/admin/dashboard');
            }
            exit;
        }
    }

    // ── Rol actual ─────────────────────────────────────────
    public static function rolActual(): string
    {
        return $_SESSION['rol'] ?? '';
    }
}