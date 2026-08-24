<?php

class Horario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerPorMedico(int $medicoId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM horarios WHERE medico_id = :id AND activo = 1");
        $stmt->execute([':id' => $medicoId]);
        return $stmt->fetchAll();
    }

    public function generarSlots(int $medicoId, string $fecha): array
    {
        // Verificar si el médico está disponible
        $stmtDisp = $this->db->prepare("SELECT disponible FROM medicos WHERE id = :id");
        $stmtDisp->execute([':id' => $medicoId]);
        $medico = $stmtDisp->fetch();

        if (!$medico || $medico['disponible'] == 0) {
            return [[
                'hora'       => null,
                'disponible' => false,
                'mensaje'    => 'El medico no esta disponible por el momento.',
            ]];
        }

        // Verificar si la fecha es feriado
        $stmtFer = $this->db->prepare("SELECT COUNT(*) FROM feriados WHERE fecha = :fecha AND activo = 1");
        $stmtFer->execute([':fecha' => $fecha]);
        if ($stmtFer->fetchColumn() > 0) {
            return [[
                'hora'       => null,
                'disponible' => false,
                'mensaje'    => 'Fecha feriada — no hay atencion.',
            ]];
        }

        // Verificar si el medico tiene bloqueo en esa fecha
        $stmtBloq = $this->db->prepare("SELECT COUNT(*) FROM bloqueos_medico WHERE medico_id = :id AND fecha = :fecha");
        $stmtBloq->execute([':id' => $medicoId, ':fecha' => $fecha]);
        if ($stmtBloq->fetchColumn() > 0) {
            return [[
                'hora'       => null,
                'disponible' => false,
                'mensaje'    => 'El medico no atiende esta fecha.',
            ]];
        }

        $diaSemana = (int) date('w', strtotime($fecha));
        $stmt = $this->db->prepare(
            "SELECT * FROM horarios WHERE medico_id = :id AND dia_semana = :dia AND activo = 1"
        );
        $stmt->execute([':id' => $medicoId, ':dia' => $diaSemana]);
        $horarios = $stmt->fetchAll();

        $hoy = date('Y-m-d');
        $horaActual = date('H:i');

        $slots = [];
        $vistos = [];

        // Obtener todas las citas ocupadas para esta fecha de una sola vez
        $stmtOcup = $this->db->prepare(
            "SELECT hora FROM citas
             WHERE medico_id = :mid
               AND fecha     = :fecha
               AND estado_id NOT IN (3)"
        );
        $stmtOcup->execute([':mid' => $medicoId, ':fecha' => $fecha]);
        $ocupados = $stmtOcup->fetchAll(PDO::FETCH_COLUMN);

        foreach ($horarios as $h) {
            $inicio = strtotime($h['hora_inicio']);
            $fin    = strtotime($h['hora_fin']);

            $intervaloMin = isset($h['intervalo_minutos']) && (int)$h['intervalo_minutos'] > 0
                ? (int)$h['intervalo_minutos']
                : 30;
            $intervalo = $intervaloMin * 60;

            if (!$inicio || !$fin || $fin <= $inicio) {
                continue;
            }

            for ($t = $inicio; $t < $fin; $t += $intervalo) {
                $horaSlot = date('H:i', $t);

                // Evitar slots duplicados (solapamiento entre bloques)
                if (isset($vistos[$horaSlot])) {
                    continue;
                }
                $vistos[$horaSlot] = true;

                // Si es hoy, omitir horarios que ya pasaron
                if ($fecha === $hoy && $horaSlot <= $horaActual) {
                    continue;
                }

                $ocupado = in_array($horaSlot, $ocupados, true);
                $slots[] = [
                    'hora'       => $horaSlot,
                    'disponible' => !$ocupado,
                ];
            }
        }

        return $slots;
    }

    public function crear(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO horarios (medico_id, dia_semana, hora_inicio, hora_fin, intervalo_minutos)
            VALUES (:medico_id, :dia_semana, :hora_inicio, :hora_fin, :intervalo_minutos)"
        );
        return $stmt->execute($data);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM horarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}