<?php

class AuthController
{
    private Usuario $usuarioModel;

    private const MAX_INTENTOS   = 5;
    private const TIEMPO_BLOQUEO = 15; // minutos

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function loginForm(): void
    {
        AuthMiddleware::redirectIfAuthenticated();
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        $csrfToken = generateCsrfToken();

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token CSRF inválido.'];
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        $email    = htmlspecialchars(strip_tags(trim($_POST['email']    ?? '')));
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Completá todos los campos.'];
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        $usuario = $this->usuarioModel->obtenerPorEmail($email);

        if (!$usuario) {
            AuditoriaModel::registrar([
                'accion'      => 'login_fallido',
                'descripcion' => "Email inexistente: $email",
            ]);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Credenciales incorrectas.'];
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        // ── Verificar bloqueo ──────────────────────────────
        if (!empty($usuario['locked_until']) && strtotime($usuario['locked_until']) > time()) {
            $min = ceil((strtotime($usuario['locked_until']) - time()) / 60);
            AuditoriaModel::registrar([
                'accion'      => 'login_bloqueado',
                'descripcion' => "Cuenta bloqueada: $email",
            ]);
            $_SESSION['flash'] = ['type' => 'error', 'message' => "Cuenta bloqueada. Intentá en {$min} minuto(s)."];
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        // ── Verificar contraseña ───────────────────────────
        if (!password_verify($password, $usuario['password'])) {
            $intentos = ($usuario['login_attempts'] ?? 0) + 1;
            $locked   = null;

            if ($intentos >= self::MAX_INTENTOS) {
                $locked = date('Y-m-d H:i:s', strtotime('+' . self::TIEMPO_BLOQUEO . ' minutes'));
                $this->usuarioModel->updateLoginAttempts($usuario['id'], $intentos, $locked);
                AuditoriaModel::registrar([
                    'accion'      => 'cuenta_bloqueada',
                    'descripcion' => "Bloqueada tras $intentos intentos: $email",
                ]);
                $_SESSION['flash'] = ['type' => 'error', 'message' => "Demasiados intentos. Cuenta bloqueada " . self::TIEMPO_BLOQUEO . " min."];
            } else {
                $this->usuarioModel->updateLoginAttempts($usuario['id'], $intentos);
                $rest = self::MAX_INTENTOS - $intentos;
                AuditoriaModel::registrar([
                    'accion'      => 'login_fallido',
                    'descripcion' => "Contraseña incorrecta para: $email (intento $intentos)",
                ]);
                $_SESSION['flash'] = ['type' => 'error', 'message' => "Contraseña incorrecta. Te quedan $rest intento(s)."];
            }
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }

        // ── Login exitoso ──────────────────────────────────
        $this->usuarioModel->resetLoginAttempts($usuario['id']);
        $this->usuarioModel->updateLastLogin($usuario['id'], $_SERVER['REMOTE_ADDR'] ?? null);

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre']     = $usuario['nombre'] . ' ' . ($usuario['apellido'] ?? '');
        $_SESSION['email']      = $usuario['email'];
        $_SESSION['rol']        = $usuario['rol'] ?? ($usuario['rol_nombre'] ?? null);
        $_SESSION['rol_id']     = $usuario['rol_id'] ?? null;

        // Cargar permisos RBAC si tiene rol_id
        if (!empty($usuario['rol_id'])) {
            $rbac = new RbacModel();
            $_SESSION['user_permisos'] = $rbac->getPermisosUsuario($usuario['id']);
        }

        // Registrar en auditoría y sesiones
        AuditoriaModel::registrar([
            'accion'      => 'login',
            'tabla'       => 'usuarios',
            'registro_id' => $usuario['id'],
            'descripcion' => "Login exitoso: {$usuario['email']}",
        ]);

        $sesionModel = new SesionModel();
        $sesionModel->crearSesion($usuario['id']);

        $rol = $_SESSION['rol'] ?? '';
        if ($rol === 'medico') {
            header('Location: ' . APP_URL . '/medico/dashboard');
        } else {
            header('Location: ' . APP_URL . '/admin/dashboard');
        }
        exit;
    }

    public function logout(): void
    {
        AuditoriaModel::registrar([
            'accion'      => 'logout',
            'tabla'       => 'usuarios',
            'registro_id' => $_SESSION['usuario_id'] ?? null,
            'descripcion' => 'Logout: ' . ($_SESSION['email'] ?? ''),
        ]);
        session_destroy();
        header('Location: ' . APP_URL . '/admin/login');
        exit;
    }
}