<?php
class EspecialidadController {
    private Especialidad $espModel;

    public function __construct() {
        $this->espModel = new Especialidad();
    }

    public function listado(): void {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        $especialidades = $this->espModel->obtenerTodas();
        $csrfToken      = generateCsrfToken(); // ← fix CSRF
        require_once __DIR__ . '/../views/admin/especialidades.php';
    }

    public function crear(): void {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/especialidades');
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/especialidades');
        }
        $data = [
            ':nombre'      => sanitize($_POST['nombre'] ?? ''),
            ':descripcion' => sanitize($_POST['descripcion'] ?? ''),
            ':icono'       => sanitize($_POST['icono'] ?? 'fa-stethoscope'),
        ];
        if ($this->espModel->crear($data)) {
            $id = Database::getInstance()->getConnection()->lastInsertId();

            // ── Auto-crear médico por defecto con horarios ──
            $medicoModel = new Medico();
            $horarioModel = new Horario();
            $nombreEsp = $data[':nombre'];
            $medicoData = [
                ':nombre'           => $nombreEsp,
                ':apellido'         => '(por defecto)',
                ':email'            => '',
                ':telefono'         => '',
                ':especialidad_id'  => (int)$id,
                ':matricula'        => '',
                ':descripcion'      => "Médico automático de $nombreEsp",
            ];
            if ($medicoModel->crear($medicoData)) {
                $medicoId = Database::getInstance()->getConnection()->lastInsertId();
                $horariosDefecto = [
                    ['dia' => 1, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 1, 'ini' => '14:00', 'fin' => '18:00'],
                    ['dia' => 2, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 2, 'ini' => '14:00', 'fin' => '18:00'],
                    ['dia' => 3, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 3, 'ini' => '14:00', 'fin' => '18:00'],
                    ['dia' => 4, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 4, 'ini' => '14:00', 'fin' => '18:00'],
                    ['dia' => 5, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 5, 'ini' => '14:00', 'fin' => '18:00'],
                    ['dia' => 6, 'ini' => '07:00', 'fin' => '12:00'],
                    ['dia' => 6, 'ini' => '14:00', 'fin' => '18:00'],
                ];
                foreach ($horariosDefecto as $h) {
                    $horarioModel->crear([
                        ':medico_id'         => $medicoId,
                        ':dia_semana'        => $h['dia'],
                        ':hora_inicio'       => $h['ini'],
                        ':hora_fin'          => $h['fin'],
                        ':intervalo_minutos' => 30,
                    ]);
                }
                AuditoriaModel::registrar([
                    'accion'      => 'crear_medico_auto',
                    'tabla'       => 'medicos',
                    'registro_id' => (int)$medicoId,
                    'descripcion' => "Médico automático creado para {$nombreEsp}",
                ]);
            }

            AuditoriaModel::registrar([
                'accion'      => 'crear',
                'tabla'       => 'especialidades',
                'registro_id' => (int)$id,
                'descripcion' => "Especialidad creada: {$data[':nombre']}",
            ]);
            flashMessage('success', 'Especialidad creada correctamente.');
        } else {
            flashMessage('error', 'Error al crear la especialidad.');
        }
        redirect('/admin/especialidades');
    }

    public function editar(): void {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/especialidades');
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/especialidades');
        }
        $id  = (int)($_POST['id'] ?? 0);
        $esp = $this->espModel->obtenerPorId($id);
        if (!$esp) {
            flashMessage('error', 'Especialidad no encontrada.');
            redirect('/admin/especialidades');
        }
        $data = [
            ':nombre'      => sanitize($_POST['nombre'] ?? ''),
            ':descripcion' => sanitize($_POST['descripcion'] ?? ''),
            ':icono'       => sanitize($_POST['icono'] ?? 'fa-stethoscope'),
        ];
        if ($this->espModel->actualizar($id, $data)) {
            AuditoriaModel::registrar([
                'accion'      => 'editar',
                'tabla'       => 'especialidades',
                'registro_id' => $id,
                'descripcion' => "Especialidad editada: {$data[':nombre']}",
                'datos_antes' => $esp ? ['nombre' => $esp['nombre']] : [],
                'datos_despues' => ['nombre' => $data[':nombre']],
            ]);
            flashMessage('success', 'Especialidad actualizada correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar la especialidad.');
        }
        redirect('/admin/especialidades');
    }

    public function eliminar(): void {
        AuthMiddleware::requireRole('administrador');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $esp = $this->espModel->obtenerPorId($id);
        $this->espModel->eliminar($id);
        AuditoriaModel::registrar([
            'accion'      => 'eliminar',
            'tabla'       => 'especialidades',
            'registro_id' => $id,
            'descripcion' => "Especialidad desactivada: " . ($esp['nombre'] ?? "#$id"),
        ]);
        flashMessage('success', 'Especialidad desactivada.');
        redirect('/admin/especialidades');
    }
}