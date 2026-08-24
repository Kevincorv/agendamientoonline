<?php

class HorarioController
{
    private Horario $horarioModel;
    private Medico $medicoModel;
    private BloqueoMedico $bloqueoModel;

    public function __construct()
    {
        $this->horarioModel = new Horario();
        $this->medicoModel  = new Medico();
        $this->bloqueoModel = new BloqueoMedico();
    }

    public function listado(): void
    {
        AuthMiddleware::requireRole('administrador');
        $medicos = $this->medicoModel->obtenerTodos();
        $horariosPorMedico = [];
        foreach ($medicos as $m) {
            $horariosPorMedico[$m['id']] = $this->horarioModel->obtenerPorMedico($m['id']);
        }
        $bloqueosPorMedico = [];
        foreach ($medicos as $m) {
            $bloqueosPorMedico[$m['id']] = $this->bloqueoModel->obtenerPorMedico($m['id']);
        }
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/horarios.php';
    }

    public function crear(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/horarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/horarios');
        }

        $medicoId = (int) ($_POST['medico_id'] ?? 0);
        $dia      = (int) ($_POST['dia_semana'] ?? -1);
        $inicio   = sanitize($_POST['hora_inicio'] ?? '');
        $fin      = sanitize($_POST['hora_fin'] ?? '');
        $duracion = (int) ($_POST['duracion'] ?? 30);

        if (!$medicoId || $dia < 0 || $dia > 6 || !$inicio || !$fin) {
            flashMessage('error', 'Completá todos los campos obligatorios.');
            redirect('/admin/horarios');
        }

        if ($inicio >= $fin) {
            flashMessage('error', 'La hora de inicio debe ser menor a la hora de fin.');
            redirect('/admin/horarios');
        }

        if ($this->horarioModel->crear([
            ':medico_id'        => $medicoId,
            ':dia_semana'       => $dia,
            ':hora_inicio'      => $inicio,
            ':hora_fin'         => $fin,
            ':duracion'         => $duracion,
            ':intervalo_minutos'=> $duracion,
        ])) {
            AuditoriaModel::registrar([
                'accion'      => 'crear',
                'tabla'       => 'horarios',
                'descripcion' => "Bloque horario creado para médico #{$medicoId}, día {$dia}",
            ]);
            flashMessage('success', 'Bloque horario agregado correctamente.');
        } else {
            flashMessage('error', 'Error al crear el bloque horario.');
        }
        redirect('/admin/horarios');
    }

    public function eliminarBloque(): void
    {
        AuthMiddleware::requireRole('administrador');
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id && $this->horarioModel->eliminar($id)) {
            AuditoriaModel::registrar([
                'accion'      => 'eliminar',
                'tabla'       => 'horarios',
                'registro_id' => $id,
                'descripcion' => "Bloque horario #{$id} eliminado",
            ]);
            flashMessage('success', 'Bloque horario eliminado.');
        } else {
            flashMessage('error', 'Error al eliminar el bloque.');
        }
        redirect('/admin/horarios');
    }

    public function bloquearFecha(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/horarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF invalido.');
            redirect('/admin/horarios');
        }

        $medicoId = (int) ($_POST['medico_id'] ?? 0);
        $fecha    = sanitize($_POST['fecha'] ?? '');
        $motivo   = sanitize($_POST['motivo'] ?? '');

        if (!$medicoId || !$fecha) {
            flashMessage('error', 'Completa todos los campos.');
            redirect('/admin/horarios');
        }

        if ($this->bloqueoModel->crear($medicoId, $fecha, $motivo)) {
            flashMessage('success', 'Fecha bloqueada para el medico.');
        } else {
            flashMessage('error', 'Error al bloquear (puede que ya exista).');
        }
        redirect('/admin/horarios');
    }

    public function desbloquearFecha(): void
    {
        AuthMiddleware::requireRole('administrador');
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->bloqueoModel->eliminar($id);
            flashMessage('success', 'Bloqueo eliminado.');
        }
        redirect('/admin/horarios');
    }

    public function slotsJson(): void
    {
        header('Content-Type: application/json');
        $medicoId = (int) ($_GET['medico_id'] ?? 0);
        $fecha    = sanitize($_GET['fecha'] ?? '');
        if (!$medicoId || !$fecha) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
            return;
        }
        echo json_encode([
            'success' => true,
            'slots'   => $this->horarioModel->generarSlots($medicoId, $fecha),
        ]);
    }
}
