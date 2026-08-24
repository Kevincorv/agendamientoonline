<?php
class Especialidad {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodas(): array {
        return $this->db->query("SELECT * FROM especialidades WHERE activo = 1 ORDER BY nombre ASC")->fetchAll();
    }

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM especialidades WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO especialidades (nombre, descripcion, icono) VALUES (:nombre, :descripcion, :icono)");
        return $stmt->execute($data);
    }

    public function actualizar(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE especialidades SET nombre=:nombre, descripcion=:descripcion, icono=:icono WHERE id=:id");
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("UPDATE especialidades SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
