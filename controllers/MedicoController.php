<?php
class MedicoController {
    private Medico $medicoModel;
    private Especialidad $espModel;
    private Cita $citaModel;
    private Horario $horarioModel;

    public function __construct() {
        $this->medicoModel  = new Medico();
        $this->espModel     = new Especialidad();
        $this->citaModel    = new Cita();
        $this->horarioModel = new Horario();
    }

    public function listado(): void {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        $medicos        = $this->medicoModel->obtenerTodos();
        $especialidades = $this->espModel->obtenerTodas();
        $csrfToken      = generateCsrfToken(); // ← fix CSRF
        require_once __DIR__ . '/../views/admin/medicos.php';
    }

    public function crear(): void {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/medicos');
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.'); redirect('/admin/medicos');
        }
        $data = [
            ':nombre'          => sanitize($_POST['nombre'] ?? ''),
            ':apellido'        => sanitize($_POST['apellido'] ?? ''),
            ':email'           => sanitize($_POST['email'] ?? ''),
            ':telefono'        => sanitize($_POST['telefono'] ?? ''),
            ':especialidad_id' => (int)($_POST['especialidad_id'] ?? 0),
            ':matricula'       => sanitize($_POST['matricula'] ?? ''),
            ':descripcion'     => sanitize($_POST['descripcion'] ?? ''),
        ];
        if ($this->medicoModel->crear($data)) {
            flashMessage('success', 'Médico creado correctamente.');
        } else {
            flashMessage('error', 'Error al crear el médico.');
        }
        redirect('/admin/medicos');
    }

    public function editar(): void {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/medicos');
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/medicos');
        }
        $id     = (int)($_POST['id'] ?? 0);
        $medico = $this->medicoModel->obtenerPorId($id);
        if (!$medico) {
            flashMessage('error', 'Médico no encontrado.');
            redirect('/admin/medicos');
        }
        $data = [
            ':nombre'          => sanitize($_POST['nombre']          ?? ''),
            ':apellido'        => sanitize($_POST['apellido']        ?? ''),
            ':email'           => sanitize($_POST['email']           ?? ''),
            ':telefono'        => sanitize($_POST['telefono']        ?? ''),
            ':especialidad_id' => (int)($_POST['especialidad_id']    ?? 0),
            ':matricula'       => sanitize($_POST['matricula']       ?? ''),
            ':descripcion'     => sanitize($_POST['descripcion']     ?? ''),
        ];
        if ($this->medicoModel->actualizar($id, $data)) {
            flashMessage('success', 'Médico actualizado correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar el médico.');
        }
        redirect('/admin/medicos');
    }

    public function eliminar(): void {
        AuthMiddleware::requireRole('administrador');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $this->medicoModel->eliminar($id);
        flashMessage('success', 'Médico desactivado.');
        redirect('/admin/medicos');
    }

    public function toggleDisponibilidad(): void {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->medicoModel->toggleDisponible($id);
            flashMessage('success', 'Disponibilidad del médico actualizada.');
        }
        redirect('/admin/medicos');
    }

    public function dashboardMedico(): void {
        AuthMiddleware::requireRole('medico');
        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        if (!$medico) { redirect('/admin/login'); }

        $fecha  = sanitize($_GET['fecha'] ?? date('Y-m-d'));
        $citas  = $this->citaModel->obtenerPorMedico($medico['id'], $fecha);

        // Estadísticas
        $totalAtendidos = $this->citaModel->contarPorMedicoYEstado($medico['id'], 4);
        $totalHoy       = count($citas);
        $pendientesHoy  = count(array_filter($citas, fn($c) => $c['estado_id'] == 1 || $c['estado_id'] == 2));

        // Próxima cita del día
        $proximaCita = null;
        $horaActual  = date('H:i:s');
        foreach ($citas as $c) {
            if (($c['estado_id'] == 1 || $c['estado_id'] == 2) && $c['hora'] >= $horaActual) {
                $proximaCita = $c;
                break;
            }
        }

        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/medico/dashboard.php';
    }

    public function perfil(): void {
        AuthMiddleware::requireRole('medico');
        $medico    = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        if (!$medico) { redirect('/admin/login'); }
        $especialidades = $this->espModel->obtenerTodas();
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/medico/perfil.php';
    }

    public function actualizarPerfil(): void {
        AuthMiddleware::requireRole('medico');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/medico/perfil');
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.'); redirect('/medico/perfil');
        }

        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        $medico = $this->medicoModel->obtenerPorUsuarioId($id);
        if (!$medico) { redirect('/admin/login'); }

        $data = [
            ':nombre'          => sanitize($_POST['nombre']          ?? ''),
            ':apellido'        => sanitize($_POST['apellido']        ?? ''),
            ':email'           => sanitize($_POST['email']           ?? ''),
            ':telefono'        => sanitize($_POST['telefono']        ?? ''),
            ':matricula'       => sanitize($_POST['matricula']       ?? ''),
            ':descripcion'     => sanitize($_POST['descripcion']     ?? ''),
        ];

        if ($this->medicoModel->actualizar($medico['id'], $data)) {
            AuditoriaModel::registrar([
                'accion'      => 'editar',
                'tabla'       => 'medicos',
                'registro_id' => $medico['id'],
                'descripcion' => "Médico editó su perfil: {$medico['nombre']} {$medico['apellido']}",
            ]);
            flashMessage('success', 'Perfil actualizado correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar el perfil.');
        }
        redirect('/medico/perfil');
    }

    public function cambiarDisponibilidad(): void {
        AuthMiddleware::requireRole('medico');
        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        if ($medico) {
            $this->medicoModel->toggleDisponible($medico['id']);
            flashMessage('success', 'Disponibilidad actualizada.');
        }
        redirect('/medico/dashboard');
    }
}