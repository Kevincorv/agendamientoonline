<?php

class Feriado
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $stmt = $this->db->query("SELECT * FROM feriados ORDER BY fecha DESC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM feriados WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function esFeriado(string $fecha): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM feriados WHERE fecha = :fecha AND activo = 1");
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchColumn() > 0;
    }

    public function crear(string $fecha, string $motivo): bool
    {
        $stmt = $this->db->prepare("INSERT INTO feriados (fecha, motivo) VALUES (:fecha, :motivo)");
        return $stmt->execute([':fecha' => $fecha, ':motivo' => $motivo]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM feriados WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function toggle(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE feriados SET activo = NOT activo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
