<?php

class BloqueoMedico
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerPorMedico(int $medicoId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM bloqueos_medico WHERE medico_id = :id ORDER BY fecha DESC");
        $stmt->execute([':id' => $medicoId]);
        return $stmt->fetchAll();
    }

    public function obtenerTodos(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, m.nombre AS medico_nombre, m.apellido AS medico_apellido
             FROM bloqueos_medico b
             JOIN medicos m ON b.medico_id = m.id
             ORDER BY b.fecha DESC"
        );
        return $stmt->fetchAll();
    }

    public function estaBloqueado(int $medicoId, string $fecha): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM bloqueos_medico WHERE medico_id = :id AND fecha = :fecha"
        );
        $stmt->execute([':id' => $medicoId, ':fecha' => $fecha]);
        return $stmt->fetchColumn() > 0;
    }

    public function crear(int $medicoId, string $fecha, string $motivo = ''): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO bloqueos_medico (medico_id, fecha, motivo) VALUES (:medico_id, :fecha, :motivo)"
        );
        return $stmt->execute([':medico_id' => $medicoId, ':fecha' => $fecha, ':motivo' => $motivo]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bloqueos_medico WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
