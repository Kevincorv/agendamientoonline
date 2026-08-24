<?php

class SistemaController
{
    private Cita $citaModel;
    private Medico $medicoModel;
    private Especialidad $espModel;

    public function __construct()
    {
        $this->citaModel    = new Cita();
        $this->medicoModel  = new Medico();
        $this->espModel     = new Especialidad();
    }

    public function auditoria(): void
    {
        AuthMiddleware::requireRole('administrador');
        $model      = new AuditoriaModel();
        $pagina     = max(1, (int)($_GET['pagina'] ?? 1));
        $filtros    = [
            'usuario_id'  => $_GET['usuario_id']  ?? null,
            'accion'      => $_GET['accion']       ?? null,
            'tabla'       => $_GET['tabla']        ?? null,
            'fecha_desde' => $_GET['fecha_desde']  ?? null,
            'fecha_hasta' => $_GET['fecha_hasta']  ?? null,
            'ip'          => $_GET['ip']           ?? null,
        ];
        $resultado  = $model->getLogs($filtros, $pagina);
        $csrfToken  = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/auditoria_listado.php';
    }

    public function reportes(): void
    {
        AuthMiddleware::requireRole('administrador');
        $csrfToken = generateCsrfToken();

        $filtros = [
            'desde'           => sanitize($_GET['desde']           ?? date('Y-m-01')),
            'hasta'           => sanitize($_GET['hasta']           ?? date('Y-m-d')),
            'medico_id'       => (int)($_GET['medico_id']          ?? 0),
            'especialidad_id' => (int)($_GET['especialidad_id']    ?? 0),
            'estado_id'       => (int)($_GET['estado_id']          ?? 0),
            'q'               => sanitize($_GET['q']               ?? ''),
        ];

        $tab = sanitize($_GET['tab'] ?? 'resumen');

        $stats = $this->citaModel->reportesStats($filtros);
        $porMedico = $this->citaModel->reportesPorMedico($filtros);
        $porEspecialidad = $this->citaModel->reportesPorEspecialidad($filtros);

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $citasPaginado = $this->citaModel->reportesCitas($filtros, $pagina);
        $medicos = $this->medicoModel->obtenerTodos();
        $especialidades = $this->espModel->obtenerTodas();

        require_once __DIR__ . '/../views/admin/reportes.php';
    }

    public function reportesExportarCSV(): void
    {
        AuthMiddleware::requireRole('administrador');

        $filtros = [
            'desde'           => sanitize($_GET['desde']           ?? date('Y-m-01')),
            'hasta'           => sanitize($_GET['hasta']           ?? date('Y-m-d')),
            'medico_id'       => (int)($_GET['medico_id']          ?? 0),
            'especialidad_id' => (int)($_GET['especialidad_id']    ?? 0),
            'estado_id'       => (int)($_GET['estado_id']          ?? 0),
            'q'               => sanitize($_GET['q']               ?? ''),
        ];

        $csv = $this->citaModel->exportarCitasCSV($filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_citas_' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF"; // BOM UTF-8
        echo $csv;
        exit;
    }

    public function backups(): void
    {
        AuthMiddleware::requireRole('administrador');
        $csrfToken = generateCsrfToken();

        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backups = [];
        $files = glob($backupDir . '/*.sql');
        if ($files) {
            foreach ($files as $file) {
                $backups[] = [
                    'nombre'   => basename($file),
                    'ruta'     => $file,
                    'tamano'   => filesize($file),
                    'fecha'    => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }
        usort($backups, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));

        require_once __DIR__ . '/../views/admin/backups.php';
    }

    public function backupsCrear(): void
    {
        AuthMiddleware::requireRole('administrador');

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Error de seguridad CSRF.');
            redirect('/admin/backups');
        }

        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $output = "-- Backup generado el " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Base de datos: " . DB_NAME . "\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                $stmtCreate = $db->query("SHOW CREATE TABLE `{$table}`");
                $row = $stmtCreate->fetch(PDO::FETCH_NUM);
                $output .= "\n-- Estructura de tabla `{$table}`\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $row[1] . ";\n\n";

                $stmtData = $db->query("SELECT * FROM `{$table}`");
                $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);
                if (empty($rows)) continue;

                $columns = array_keys($rows[0]);
                $output .= "-- Datos de tabla `{$table}`\n";

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . str_replace("'", "''", $val) . "'";
                        }
                    }
                    $output .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }

            $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            file_put_contents($filepath, $output);
            AuditoriaModel::registrar([
                'accion'      => 'crear_backup',
                'tabla'       => 'backups',
                'descripcion' => "Backup creado: {$filename}",
            ]);
            flashMessage('success', "Backup creado exitosamente: {$filename}");
        } catch (Exception $e) {
            flashMessage('error', 'Error al crear backup: ' . $e->getMessage());
        }

        redirect('/admin/backups');
    }

    public function backupsDescargar(): void
    {
        AuthMiddleware::requireRole('administrador');

        $nombre = basename($_GET['archivo'] ?? '');
        if (!$nombre) {
            flashMessage('error', 'Archivo no especificado.');
            redirect('/admin/backups');
        }

        $filepath = __DIR__ . '/../backups/' . $nombre;
        if (!file_exists($filepath)) {
            flashMessage('error', 'El archivo no existe.');
            redirect('/admin/backups');
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    public function backupsEliminar(): void
    {
        AuthMiddleware::requireRole('administrador');

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Error de seguridad CSRF.');
            redirect('/admin/backups');
        }

        $nombre = basename($_POST['archivo'] ?? '');
        if (!$nombre) {
            flashMessage('error', 'Archivo no especificado.');
            redirect('/admin/backups');
        }

        $filepath = __DIR__ . '/../backups/' . $nombre;
        if (file_exists($filepath) && unlink($filepath)) {
            AuditoriaModel::registrar([
                'accion'      => 'eliminar_backup',
                'tabla'       => 'backups',
                'descripcion' => "Backup eliminado: {$nombre}",
            ]);
            flashMessage('success', "Backup eliminado: {$nombre}");
        } else {
            flashMessage('error', 'No se pudo eliminar el archivo.');
        }

        redirect('/admin/backups');
    }

    public function backupsRestaurar(): void
    {
        AuthMiddleware::requireRole('administrador');

        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Error de seguridad CSRF.');
            redirect('/admin/backups');
        }

        $nombre = basename($_POST['archivo'] ?? '');
        if (!$nombre) {
            flashMessage('error', 'Archivo no especificado.');
            redirect('/admin/backups');
        }

        $filepath = __DIR__ . '/../backups/' . $nombre;
        if (!file_exists($filepath)) {
            flashMessage('error', 'El archivo de backup no existe.');
            redirect('/admin/backups');
        }

        try {
            $sql = file_get_contents($filepath);
            $db = Database::getInstance()->getConnection();
            $db->exec($sql);

            AuditoriaModel::registrar([
                'accion'      => 'restaurar_backup',
                'tabla'       => 'backups',
                'descripcion' => "Base de datos restaurada desde: {$nombre}",
            ]);
            flashMessage('success', "Base de datos restaurada exitosamente desde: {$nombre}");
        } catch (Exception $e) {
            flashMessage('error', 'Error al restaurar backup: ' . $e->getMessage());
        }

        redirect('/admin/backups');
    }
}
