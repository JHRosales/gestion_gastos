
<?php

class IngresoModel extends Model {
    private $conn;
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }
    public function crearIngreso($usuario_id, $monto, $categoria, $descripcion, $fecha) {
        if (!$this->conn) return false;
        if (empty($usuario_id) || empty($monto) || empty($categoria) || empty($fecha)) {
            return false;
        }
        try {
            $sql = "INSERT INTO ingresos (usuario_id, monto, categoria, descripcion, fecha) VALUES (:usuario_id, :monto, :categoria, :descripcion, :fecha)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fecha', $fecha);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    public function listarIngresos($usuario_id) {
        if (!$this->conn) return [];
        try {
            $sql = "SELECT * FROM ingresos WHERE usuario_id = :usuario_id ORDER BY fecha DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function obtenerPorId($id, $usuario_id) {
        if (!$this->conn) return null;
        try {
            $sql = "SELECT * FROM ingresos WHERE id = :id AND usuario_id = :usuario_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    public function actualizarIngreso($id, $usuario_id, $monto, $categoria, $descripcion, $fecha) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id) || empty($monto) || empty($categoria) || empty($fecha)) {
            return false;
        }
        try {
            $sql = "UPDATE ingresos SET monto = :monto, categoria = :categoria, descripcion = :descripcion, fecha = :fecha WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    public function eliminarIngreso($id, $usuario_id) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id)) return false;
        try {
            $sql = "DELETE FROM ingresos WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
