<?php
require_once(__DIR__ . '/../application/Database.php');

class CuentaModel extends Model {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }

    /**
     * Crear una nueva cuenta
     */
    public function crearCuenta($usuario_id, $nombre, $tipo, $saldo_inicial, $moneda) {
        if (!$this->conn) return false;
        if (empty($usuario_id) || empty($nombre) || empty($tipo) || empty($moneda)) {
            return false;
        }
        
        try {
            $sql = "INSERT INTO cuentas (usuario_id, nombre, tipo, saldo_inicial, moneda, activa) 
                    VALUES (:usuario_id, :nombre, :tipo, :saldo_inicial, :moneda, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':saldo_inicial', $saldo_inicial);
            $stmt->bindParam(':moneda', $moneda);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear cuenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todas las cuentas de un usuario
     */
    public function listarCuentas($usuario_id) {
        if (!$this->conn) return [];
        try {
            $sql = "SELECT * FROM cuentas WHERE usuario_id = :usuario_id AND activa = 1 ORDER BY fecha_creacion DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar cuentas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener cuenta por ID
     */
    public function obtenerPorId($id, $usuario_id) {
        if (!$this->conn) return null;
        try {
            $sql = "SELECT * FROM cuentas WHERE id = :id AND usuario_id = :usuario_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cuenta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar cuenta
     */
    public function actualizarCuenta($id, $usuario_id, $nombre, $tipo, $saldo_inicial, $moneda) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id) || empty($nombre) || empty($tipo) || empty($moneda)) {
            return false;
        }
        try {
            $sql = "UPDATE cuentas SET nombre = :nombre, tipo = :tipo, saldo_inicial = :saldo_inicial, 
                    moneda = :moneda WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':saldo_inicial', $saldo_inicial);
            $stmt->bindParam(':moneda', $moneda);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar cuenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar cuenta (soft delete - marcar como inactiva)
     */
    public function eliminarCuenta($id, $usuario_id) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id)) return false;
        
        try {
            $sql = "UPDATE cuentas SET activa = 0 WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar cuenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcular saldo actual de una cuenta
     * Saldo = Saldo inicial + Ingresos - Gastos
     */
    public function calcularSaldo($cuenta_id, $usuario_id) {
        if (!$this->conn) return 0;
        
        try {
            // Obtener saldo inicial
            $sqlCuenta = "SELECT saldo_inicial FROM cuentas WHERE id = :cuenta_id AND usuario_id = :usuario_id";
            $stmtCuenta = $this->conn->prepare($sqlCuenta);
            $stmtCuenta->bindParam(':cuenta_id', $cuenta_id, PDO::PARAM_INT);
            $stmtCuenta->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtCuenta->execute();
            $cuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC);
            
            if (!$cuenta) return 0;
            
            $saldo_inicial = (float)$cuenta['saldo_inicial'];
            
            // Sumar ingresos
            $sqlIngresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos 
                           WHERE cuenta_id = :cuenta_id AND usuario_id = :usuario_id";
            $stmtIngresos = $this->conn->prepare($sqlIngresos);
            $stmtIngresos->bindParam(':cuenta_id', $cuenta_id, PDO::PARAM_INT);
            $stmtIngresos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtIngresos->execute();
            $ingresos = $stmtIngresos->fetch(PDO::FETCH_ASSOC);
            $total_ingresos = (float)$ingresos['total'];
            
            // Sumar gastos
            $sqlGastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos 
                         WHERE cuenta_id = :cuenta_id AND usuario_id = :usuario_id";
            $stmtGastos = $this->conn->prepare($sqlGastos);
            $stmtGastos->bindParam(':cuenta_id', $cuenta_id, PDO::PARAM_INT);
            $stmtGastos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtGastos->execute();
            $gastos = $stmtGastos->fetch(PDO::FETCH_ASSOC);
            $total_gastos = (float)$gastos['total'];
            
            return $saldo_inicial + $total_ingresos - $total_gastos;
        } catch (PDOException $e) {
            error_log("Error al calcular saldo: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener la moneda de una cuenta
     */
    public function obtenerMoneda($cuenta_id, $usuario_id) {
        if (!$this->conn) return 'PEN';
        try {
            $sql = "SELECT moneda FROM cuentas WHERE id = :cuenta_id AND usuario_id = :usuario_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cuenta_id', $cuenta_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['moneda'] : 'PEN';
        } catch (PDOException $e) {
            error_log("Error al obtener moneda: " . $e->getMessage());
            return 'PEN';
        }
    }
}

