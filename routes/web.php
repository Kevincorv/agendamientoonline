<?php

function resolverRuta(string $uri): void {
    $uri = strtok($uri, '?');
    $uri = rtrim($uri, '/') ?: '/';

    $method = $_SERVER['REQUEST_METHOD'];

    // ── Rutas estáticas ────────────────────────────────────
    $rutas = [
        'GET' => [
            '/'                             => [CitaController::class,        'home'],
            '/agendar'                      => [CitaController::class,        'agendar'],
            '/confirmacion'                 => [CitaController::class,        'confirmacion'],
            '/cancelar-cita'                => [CitaController::class,        'cancelarCita'],
            '/admin'                        => [AuthController::class,        'loginForm'],
            '/admin/login'                  => [AuthController::class,        'loginForm'],
            '/admin/logout'                 => [AuthController::class,        'logout'],
            '/admin/dashboard'              => [DashboardController::class,   'index'],
            '/admin/citas'                  => [CitaController::class,        'listadoAdmin'],
            '/admin/citas/exportar'         => [CitaController::class,        'exportarCSV'],
            '/admin/medicos'                => [MedicoController::class,      'listado'],
            '/admin/medicos/disponibilidad' => [MedicoController::class,      'toggleDisponibilidad'],
            '/admin/especialidades'         => [EspecialidadController::class,'listado'],
            '/admin/usuarios'               => [UsuarioController::class,     'listado'],
            '/medico/dashboard'             => [MedicoController::class,      'dashboardMedico'],
            '/medico/perfil'                => [MedicoController::class,      'perfil'],
            '/medico/disponibilidad'        => [MedicoController::class,      'cambiarDisponibilidad'],
            '/admin/api/stats/hoy'          => [DashboardController::class,   'statsJson'],
            '/admin/api/slots'              => [HorarioController::class,     'slotsJson'],
            '/admin/api/citas/detalle'      => [CitaController::class,        'detalleJson'],
            // ── PACIENTE ───────────────────────────────
            '/paciente/login'               => [PacienteController::class,    'loginForm'],
            '/paciente/registro'            => [PacienteController::class,    'registroForm'],
            '/paciente/dashboard'           => [PacienteController::class,    'dashboard'],
            '/paciente/historial'           => [PacienteController::class,    'historial'],
            '/paciente/perfil'              => [PacienteController::class,    'perfil'],
            '/paciente/comprobante'         => [PacienteController::class,    'comprobante'],
            '/paciente/reagendar'           => [PacienteController::class,    'reagendarForm'],
            '/paciente/logout'              => [PacienteController::class,    'logout'],
            '/paciente/notificaciones/json' => [PacienteController::class,    'notificacionesJson'],
            '/admin/horarios'               => [HorarioController::class,     'listado'],
            '/admin/feriados'               => [FeriadoController::class,     'listado'],
            '/admin/auditoria'              => [SistemaController::class,     'auditoria'],
            '/admin/reportes'               => [SistemaController::class,     'reportes'],
            '/admin/reportes/exportar'      => [SistemaController::class,     'reportesExportarCSV'],
            '/admin/backups'                => [SistemaController::class,     'backups'],
            '/admin/backups/descargar'      => [SistemaController::class,     'backupsDescargar'],
            '/admin/api/notificaciones'     => [NotificacionController::class,'json'],
            '/admin/api/citas/cambiar-medico' => [CitaController::class,      'cambiarMedico'],
        ],
        'POST' => [
            '/cita/guardar'                 => [CitaController::class,        'guardarCita'],
            '/cancelar-cita'                => [CitaController::class,        'cancelarCita'],
            '/admin/login'                  => [AuthController::class,        'login'],
            '/admin/citas/estado'           => [CitaController::class,        'cambiarEstado'],
            '/admin/api/citas/estado'       => [CitaController::class,        'cambiarEstadoAjax'],
            '/admin/medicos/crear'          => [MedicoController::class,      'crear'],
            '/admin/medicos/disponibilidad' => [MedicoController::class,      'toggleDisponibilidad'],
            '/admin/especialidades/crear'   => [EspecialidadController::class,'crear'],
            '/admin/usuarios/crear'         => [UsuarioController::class,     'crear'],
            '/admin/horarios/crear'         => [HorarioController::class,     'crear'],
            '/admin/horarios/eliminar'      => [HorarioController::class,     'eliminarBloque'],
            '/admin/horarios/bloquear'      => [HorarioController::class,     'bloquearFecha'],
            '/admin/horarios/desbloquear'   => [HorarioController::class,     'desbloquearFecha'],
            // ── PACIENTE ───────────────────────────────
            '/paciente/login'               => [PacienteController::class,    'login'],
            '/paciente/registro'            => [PacienteController::class,    'registro'],
            '/paciente/cancelar'            => [PacienteController::class,    'cancelar'],
            '/paciente/reagendar'           => [PacienteController::class,    'reagendar'],
            '/paciente/perfil/actualizar'   => [PacienteController::class,    'actualizarPerfil'],
            '/paciente/password'            => [PacienteController::class,    'cambiarPassword'],
            '/paciente/notificaciones/marcar' => [PacienteController::class,  'marcarNotificaciones'],
            '/medico/perfil/actualizar'     => [MedicoController::class,      'actualizarPerfil'],
            '/admin/feriados/crear'        => [FeriadoController::class,     'crear'],
            '/admin/feriados/eliminar'     => [FeriadoController::class,     'eliminar'],
            '/admin/feriados/toggle'       => [FeriadoController::class,     'toggle'],
            '/admin/backups/crear'         => [SistemaController::class,     'backupsCrear'],
            '/admin/backups/eliminar'      => [SistemaController::class,     'backupsEliminar'],
            '/admin/backups/restaurar'     => [SistemaController::class,     'backupsRestaurar'],
            '/admin/api/notificaciones/marcar' => [NotificacionController::class, 'marcarLeida'],
            '/admin/api/notificaciones/marcar-leidas' => [NotificacionController::class, 'marcarLeidas'],
        ],
    ];

    // ── Coincidencia exacta ───────────────────────────────
    if (isset($rutas[$method][$uri])) {
        [$clase, $metodo] = $rutas[$method][$uri];
        $controlador = new $clase();
        $controlador->$metodo();
        return;
    }

    // ── Rutas dinámicas POST /admin/{recurso}/{id}/{accion} ─
    if ($method === 'POST') {
        $dinamicas = [
            '#^/admin/usuarios/(?P<id>\d+)/eliminar$#'    => [UsuarioController::class,  'eliminar'],
            '#^/admin/usuarios/(?P<id>\d+)/editar$#'      => [UsuarioController::class,  'editar'],
            '#^/admin/usuarios/(?P<id>\d+)/desbloquear$#' => [UsuarioController::class,  'desbloquear'],
            '#^/admin/medicos/(?P<id>\d+)/eliminar$#'     => [MedicoController::class,   'eliminar'],
            '#^/admin/medicos/(?P<id>\d+)/editar$#'       => [MedicoController::class,   'editar'],
            '#^/admin/citas/(?P<id>\d+)/eliminar$#'       => [CitaController::class,     'eliminar'],
            '#^/admin/citas/(?P<id>\d+)/estado$#'         => [CitaController::class,     'cambiarEstado'],
            '#^/admin/especialidades/(?P<id>\d+)/eliminar$#' => [EspecialidadController::class, 'eliminar'],
            '#^/admin/especialidades/(?P<id>\d+)/editar$#'   => [EspecialidadController::class, 'editar'],
            '#^/admin/horarios/(?P<id>\d+)/eliminar$#'      => [HorarioController::class, 'eliminarBloque'],
        ];

        foreach ($dinamicas as $patron => [$clase, $metodo]) {
            if (preg_match($patron, $uri, $matches)) {
                $_POST['id'] = (int) $matches['id'];
                $controlador = new $clase();
                $controlador->$metodo();
                return;
            }
        }
    }

    // ── 404 ───────────────────────────────────────────────
    http_response_code(404);
    require_once __DIR__ . '/../views/layouts/404.php';
}