<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<pre style='background:#fee;padding:20px;border-radius:8px'>";
    echo "ERROR [$errno]: $errstr\nArchivo: $errfile\nLínea: $errline\n";
    echo "</pre>";
    return true;
});

set_exception_handler(function(Throwable $e) {
    echo "<pre style='background:#fee;padding:20px;border-radius:8px'>";
    echo "EXCEPCIÓN: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\nLínea: " . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
});

// ── Config ─────────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// ── Helpers ────────────────────────────────────────────────
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/email.php';

// ── Models (primero Database, luego los demás) ─────────────
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/models/Medico.php';
require_once __DIR__ . '/models/Especialidad.php';
require_once __DIR__ . '/models/Horario.php';
require_once __DIR__ . '/models/Cita.php';
require_once __DIR__ . '/models/RbacModel.php';
require_once __DIR__ . '/models/AuditoriaModel.php';
require_once __DIR__ . '/models/SesionModel.php';
require_once __DIR__ . '/models/Feriado.php';
require_once __DIR__ . '/models/BloqueoMedico.php';
require_once __DIR__ . '/models/Notificacion.php';

// ── Middleware (después de los models) ─────────────────────
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// ── Controllers ────────────────────────────────────────────
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CitaController.php';
require_once __DIR__ . '/controllers/MedicoController.php';
require_once __DIR__ . '/controllers/EspecialidadController.php';
require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/SistemaController.php';
require_once __DIR__ . '/controllers/HorarioController.php';
require_once __DIR__ . '/controllers/PacienteController.php';
require_once __DIR__ . '/controllers/AuditoriaController.php';
require_once __DIR__ . '/controllers/FeriadoController.php';
require_once __DIR__ . '/controllers/NotificacionController.php';

// ── Sesión ─────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Rutas ──────────────────────────────────────────────────
require_once __DIR__ . '/routes/web.php';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = '/clinica-san-luis';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}

resolverRuta($uri ?: '/');