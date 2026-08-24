<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\AuditoriaModel;
use App\Core\Session;

class AuditoriaController
{
    private AuditoriaModel $model;

    public function __construct()
    {
        $this->model = new AuditoriaModel();
    }

    /** GET /admin/auditoria */
    public function index(): void
    {
        AuthMiddleware::requirePermiso('auditoria.ver');

        $filtros = [
            'usuario_id'  => $_GET['usuario_id']  ?? null,
            'accion'      => $_GET['accion']       ?? null,
            'tabla'       => $_GET['tabla']        ?? null,
            'fecha_desde' => $_GET['fecha_desde']  ?? null,
            'fecha_hasta' => $_GET['fecha_hasta']  ?? null,
            'ip'          => $_GET['ip']           ?? null,
        ];

        $pagina   = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->model->getLogs($filtros, $pagina);

        $csrfToken = Session::generateCsrf();
        require_once VIEW_PATH . '/admin/auditoria.php';
    }

    /** GET /admin/auditoria/{id} */
    public function detalle(int $id): void
    {
        AuthMiddleware::requirePermiso('auditoria.ver');

        $log = $this->model->getLog($id);
        if (!$log) {
            Session::setFlash('error', 'Log no encontrado.');
            header('Location: ' . APP_URL . '/admin/auditoria');
            exit;
        }

        $csrfToken = Session::generateCsrf();
        require_once VIEW_PATH . '/admin/auditoria_detalle.php';
    }
}