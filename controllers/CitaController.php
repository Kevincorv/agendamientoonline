<?php

class CitaController
{
    private Cita $citaModel;
    private Medico $medicoModel;
    private Especialidad $espModel;
    private Horario $horarioModel;
    private PDO $db;

    public function __construct()
    {
        $this->citaModel    = new Cita();
        $this->medicoModel  = new Medico();
        $this->espModel     = new Especialidad();
        $this->horarioModel = new Horario();
        $this->db           = Database::getInstance()->getConnection();
    }

    // ── PÚBLICO ──────────────────────────────────────────

    public function home(): void
    {
        $especialidades = $this->espModel->obtenerTodas();
        $medicos        = $this->medicoModel->obtenerTodos();
        require_once __DIR__ . '/../views/public/home.php';
    }

    public function agendar(): void
    {
        $especialidades = $this->espModel->obtenerTodas();
        $medicos        = [];
        $slots          = [];
        $medicoSel      = null;
        $csrfToken      = generateCsrfToken();

        if (!empty($_GET['especialidad_id'])) {
            $medicos = $this->medicoModel->obtenerPorEspecialidad((int)$_GET['especialidad_id']);
        }

        if (!empty($_GET['medico_id'])) {
            $medicoSel = $this->medicoModel->obtenerPorId((int)$_GET['medico_id']);
        }

        if (!empty($_GET['medico_id']) && !empty($_GET['fecha'])) {
            $fecha = sanitize($_GET['fecha']);
            if ($fecha >= date('Y-m-d')) {
                $slots = $this->horarioModel->generarSlots((int)$_GET['medico_id'], $fecha);
            }
        }

        // Si se seleccionó hora para hoy y ya pasó, limpiar
        if (!empty($_GET['hora']) && !empty($_GET['fecha']) && $_GET['fecha'] === date('Y-m-d') && $_GET['hora'] < date('H:i')) {
            $qs = $_GET;
            unset($qs['hora']);
            redirect('/agendar?' . http_build_query($qs));
        }

        require_once __DIR__ . '/../views/public/agendar.php';
    }

