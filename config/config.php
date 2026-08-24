<?php
define('APP_NAME', 'CITAS MÉDICAS ONLINE');
define('APP_URL', 'http://localhost/clinica-san-luis');

define('APP_VERSION', '2.0.0');

// Email config
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'noreply@citasmedicasonline.com');
define('MAIL_PASS', 'tu_password');
define('MAIL_FROM_NAME', 'CITAS MÉDICAS ONLINE');
define('MAIL_RECORDATORIO_HORAS', 24); // horas antes para enviar recordatorio

// Zona horaria
date_default_timezone_set('America/Asuncion');

// Manejo de errores
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_name('CLINICA_SESSION');
