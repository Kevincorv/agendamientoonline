<?php
class DashboardController {
    private Cita $citaModel;

    public function __construct() {
        $this->citaModel = new Cita();
    }

    public function index(): void {
        AuthMiddleware::requireRole('administrador', 'recepcionista');

        $stats        = $this->citaModel->estadisticasDashboard();
        $recientes    = $this->citaModel->obtenerProximas(8);

        // Nuevos indicadores
        $pacientesUnicos        = $this->citaModel->contarPacientesNuevos();
        $tasaCancelaciones      = $this->citaModel->tasaCancelaciones();
        $especialidadesTop      = $this->citaModel->especialidadesMasSolicitadas(5);
        $medicosTop             = $this->citaModel->medicosMasConsultas(5);
        $citasPorDia            = $this->citaModel->citasPorDiaSemana();

        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function statsJson(): void {
        AuthMiddleware::requireRole('administrador', 'recepcionista');
        header('Content-Type: application/json');

        $stats = $this->citaModel->estadisticasDashboard();
        echo json_encode([
            'success' => true,
            'stats'   => $stats,
        ]);
    }
}