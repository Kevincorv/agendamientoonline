<?php
class Cita {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crear(array $data): string|false {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO citas (medico_id, especialidad_id, nombre_paciente, telefono, email, motivo, fecha, hora, token_cancelacion, estado_id)
                VALUES (:medico_id, :especialidad_id, :nombre_paciente, :telefono, :email, :motivo, :fecha, :hora, :token, 1)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([
            ':medico_id'       => $data['medico_id'],
            ':especialidad_id' => $data['especialidad_id'],
            ':nombre_paciente' => $data['nombre_paciente'],
            ':telefono'        => $data['telefono'],
            ':email'           => $data['email'],
            ':motivo'          => $data['motivo'],
            ':fecha'           => $data['fecha'],
            ':hora'            => $data['hora'],
            ':token'           => $token,
        ])) {
            return $token;
        }
        return false;
    }

    public function obtenerPorToken(string $token): array|false {
        $stmt = $this->db->prepare("SELECT c.*, 
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad, 
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            LEFT JOIN medicos m ON c.medico_id = m.id
            LEFT JOIN especialidades e ON c.especialidad_id = e.id
            LEFT JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.token_cancelacion = :token");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    public function cancelarPorToken(string $token): bool {
        $stmt = $this->db->prepare("UPDATE citas SET estado_id = 3 WHERE token_cancelacion = :token AND estado_id NOT IN (3,4)");
        $stmt->execute([':token' => $token]);
        return $stmt->rowCount() > 0;
    }

    public function estaDisponible(int $medicoId, string $fecha, string $hora): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE medico_id = :medico_id AND fecha = :fecha AND hora = :hora AND estado_id NOT IN (3)");
        $stmt->execute([':medico_id' => $medicoId, ':fecha' => $fecha, ':hora' => $hora]);
        return $stmt->fetchColumn() == 0;
    }

    public function contarPorFechaYEstado(string $fecha, ?int $estadoId): int {
        $sql = "SELECT COUNT(*) FROM citas WHERE fecha = :fecha";
        $params = [':fecha' => $fecha];
        if ($estadoId !== null) {
            $sql .= " AND estado_id = :estado_id";
            $params[':estado_id'] = $estadoId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function obtenerPorFecha(string $fecha): array {
        $stmt = $this->db->prepare("SELECT c.*, 
                m.nombre as medico_nombre, m.apellido as medico_apellido,
                e.nombre as especialidad,
                ec.nombre as estado, ec.color as estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.fecha = :fecha
            ORDER BY c.hora ASC");
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodas(array $filtros = []): array {
        $sql = "SELECT c.*, 
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad, 
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE 1=1";
        $params = [];

        if (!empty($filtros['fecha'])) {
            $sql .= " AND c.fecha = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        if (!empty($filtros['medico_id'])) {
            $sql .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sql .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (c.nombre_paciente LIKE :q1 OR c.telefono LIKE :q2)";
            $params[':q1'] = '%' . $filtros['q'] . '%';
            $params[':q2'] = '%' . $filtros['q'] . '%';
        }
        $sql .= " ORDER BY c.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodasPaginado(array $filtros = [], int $pagina = 1, int $porPagina = 15): array {
        $sqlCount = "SELECT COUNT(*) FROM citas c WHERE 1=1";
        $sqlSelect = "SELECT c.*,
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad,
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE 1=1";
        $params = [];

        if (!empty($filtros['fecha'])) {
            $sqlCount .= " AND c.fecha = :fecha";
            $sqlSelect .= " AND c.fecha = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        if (!empty($filtros['medico_id'])) {
            $sqlCount .= " AND c.medico_id = :medico_id";
            $sqlSelect .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sqlCount .= " AND c.estado_id = :estado_id";
            $sqlSelect .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }
        if (!empty($filtros['q'])) {
            $sqlCount .= " AND (c.nombre_paciente LIKE :q1 OR c.telefono LIKE :q2)";
            $sqlSelect .= " AND (c.nombre_paciente LIKE :q1 OR c.telefono LIKE :q2)";
            $params[':q1'] = '%' . $filtros['q'] . '%';
            $params[':q2'] = '%' . $filtros['q'] . '%';
        }

        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();

        $offset = ($pagina - 1) * $porPagina;
        $sqlSelect .= " ORDER BY c.creado_en DESC LIMIT :limit OFFSET :offset";
        $stmtSelect = $this->db->prepare($sqlSelect);
        foreach ($params as $k => $v) {
            $stmtSelect->bindValue($k, $v);
        }
        $stmtSelect->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmtSelect->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmtSelect->execute();
        $datos = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        $paginas = (int)ceil($total / $porPagina);

        return [
            'datos'      => $datos,
            'total'      => $total,
            'pagina'     => $pagina,
            'porPagina'  => $porPagina,
            'paginas'    => $paginas,
        ];
    }

    public function cambiarEstado(int $id, int $estadoId, string $notas = ''): bool {
        $stmt = $this->db->prepare("UPDATE citas SET estado_id = :estado_id, notas_medico = :notas WHERE id = :id");
        return $stmt->execute([':estado_id' => $estadoId, ':notas' => $notas, ':id' => $id]);
    }

    public function estadisticasHoy(): array {
        $hoy = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT
            COUNT(*) AS total,
            SUM(estado_id = 1) AS pendientes,
            SUM(estado_id = 2) AS confirmadas,
            SUM(estado_id = 3) AS canceladas,
            SUM(estado_id = 4) AS atendidas
            FROM citas WHERE fecha = :hoy");
        $stmt->execute([':hoy' => $hoy]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function estadisticasDashboard(): array {
        $stmt = $this->db->query("SELECT
            COUNT(*) AS total,
            SUM(estado_id = 1) AS pendientes,
            SUM(estado_id = 2) AS confirmadas,
            SUM(estado_id = 3) AS canceladas
            FROM citas");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'       => (int)($res['total']       ?? 0),
            'pendientes'  => (int)($res['pendientes']  ?? 0),
            'confirmadas' => (int)($res['confirmadas'] ?? 0),
            'canceladas'  => (int)($res['canceladas']  ?? 0),
        ];
    }

    public function obtenerProximas(int $limite = 8): array {
        $hoy = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT c.*,
                m.nombre as medico_nombre, m.apellido as medico_apellido,
                e.nombre as especialidad,
                ec.nombre as estado, ec.color as estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.fecha >= :hoy
            ORDER BY c.fecha ASC, c.hora ASC
            LIMIT :limite");
        $stmt->bindValue(':hoy', $hoy, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $futuras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($futuras) < $limite) {
            $restantes = $limite - count($futuras);
            $stmt2 = $this->db->prepare("SELECT c.*,
                    m.nombre as medico_nombre, m.apellido as medico_apellido,
                    e.nombre as especialidad,
                    ec.nombre as estado, ec.color as estado_color
                FROM citas c
                JOIN medicos m ON c.medico_id = m.id
                JOIN especialidades e ON c.especialidad_id = e.id
                JOIN estados_citas ec ON c.estado_id = ec.id
                WHERE c.fecha < :hoy
                ORDER BY c.fecha DESC, c.hora DESC
                LIMIT :restantes");
            $stmt2->bindValue(':hoy', $hoy, PDO::PARAM_STR);
            $stmt2->bindValue(':restantes', $restantes, PDO::PARAM_INT);
            $stmt2->execute();
            $pasadas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            return array_merge($futuras, $pasadas);
        }

        return $futuras;
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT c.*,
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad,
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            LEFT JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function obtenerPorPacienteEmail(string $email, bool $onlyFuture = false, int $limit = 50): array
    {
        $sql = "SELECT c.*,
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad,
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            LEFT JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.email = :email";

        if ($onlyFuture) {
            $sql .= " AND c.fecha >= CURDATE() AND (c.fecha > CURDATE() OR c.hora >= CURTIME())";
        } else {
            $sql .= " AND (c.fecha < CURDATE() OR (c.fecha = CURDATE() AND c.hora < CURTIME()))";
        }

        $sql .= " ORDER BY c.fecha DESC, c.hora DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si es future, reordenar ascendente
        if ($onlyFuture) {
            usort($result, function($a, $b) {
                $cmp = strcmp($a['fecha'], $b['fecha']);
                if ($cmp !== 0) return $cmp;
                return strcmp($a['hora'], $b['hora']);
            });
        }

        return $result;
    }

    public function contarPacientesNuevos(): int
    {
        $stmt = $this->db->query("SELECT COUNT(DISTINCT email) FROM citas");
        return (int) $stmt->fetchColumn();
    }

    public function tasaCancelaciones(): float
    {
        $stmt = $this->db->query("SELECT
            COUNT(*) AS total,
            SUM(estado_id = 3) AS canceladas
            FROM citas");
        $res = $stmt->fetch();
        $total = (int)($res['total'] ?? 0);
        if ($total === 0) return 0;
        return round(((int)($res['canceladas'] ?? 0) / $total) * 100, 1);
    }

    public function especialidadesMasSolicitadas(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.nombre, COUNT(c.id) AS total
             FROM citas c
             JOIN especialidades e ON c.especialidad_id = e.id
             GROUP BY e.id, e.nombre
             ORDER BY total DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function medicosMasConsultas(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.nombre, m.apellido, e.nombre AS especialidad, COUNT(c.id) AS total
             FROM citas c
             JOIN medicos m ON c.medico_id = m.id
             LEFT JOIN especialidades e ON m.especialidad_id = e.id
             GROUP BY m.id, m.nombre, m.apellido, e.nombre
             ORDER BY total DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function citasPorDiaSemana(): array
    {
        $stmt = $this->db->query(
            "SELECT DAYOFWEEK(c.fecha) AS dia, COUNT(*) AS total
             FROM citas c
             WHERE c.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY dia
             ORDER BY dia"
        );
        $result = $stmt->fetchAll();
        $dias = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
        foreach ($result as $r) {
            $dias[(int)$r['dia']] = (int)$r['total'];
        }
        // Reordenar de Lun(2) a Dom(1) para el gráfico
        return [
            $dias[2], // Lun
            $dias[3], // Mar
            $dias[4], // Mié
            $dias[5], // Jue
            $dias[6], // Vie
            $dias[7], // Sáb
            $dias[1], // Dom
        ];
    }

    public function contarPorMedicoYEstado(int $medicoId, int $estadoId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM citas WHERE medico_id = :medico_id AND estado_id = :estado_id"
        );
        $stmt->execute([':medico_id' => $medicoId, ':estado_id' => $estadoId]);
        return (int) $stmt->fetchColumn();
    }

    public function reagendar(int $id, int $medicoId, string $fecha, string $hora): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE citas SET medico_id = :medico_id, fecha = :fecha, hora = :hora, estado_id = 1 WHERE id = :id"
        );
        return $stmt->execute([
            ':medico_id' => $medicoId,
            ':fecha'     => $fecha,
            ':hora'      => $hora,
            ':id'        => $id,
        ]);
    }

    public function cambiarMedico(int $citaId, int $nuevoMedicoId, string $nuevaFecha, string $nuevaHora): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE citas SET medico_id = :medico_id, fecha = :fecha, hora = :hora, estado_id = 1
             WHERE id = :id"
        );
        return $stmt->execute([
            ':medico_id' => $nuevoMedicoId,
            ':fecha'     => $nuevaFecha,
            ':hora'      => $nuevaHora,
            ':id'        => $citaId,
        ]);
    }

    public function obtenerPorMedico(int $medicoId, string $fecha = ''): array {
        $fecha = $fecha ?: date('Y-m-d');
        $stmt = $this->db->prepare("SELECT c.*, 
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE c.medico_id = :medico_id AND c.fecha = :fecha
            ORDER BY c.hora ASC");
        $stmt->execute([':medico_id' => $medicoId, ':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM citas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ── REPORTES ───────────────────────────────────────────

    public function reportesStats(array $filtros): array {
        $sqlWhere = " WHERE 1=1";
        $params = [];

        if (!empty($filtros['desde'])) {
            $sqlWhere .= " AND c.fecha >= :desde";
            $params[':desde'] = $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $sqlWhere .= " AND c.fecha <= :hasta";
            $params[':hasta'] = $filtros['hasta'];
        }
        if (!empty($filtros['medico_id'])) {
            $sqlWhere .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }
        if (!empty($filtros['especialidad_id'])) {
            $sqlWhere .= " AND c.especialidad_id = :especialidad_id";
            $params[':especialidad_id'] = $filtros['especialidad_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sqlWhere .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }

        $stmt = $this->db->prepare("SELECT
            COUNT(*) AS total,
            SUM(c.estado_id = 1) AS pendientes,
            SUM(c.estado_id = 2) AS confirmadas,
            SUM(c.estado_id = 3) AS canceladas,
            SUM(c.estado_id = 4) AS atendidas
            FROM citas c{$sqlWhere}");
        $stmt->execute($params);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $this->db->prepare("SELECT COUNT(DISTINCT c.email) AS pacientes FROM citas c{$sqlWhere}");
        $stmt2->execute($params);
        $pacientes = (int)$stmt2->fetchColumn();

        return [
            'total'       => (int)($stats['total']       ?? 0),
            'pendientes'  => (int)($stats['pendientes']  ?? 0),
            'confirmadas' => (int)($stats['confirmadas'] ?? 0),
            'canceladas'  => (int)($stats['canceladas']  ?? 0),
            'atendidas'   => (int)($stats['atendidas']   ?? 0),
            'pacientes'   => $pacientes,
        ];
    }

    public function reportesPorMedico(array $filtros): array {
        $sqlWhere = " WHERE 1=1";
        $params = [];

        if (!empty($filtros['desde'])) {
            $sqlWhere .= " AND c.fecha >= :desde";
            $params[':desde'] = $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $sqlWhere .= " AND c.fecha <= :hasta";
            $params[':hasta'] = $filtros['hasta'];
        }
        if (!empty($filtros['medico_id'])) {
            $sqlWhere .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }
        if (!empty($filtros['especialidad_id'])) {
            $sqlWhere .= " AND c.especialidad_id = :especialidad_id";
            $params[':especialidad_id'] = $filtros['especialidad_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sqlWhere .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }

        $stmt = $this->db->prepare("SELECT
                m.id, m.nombre, m.apellido, e.nombre AS especialidad,
                COUNT(c.id) AS total,
                SUM(c.estado_id = 4) AS atendidas,
                SUM(c.estado_id = 3) AS canceladas
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            LEFT JOIN especialidades e ON m.especialidad_id = e.id
            {$sqlWhere}
            GROUP BY m.id, m.nombre, m.apellido, e.nombre
            ORDER BY total DESC");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportesPorEspecialidad(array $filtros): array {
        $sqlWhere = " WHERE 1=1";
        $params = [];

        if (!empty($filtros['desde'])) {
            $sqlWhere .= " AND c.fecha >= :desde";
            $params[':desde'] = $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $sqlWhere .= " AND c.fecha <= :hasta";
            $params[':hasta'] = $filtros['hasta'];
        }
        if (!empty($filtros['especialidad_id'])) {
            $sqlWhere .= " AND c.especialidad_id = :especialidad_id";
            $params[':especialidad_id'] = $filtros['especialidad_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sqlWhere .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }

        $stmt = $this->db->prepare("SELECT
                e.id, e.nombre,
                COUNT(c.id) AS total,
                SUM(c.estado_id = 4) AS atendidas,
                SUM(c.estado_id = 3) AS canceladas
            FROM citas c
            JOIN especialidades e ON c.especialidad_id = e.id
            {$sqlWhere}
            GROUP BY e.id, e.nombre
            ORDER BY total DESC");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportesCitas(array $filtros, int $pagina = 1, int $porPagina = 30): array {
        $sqlCount = "SELECT COUNT(*) FROM citas c WHERE 1=1";
        $sqlSelect = "SELECT c.*,
                m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                e.nombre AS especialidad,
                ec.nombre AS estado, ec.color AS estado_color
            FROM citas c
            JOIN medicos m ON c.medico_id = m.id
            JOIN especialidades e ON c.especialidad_id = e.id
            JOIN estados_citas ec ON c.estado_id = ec.id
            WHERE 1=1";
        $params = [];

        if (!empty($filtros['desde'])) {
            $sqlCount .= " AND c.fecha >= :desde";
            $sqlSelect .= " AND c.fecha >= :desde";
            $params[':desde'] = $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $sqlCount .= " AND c.fecha <= :hasta";
            $sqlSelect .= " AND c.fecha <= :hasta";
            $params[':hasta'] = $filtros['hasta'];
        }
        if (!empty($filtros['medico_id'])) {
            $sqlCount .= " AND c.medico_id = :medico_id";
            $sqlSelect .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }
        if (!empty($filtros['especialidad_id'])) {
            $sqlCount .= " AND c.especialidad_id = :especialidad_id";
            $sqlSelect .= " AND c.especialidad_id = :especialidad_id";
            $params[':especialidad_id'] = $filtros['especialidad_id'];
        }
        if (!empty($filtros['estado_id'])) {
            $sqlCount .= " AND c.estado_id = :estado_id";
            $sqlSelect .= " AND c.estado_id = :estado_id";
            $params[':estado_id'] = $filtros['estado_id'];
        }
        if (!empty($filtros['q'])) {
            $sqlCount .= " AND (c.nombre_paciente LIKE :q1 OR c.telefono LIKE :q2)";
            $sqlSelect .= " AND (c.nombre_paciente LIKE :q1 OR c.telefono LIKE :q2)";
            $params[':q1'] = '%' . $filtros['q'] . '%';
            $params[':q2'] = '%' . $filtros['q'] . '%';
        }

        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();

        $offset = ($pagina - 1) * $porPagina;
        $sqlSelect .= " ORDER BY c.creado_en DESC LIMIT :limit OFFSET :offset";
        $stmtSelect = $this->db->prepare($sqlSelect);
        foreach ($params as $k => $v) {
            $stmtSelect->bindValue($k, $v);
        }
        $stmtSelect->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmtSelect->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmtSelect->execute();
        $datos = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

        return [
            'datos'     => $datos,
            'total'     => $total,
            'pagina'    => $pagina,
            'porPagina' => $porPagina,
            'paginas'   => (int)ceil($total / $porPagina),
        ];
    }

    public function exportarCitasCSV(array $filtros): string {
        $citas = $this->reportesCitas($filtros, 1, 99999);
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Paciente', 'Telefono', 'Email', 'Medico', 'Especialidad', 'Fecha', 'Hora', 'Estado', 'Motivo']);

        foreach ($citas['datos'] as $c) {
            fputcsv($output, [
                $c['nombre_paciente'],
                $c['telefono'],
                $c['email'],
                "Dr. {$c['medico_nombre']} {$c['medico_apellido']}",
                $c['especialidad'],
                $c['fecha'],
                $c['hora'],
                $c['estado'],
                $c['motivo'],
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }
}
