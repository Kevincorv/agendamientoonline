<?php

class FeriadoController
{
    private Feriado $feriadoModel;

    public function __construct()
    {
        $this->feriadoModel = new Feriado();
    }

    public function listado(): void
    {
        AuthMiddleware::requireRole('administrador');
        $feriados  = $this->feriadoModel->obtenerTodos();
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/feriados.php';
    }

    public function crear(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/feriados');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF invalido.');
            redirect('/admin/feriados');
        }

        $fecha  = sanitize($_POST['fecha'] ?? '');
        $motivo = sanitize($_POST['motivo'] ?? '');

        if (!$fecha || !$motivo) {
            flashMessage('error', 'Completa todos los campos.');
            redirect('/admin/feriados');
        }

        if ($this->feriadoModel->crear($fecha, $motivo)) {
            AuditoriaModel::registrar([
                'accion'      => 'crear',
                'tabla'       => 'feriados',
                'descripcion' => "Feriado creado: {$fecha} - {$motivo}",
            ]);
            Notificacion::crear("Nuevo feriado registrado", "{$motivo} - {$fecha}", 'info');
            flashMessage('success', 'Feriado agregado correctamente.');
        } else {
            flashMessage('error', 'Error al agregar el feriado (puede que ya exista).');
        }
        redirect('/admin/feriados');
    }

    public function eliminar(): void
    {
        AuthMiddleware::requireRole('administrador');
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id && $this->feriadoModel->eliminar($id)) {
            AuditoriaModel::registrar([
                'accion'      => 'eliminar',
                'tabla'       => 'feriados',
                'registro_id' => $id,
                'descripcion' => "Feriado #{$id} eliminado",
            ]);
            flashMessage('success', 'Feriado eliminado.');
        } else {
            flashMessage('error', 'Error al eliminar el feriado.');
        }
        redirect('/admin/feriados');
    }

    public function toggle(): void
    {
        AuthMiddleware::requireRole('administrador');
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $this->feriadoModel->toggle($id);
        redirect('/admin/feriados');
    }
}