    public function guardarCita(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/agendar');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Error de seguridad CSRF.');
            redirect('/agendar');
        }

        $data = [
            'medico_id'       => (int)($_POST['medico_id']       ?? 0),
            'especialidad_id' => (int)($_POST['especialidad_id'] ?? 0),
            'nombre_paciente' => sanitize($_POST['nombre_paciente'] ?? ''),
            'telefono'        => sanitize($_POST['telefono']        ?? ''),
            'email'           => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '',
            'motivo'          => sanitize($_POST['motivo']          ?? ''),
            'fecha'           => sanitize($_POST['fecha']           ?? ''),
            'hora'            => sanitize($_POST['hora']            ?? ''),
        ];

        if (!$data['medico_id'] || !$data['especialidad_id'] || empty($data['nombre_paciente'])
            || empty($data['telefono']) || empty($data['fecha']) || empty($data['hora'])) {
            flashMessage('error', 'Por favor completá todos los campos obligatorios.');
            redirect('/agendar');
        }

        if ($data['fecha'] < date('Y-m-d')) {
            flashMessage('error', 'La fecha no puede ser en el pasado.');
            redirect('/agendar');
        }

        if ($data['fecha'] === date('Y-m-d') && $data['hora'] < date('H:i')) {
            flashMessage('error', 'Este horario ya pasó. Seleccioná otro.');
            redirect('/agendar');
        }

        if (!$this->citaModel->estaDisponible($data['medico_id'], $data['fecha'], $data['hora'])) {
            flashMessage('error', 'Este horario ya no está disponible. Por favor seleccioná otro.');
            redirect('/agendar');
        }

        $token = $this->citaModel->crear($data);
        if ($token !== false) {
            $cita  = $this->citaModel->obtenerPorToken($token);
            if ($cita) {
                Notificacion::crear("Nueva cita registrada", "{$cita['nombre_paciente']} - {$cita['fecha']} {$cita['hora']}", 'info');
                if (!empty($cita['email'])) {
                    enviarConfirmacionCita($cita);
                }
            }
            redirect('/confirmacion?token=' . $token);
        }

        flashMessage('error', 'Error al registrar la cita. Intentalo de nuevo.');
        redirect('/agendar');
    }

    public function confirmacion(): void
    {
        $token = sanitize($_GET['token'] ?? '');
        $cita  = $token ? $this->citaModel->obtenerPorToken($token) : null;
        if (!$cita) {
            redirect('/');
        }
        require_once __DIR__ . '/../views/public/confirmacion.php';
    }

    public function cancelarCita(): void
    {
        $token = sanitize($_GET['token'] ?? $_POST['token'] ?? '');
        if (!$token) {
            redirect('/');
        }

        $cita = $this->citaModel->obtenerPorToken($token);
        if (!$cita) {
            flashMessage('error', 'Token inválido.');
            redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrf($_POST['csrf_token'] ?? '')) {
                flashMessage('error', 'Error de seguridad CSRF.');
                redirect('/');
            }
            if ($this->citaModel->cancelarPorToken($token)) {
                AuditoriaModel::registrar([
                    'accion'      => 'cancelar',
                    'tabla'       => 'citas',
                    'registro_id' => $cita['id'],
                    'descripcion' => "Cita #{$cita['id']} cancelada por token por {$cita['nombre_paciente']}",
                ]);
                Notificacion::crear("Cita cancelada", "{$cita['nombre_paciente']} canceló cita #{$cita['id']}", 'warning');
                flashMessage('success', 'Cita cancelada exitosamente.');
            } else {
                flashMessage('error', 'No se pudo cancelar la cita. Es posible que ya esté cancelada.');
            }
            redirect('/');
        }

        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/public/cancelar.php';
    }

    // ── ADMIN ─────────────────────────────────────────────

    public function listadoAdmin(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        $filtros = [
            'fecha'     => sanitize($_GET['fecha']     ?? ''),
            'medico_id' => (int)($_GET['medico_id']    ?? 0),
            'estado_id' => (int)($_GET['estado_id']    ?? 0),
            'q'         => sanitize($_GET['q']         ?? ''),
        ];
        $pagina   = max(1, (int)($_GET['pagina'] ?? 1));
        $result   = $this->citaModel->obtenerTodasPaginado($filtros, $pagina);
        $citas    = $result['datos'];
        $paginacion = [
            'total'     => $result['total'],
            'pagina'    => $result['pagina'],
            'paginas'   => $result['paginas'],
            'porPagina' => $result['porPagina'],
        ];
        $medicos    = $this->medicoModel->obtenerTodos();
        $csrfToken  = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/citas.php';
    }

    public function cambiarEstado(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/citas');
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Error de seguridad CSRF.');
            redirect('/admin/citas');
        }

        $id     = (int)($_POST['id'] ?? $_POST['cita_id'] ?? 0);
        $estado = (int)($_POST['estado_id'] ?? 0);
        $notas  = sanitize($_POST['notas']  ?? '');

        $estadosValidos = [1, 2, 3, 4];
        if (!in_array($estado, $estadosValidos)) {
            flashMessage('error', 'Estado inválido.');
            redirect('/admin/citas');
        }

        if ($this->citaModel->cambiarEstado($id, $estado, $notas)) {
            AuditoriaModel::registrar([
                'accion'      => 'cambiar_estado',
                'tabla'       => 'citas',
                'registro_id' => $id,
                'descripcion' => "Cita #{$id} → estado {$estado}",
            ]);
            Notificacion::crear("Estado de cita actualizado", "Cita #{$id} cambio a estado {$estado}", 'info');
            flashMessage('success', 'Estado actualizado correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar el estado.');
        }
        redirect('/admin/citas');
    }

    public function cambiarEstadoAjax(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Error de seguridad CSRF.']);
            return;
        }

        $id     = (int)($_POST['cita_id']   ?? 0);
        $estado = (int)($_POST['estado_id'] ?? 0);
        $notas  = sanitize($_POST['notas']  ?? '');

        $estadosValidos = [1, 2, 3, 4];
        if (!in_array($estado, $estadosValidos)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido.']);
            return;
        }

        if ($this->citaModel->cambiarEstado($id, $estado, $notas)) {
            AuditoriaModel::registrar([
                'accion'      => 'cambiar_estado',
                'tabla'       => 'citas',
                'registro_id' => $id,
                'descripcion' => "Cita #{$id} → estado {$estado} (AJAX)",
            ]);
            Notificacion::crear("Estado de cita actualizado (AJAX)", "Cita #{$id} cambio a estado {$estado}", 'info');
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
    }

    public function detalleJson(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID requerido.']);
            return;
        }

        $cita = $this->citaModel->obtenerPorId($id);
        if (!$cita) {
            echo json_encode(['success' => false, 'message' => 'Cita no encontrada.']);
            return;
        }

        echo json_encode(['success' => true, 'cita' => $cita]);
    }

    public function cambiarMedico(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metodo no permitido.']);
            return;
        }

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Error de seguridad CSRF.']);
            return;
        }

        $citaId       = (int) ($_POST['cita_id'] ?? 0);
        $nuevoMedico  = (int) ($_POST['medico_id'] ?? 0);
        $nuevaFecha   = sanitize($_POST['fecha'] ?? '');
        $nuevaHora    = sanitize($_POST['hora'] ?? '');

        if (!$citaId || !$nuevoMedico || !$nuevaFecha || !$nuevaHora) {
            echo json_encode(['success' => false, 'message' => 'Faltan parametros.']);
            return;
        }

        // Verificar que el slot este disponible
        if (!$this->citaModel->estaDisponible($nuevoMedico, $nuevaFecha, $nuevaHora)) {
            echo json_encode(['success' => false, 'message' => 'El horario seleccionado ya no esta disponible.']);
            return;
        }

        if ($this->citaModel->cambiarMedico($citaId, $nuevoMedico, $nuevaFecha, $nuevaHora)) {
            AuditoriaModel::registrar([
                'accion'      => 'cambiar_medico',
                'tabla'       => 'citas',
                'registro_id' => $citaId,
                'descripcion' => "Cita #{$citaId} reasignada al medico #{$nuevoMedico} el {$nuevaFecha} a las {$nuevaHora}",
            ]);
            Notificacion::crear("Cita reasignada", "Cita #{$citaId} fue reasignada a otro medico", 'warning');
            echo json_encode(['success' => true, 'message' => 'Cita reasignada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al reasignar la cita.']);
        }
    }

    public function exportarCSV(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        $filtros = [
            'fecha'     => sanitize($_GET['fecha'] ?? ''),
            'medico_id' => (int)($_GET['medico_id'] ?? 0),
            'estado_id' => (int)($_GET['estado_id'] ?? 0),
            'q'         => sanitize($_GET['q'] ?? ''),
        ];
        $csv = $this->citaModel->exportarCitasCSV($filtros);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="citas_' . date('Y-m-d') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo "\xEF\xBB\xBF"; // BOM UTF-8
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
        echo '<table border="1">';
        echo '<tr><th>Paciente</th><th>Teléfono</th><th>Email</th><th>Médico</th><th>Especialidad</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Motivo</th></tr>';

        $citas = $this->citaModel->obtenerTodas($filtros);
        foreach ($citas as $c) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($c['nombre_paciente']) . '</td>';
            echo '<td>' . htmlspecialchars($c['telefono']) . '</td>';
            echo '<td>' . htmlspecialchars($c['email']) . '</td>';
            echo '<td>Dr. ' . htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) . '</td>';
            echo '<td>' . htmlspecialchars($c['especialidad']) . '</td>';
            echo '<td>' . htmlspecialchars($c['fecha']) . '</td>';
            echo '<td>' . htmlspecialchars($c['hora']) . '</td>';
            echo '<td>' . htmlspecialchars($c['estado']) . '</td>';
            echo '<td>' . htmlspecialchars($c['motivo']) . '</td>';
            echo '</tr>';
        }
        echo '</table></body></html>';
        exit;
    }

    public function eliminar(): void
    {
        AuthMiddleware::requireRole('administrador');

        // FIX: el router dinámico pone el id en $_POST['id']
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$id) {
            flashMessage('error', 'ID de cita inválido.');
            redirect('/admin/citas');
        }

        if ($this->citaModel->eliminar($id)) {
            AuditoriaModel::registrar([
                'accion'      => 'eliminar',
                'tabla'       => 'citas',
                'registro_id' => $id,
                'descripcion' => "Cita #{$id} eliminada",
            ]);
            Notificacion::crear("Cita eliminada", "Cita #{$id} fue eliminada", 'danger');
            flashMessage('success', 'Cita eliminada correctamente.');
        } else {
            flashMessage('error', 'No se pudo eliminar la cita.');
        }
        redirect('/admin/citas');
    }
}