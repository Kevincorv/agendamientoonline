<?php
class Medico {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array {
        return $this->db->query("SELECT m.*, e.nombre AS especialidad
            FROM medicos m JOIN especialidades e ON m.especialidad_id = e.id
            WHERE m.activo = 1 ORDER BY m.apellido ASC")->fetchAll();
    }

    public function obtenerPorEspecialidad(int $espId): array {
        $stmt = $this->db->prepare("SELECT * FROM medicos WHERE especialidad_id = :esp_id AND activo = 1 AND disponible = 1");
        $stmt->execute([':esp_id' => $espId]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT m.*, e.nombre AS especialidad
            FROM medicos m JOIN especialidades e ON m.especialidad_id = e.id WHERE m.id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO medicos (nombre, apellido, email, telefono, especialidad_id, matricula, descripcion)
            VALUES (:nombre, :apellido, :email, :telefono, :especialidad_id, :matricula, :descripcion)");
        return $stmt->execute($data);
    }

    public function actualizar(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE medicos SET nombre=:nombre, apellido=:apellido, email=:email,
            telefono=:telefono, especialidad_id=:especialidad_id, matricula=:matricula, descripcion=:descripcion WHERE id=:id");
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("UPDATE medicos SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function toggleDisponible(int $id): bool {
        $stmt = $this->db->prepare("UPDATE medicos SET disponible = IF(disponible = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerPorUsuarioId(int $userId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM medicos WHERE usuario_id = :uid");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetch();
    }
}
