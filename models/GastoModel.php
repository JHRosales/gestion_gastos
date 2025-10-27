
<?php
require_once(__DIR__ . '/../application/Database.php');

class GastoModel extends Model {
    private $conn;
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }
    public function crearGasto($usuario_id, $monto, $categoria, $descripcion, $fecha) {
        if (!$this->conn) return false;
        if (empty($usuario_id) || empty($monto) || empty($categoria) || empty($fecha)) {
            return false;
        }
        try {
            $sql = "INSERT INTO gastos (usuario_id, monto, categoria, descripcion, fecha) VALUES (:usuario_id, :monto, :categoria, :descripcion, :fecha)";
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
    public function listarGastos($usuario_id) {
        if (!$this->conn) return [];
        try {
            $sql = "SELECT * FROM gastos WHERE usuario_id = :usuario_id ORDER BY fecha DESC";
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
            $sql = "SELECT * FROM gastos WHERE id = :id AND usuario_id = :usuario_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    public function actualizarGasto($id, $usuario_id, $monto, $categoria, $descripcion, $fecha) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id) || empty($monto) || empty($categoria) || empty($fecha)) {
            return false;
        }
        try {
            $sql = "UPDATE gastos SET monto = :monto, categoria = :categoria, descripcion = :descripcion, fecha = :fecha WHERE id = :id AND usuario_id = :usuario_id";
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
    public function eliminarGasto($id, $usuario_id) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id)) return false;
        try {
            $sql = "DELETE FROM gastos WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
