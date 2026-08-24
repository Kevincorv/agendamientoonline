<?php

class PacienteController
{
    private Usuario $usuarioModel;
    private Cita $citaModel;
    private Medico $medicoModel;
    private Especialidad $espModel;
    private Horario $horarioModel;
    private Notificacion $notifModel;

    private const MAX_INTENTOS   = 5;
    private const TIEMPO_BLOQUEO = 15;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->citaModel    = new Cita();
        $this->medicoModel  = new Medico();
        $this->espModel     = new Especialidad();
        $this->horarioModel = new Horario();
        $this->notifModel   = new Notificacion();
    }

    // ── AUTH ──────────────────────────────────────────────

    public function loginForm(): void
    {
        if (isset($_SESSION['paciente_id'])) {
            redirect('/paciente/dashboard');
        }
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/paciente/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/login');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Token CSRF inválido.');
            redirect('/paciente/login');
        }

        $email    = htmlspecialchars(strip_tags(trim($_POST['email'] ?? '')));
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flashMessage('error', 'Completá todos los campos.');
            redirect('/paciente/login');
        }

        $usuario = $this->usuarioModel->obtenerPorEmail($email);

        if (!$usuario) {
            flashMessage('error', 'Credenciales incorrectas.');
            redirect('/paciente/login');
        }

        // Solo pacientes pueden acceder aquí
        $rol = $usuario['rol'] ?? '';
        if ($rol !== 'paciente') {
            flashMessage('error', 'Esta área es solo para pacientes.');
            redirect('/paciente/login');
        }

        // Verificar bloqueo
        if (!empty($usuario['locked_until']) && strtotime($usuario['locked_until']) > time()) {
            $min = ceil((strtotime($usuario['locked_until']) - time()) / 60);
            flashMessage('error', "Cuenta bloqueada. Intentá en {$min} minuto(s).");
            redirect('/paciente/login');
        }

        if (!password_verify($password, $usuario['password'])) {
            $intentos = ($usuario['login_attempts'] ?? 0) + 1;
            if ($intentos >= self::MAX_INTENTOS) {
                $locked = date('Y-m-d H:i:s', strtotime('+' . self::TIEMPO_BLOQUEO . ' minutes'));
                $this->usuarioModel->updateLoginAttempts($usuario['id'], $intentos, $locked);
                flashMessage('error', "Demasiados intentos. Cuenta bloqueada " . self::TIEMPO_BLOQUEO . " min.");
            } else {
                $this->usuarioModel->updateLoginAttempts($usuario['id'], $intentos);
                $rest = self::MAX_INTENTOS - $intentos;
                flashMessage('error', "Contraseña incorrecta. Te quedan $rest intento(s).");
            }
            redirect('/paciente/login');
        }

        // Login exitoso
        $this->usuarioModel->resetLoginAttempts($usuario['id']);
        $this->usuarioModel->updateLastLogin($usuario['id'], $_SERVER['REMOTE_ADDR'] ?? '');

        session_regenerate_id(true);
        $_SESSION['paciente_id']    = $usuario['id'];
        $_SESSION['paciente_email'] = $usuario['email'];
        $_SESSION['paciente_nombre']= $usuario['nombre'] . ' ' . ($usuario['apellido'] ?? '');

        AuditoriaModel::registrar([
            'accion'      => 'login',
            'tabla'       => 'usuarios',
            'registro_id' => $usuario['id'],
            'descripcion' => "Login paciente: {$usuario['email']}",
        ]);

        redirect('/paciente/dashboard');
    }

    public function registroForm(): void
    {
        if (isset($_SESSION['paciente_id'])) {
            redirect('/paciente/dashboard');
        }
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/paciente/registro.php';
    }

    public function registro(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/registro');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Token CSRF inválido.');
            redirect('/paciente/registro');
        }

        $nombre   = sanitize($_POST['nombre']   ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $email    = sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar = $_POST['password_confirm'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
            flashMessage('error', 'Completá todos los campos obligatorios.');
            redirect('/paciente/registro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashMessage('error', 'Email inválido.');
            redirect('/paciente/registro');
        }

        if (strlen($password) < 6) {
            flashMessage('error', 'La contraseña debe tener al menos 6 caracteres.');
            redirect('/paciente/registro');
        }

        if ($password !== $confirmar) {
            flashMessage('error', 'Las contraseñas no coinciden.');
            redirect('/paciente/registro');
        }

        // Verificar email único
        $existe = $this->usuarioModel->obtenerPorEmail($email);
        if ($existe) {
            flashMessage('error', 'Este email ya está registrado.');
            redirect('/paciente/registro');
        }

        $data = [
            ':nombre'    => $nombre,
            ':apellido'  => $apellido,
            ':email'     => $email,
            ':password'  => password_hash($password, PASSWORD_BCRYPT),
            ':rol_id'    => 6, // paciente
        ];

        $stmt = Database::getInstance()->getConnection()->prepare(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol_id, activo, creado_en)
             VALUES (:nombre, :apellido, :email, :password, :rol_id, 1, NOW())"
        );

        if ($stmt->execute($data)) {
            AuditoriaModel::registrar([
                'accion'      => 'registro',
                'tabla'       => 'usuarios',
                'descripcion' => "Paciente registrado: $email",
            ]);
            Notificacion::crear("Nuevo paciente registrado", "$nombre $apellido - $email", 'success');
            flashMessage('success', 'Registro exitoso. Ahora podés iniciar sesión.');
            redirect('/paciente/login');
        }

        flashMessage('error', 'Error al registrar. Intentá de nuevo.');
        redirect('/paciente/registro');
    }

    public function logout(): void
    {
        AuditoriaModel::registrar([
            'accion'      => 'logout',
            'tabla'       => 'usuarios',
            'registro_id' => $_SESSION['paciente_id'] ?? null,
            'descripcion' => 'Logout paciente: ' . ($_SESSION['paciente_email'] ?? ''),
        ]);
        unset($_SESSION['paciente_id'], $_SESSION['paciente_email'], $_SESSION['paciente_nombre']);
        session_destroy();
        redirect('/paciente/login');
    }

    // ── DASHBOARD ─────────────────────────────────────────

    public function dashboard(): void
    {
        $this->requirePaciente();
        $email = $_SESSION['paciente_email'];

        // Próximas citas (fecha >= hoy, no canceladas)
        $proximas = $this->citaModel->obtenerPorPacienteEmail($email, true);

        // Historial reciente (últimas 5 pasadas)
        $historial = $this->citaModel->obtenerPorPacienteEmail($email, false, 5);

        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/paciente/dashboard.php';
    }

    // ── HISTORIAL ─────────────────────────────────────────

    public function historial(): void
    {
        $this->requirePaciente();
        $email = $_SESSION['paciente_email'];
        $citas = $this->citaModel->obtenerPorPacienteEmail($email, false);
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/paciente/historial.php';
    }

    // ── CANCELAR CITA ─────────────────────────────────────

    public function cancelar(): void
    {
        $this->requirePaciente();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/dashboard');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/paciente/dashboard');
        }

        $id    = (int) ($_POST['cita_id'] ?? 0);
        $email = $_SESSION['paciente_email'];
        $cita  = $this->citaModel->obtenerPorId($id);

        if (!$cita || $cita['email'] !== $email) {
            flashMessage('error', 'Cita no encontrada.');
            redirect('/paciente/dashboard');
        }

        if ($this->citaModel->cambiarEstado($id, 3, 'Cancelada por el paciente')) {
            AuditoriaModel::registrar([
                'accion'      => 'cancelar',
                'tabla'       => 'citas',
                'registro_id' => $id,
                'descripcion' => "Paciente {$email} canceló cita #{$id}",
            ]);
            Notificacion::crear("Cita cancelada", "Paciente {$email} canceló cita #{$id}", 'warning');
            flashMessage('success', 'Cita cancelada correctamente.');
        } else {
            flashMessage('error', 'No se pudo cancelar la cita.');
        }
        redirect('/paciente/dashboard');
    }

    // ── REAGENDAR ─────────────────────────────────────────

    public function reagendarForm(): void
    {
        $this->requirePaciente();
        $id    = (int) ($_GET['id'] ?? 0);
        $email = $_SESSION['paciente_email'];
        $cita  = $this->citaModel->obtenerPorId($id);

        if (!$cita || $cita['email'] !== $email) {
            flashMessage('error', 'Cita no encontrada.');
            redirect('/paciente/dashboard');
        }

        $especialidades = $this->espModel->obtenerTodas();
        $medicos        = $this->medicoModel->obtenerTodos();
        $slots          = [];
        $csrfToken      = generateCsrfToken();

        if (!empty($_GET['medico_id']) && !empty($_GET['fecha'])) {
            $fecha = sanitize($_GET['fecha']);
            if ($fecha >= date('Y-m-d')) {
                $slots = $this->horarioModel->generarSlots((int)$_GET['medico_id'], $fecha);
            }
        }

        require_once __DIR__ . '/../views/public/paciente/reagendar.php';
    }

    public function reagendar(): void
    {
        $this->requirePaciente();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/dashboard');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/paciente/dashboard');
        }

        $id        = (int) ($_POST['cita_id'] ?? 0);
        $medicoId  = (int) ($_POST['medico_id'] ?? 0);
        $fecha     = sanitize($_POST['fecha'] ?? '');
        $hora      = sanitize($_POST['hora'] ?? '');
        $email     = $_SESSION['paciente_email'];

        $cita = $this->citaModel->obtenerPorId($id);
        if (!$cita || $cita['email'] !== $email) {
            flashMessage('error', 'Cita no encontrada.');
            redirect('/paciente/dashboard');
        }

        if (!$medicoId || !$fecha || !$hora) {
            flashMessage('error', 'Completá todos los campos.');
            redirect('/paciente/reagendar?id=' . $id);
        }

        if ($fecha < date('Y-m-d')) {
            flashMessage('error', 'La fecha no puede ser en el pasado.');
            redirect('/paciente/reagendar?id=' . $id);
        }

        if (!$this->citaModel->estaDisponible($medicoId, $fecha, $hora)) {
            flashMessage('error', 'Este horario ya no está disponible.');
            redirect('/paciente/reagendar?id=' . $id);
        }

        // Actualizar cita existente con nuevos datos
        if ($this->citaModel->reagendar($id, $medicoId, $fecha, $hora)) {
            AuditoriaModel::registrar([
                'accion'      => 'reagendar',
                'tabla'       => 'citas',
                'registro_id' => $id,
                'descripcion' => "Paciente {$email} reagendó cita #{$id} a {$fecha} {$hora}",
            ]);
            Notificacion::crear("Cita reagendada", "Paciente {$email} reagendó cita #{$id} a {$fecha} {$hora}", 'info');
            flashMessage('success', 'Cita reagendada correctamente.');
        } else {
            flashMessage('error', 'Error al reagendar la cita.');
        }
        redirect('/paciente/dashboard');
    }

    // ── COMPROBANTE ───────────────────────────────────────

    public function comprobante(): void
    {
        $this->requirePaciente();
        $id    = (int) ($_GET['id'] ?? 0);
        $email = $_SESSION['paciente_email'];
        $cita  = $this->citaModel->obtenerPorId($id);

        if (!$cita || $cita['email'] !== $email) {
            flashMessage('error', 'Cita no encontrada.');
            redirect('/paciente/dashboard');
        }

        require_once __DIR__ . '/../views/public/paciente/comprobante.php';
    }

    // ── PERFIL ────────────────────────────────────────────

    public function perfil(): void
    {
        $this->requirePaciente();
        $usuario   = $this->usuarioModel->obtenerPorId($_SESSION['paciente_id']);
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/paciente/perfil.php';
    }

    public function actualizarPerfil(): void
    {
        $this->requirePaciente();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/perfil');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/paciente/perfil');
        }

        $id       = (int) $_SESSION['paciente_id'];
        $nombre   = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');

        if (empty($nombre) || empty($apellido)) {
            flashMessage('error', 'Completá todos los campos.');
            redirect('/paciente/perfil');
        }

        $stmt = Database::getInstance()->getConnection()->prepare(
            "UPDATE usuarios SET nombre = :nombre, apellido = :apellido WHERE id = :id"
        );
        if ($stmt->execute([':nombre' => $nombre, ':apellido' => $apellido, ':id' => $id])) {
            $_SESSION['paciente_nombre'] = $nombre . ' ' . $apellido;
            AuditoriaModel::registrar([
                'accion'      => 'editar',
                'tabla'       => 'usuarios',
                'registro_id' => $id,
                'descripcion' => 'Paciente actualizó su perfil',
            ]);
            flashMessage('success', 'Perfil actualizado correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar el perfil.');
        }
        redirect('/paciente/perfil');
    }

    public function cambiarPassword(): void
    {
        $this->requirePaciente();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paciente/perfil');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/paciente/perfil');
        }

        $id          = (int) $_SESSION['paciente_id'];
        $actual      = $_POST['password_actual'] ?? '';
        $nueva       = $_POST['password_nueva'] ?? '';
        $confirmar   = $_POST['password_confirmar'] ?? '';

        $usuario = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario || !password_verify($actual, $usuario['password'])) {
            flashMessage('error', 'Contraseña actual incorrecta.');
            redirect('/paciente/perfil');
        }

        if (strlen($nueva) < 6) {
            flashMessage('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            redirect('/paciente/perfil');
        }

        if ($nueva !== $confirmar) {
            flashMessage('error', 'Las contraseñas no coinciden.');
            redirect('/paciente/perfil');
        }

        $stmt = Database::getInstance()->getConnection()->prepare(
            "UPDATE usuarios SET password = :password WHERE id = :id"
        );
        if ($stmt->execute([':password' => password_hash($nueva, PASSWORD_BCRYPT), ':id' => $id])) {
            flashMessage('success', 'Contraseña actualizada correctamente.');
        } else {
            flashMessage('error', 'Error al cambiar la contraseña.');
        }
        redirect('/paciente/perfil');
    }

    // ── NOTIFICACIONES ──────────────────────────────────────

    public function notificacionesJson(): void
    {
        $this->requirePaciente();
        header('Content-Type: application/json');

        $notifs = $this->notifModel->obtenerNoLeidas(null, 10);
        $count  = $this->notifModel->contarNoLeidas(null);

        echo json_encode([
            'success' => true,
            'count'   => $count,
            'data'    => $notifs,
        ]);
    }

    public function marcarNotificaciones(): void
    {
        $this->requirePaciente();
        header('Content-Type: application/json');

        $this->notifModel->marcarLeidas();
        echo json_encode(['success' => true]);
    }

    // ── HELPERS ───────────────────────────────────────────

    private function requirePaciente(): void
    {
        if (!isset($_SESSION['paciente_id'])) {
            flashMessage('error', 'Debés iniciar sesión para continuar.');
            redirect('/paciente/login');
        }
    }
}
