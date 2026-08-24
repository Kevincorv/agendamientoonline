<?php

class NotificacionController
{
    private Notificacion $notifModel;

    public function __construct()
    {
        $this->notifModel = new Notificacion();
    }

    public function json(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        $userId = $_SESSION['usuario_id'] ?? null;
        $notifs = $this->notifModel->obtenerNoLeidas($userId, 10);
        $count  = $this->notifModel->contarNoLeidas($userId);

        echo json_encode([
            'success' => true,
            'count'   => $count,
            'data'    => $notifs,
        ]);
    }

    public function marcarLeidas(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        $this->notifModel->marcarLeidas();
        echo json_encode(['success' => true]);
    }

    public function marcarLeida(): void
    {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->notifModel->marcarLeida($id);
        }
        echo json_encode(['success' => true]);
    }
}
