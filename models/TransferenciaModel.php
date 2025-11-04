<?php
require_once(__DIR__ . '/../application/Database.php');

class TransferenciaModel extends Model {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }

    /**
     * Crear una transferencia con transacción atómica
     * Crea el registro de transferencia, el gasto en cuenta origen y el ingreso en cuenta destino
     */
    public function crearTransferencia($usuario_id, $cuenta_origen_id, $cuenta_destino_id, $monto, $descripcion, $fecha) {
        if (!$this->conn) return false;
        
        // Validaciones
        if (empty($usuario_id) || empty($cuenta_origen_id) || empty($cuenta_destino_id) || empty($monto) || empty($fecha)) {
            return false;
        }
        
        if ($cuenta_origen_id == $cuenta_destino_id) {
            return false; // No se puede transferir a la misma cuenta
        }
        
        if ($monto <= 0) {
            return false; // El monto debe ser positivo
        }
        
        // Verificar que las cuentas existen y pertenecen al usuario
        $sqlCuentaOrigen = "SELECT nombre FROM cuentas WHERE id = :id AND usuario_id = :usuario_id";
        $stmtCuentaOrigen = $this->conn->prepare($sqlCuentaOrigen);
        $stmtCuentaOrigen->bindParam(':id', $cuenta_origen_id, PDO::PARAM_INT);
        $stmtCuentaOrigen->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmtCuentaOrigen->execute();
        $cuentaOrigen = $stmtCuentaOrigen->fetch(PDO::FETCH_ASSOC);
        
        $sqlCuentaDestino = "SELECT nombre FROM cuentas WHERE id = :id AND usuario_id = :usuario_id";
        $stmtCuentaDestino = $this->conn->prepare($sqlCuentaDestino);
        $stmtCuentaDestino->bindParam(':id', $cuenta_destino_id, PDO::PARAM_INT);
        $stmtCuentaDestino->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmtCuentaDestino->execute();
        $cuentaDestino = $stmtCuentaDestino->fetch(PDO::FETCH_ASSOC);
        
        if (!$cuentaOrigen || !$cuentaDestino) {
            return false; // Las cuentas deben existir y pertenecer al usuario
        }
        
        $nombreOrigen = $cuentaOrigen['nombre'];
        $nombreDestino = $cuentaDestino['nombre'];
        
        // Iniciar transacción
        $this->conn->beginTransaction();
        
        try {
            // 1. Crear registro de transferencia (sin gasto_id e ingreso_id aún)
            $sqlTransferencia = "INSERT INTO transferencias (usuario_id, cuenta_origen_id, cuenta_destino_id, monto, descripcion, fecha) 
                                VALUES (:usuario_id, :cuenta_origen_id, :cuenta_destino_id, :monto, :descripcion, :fecha)";
            $stmtTransferencia = $this->conn->prepare($sqlTransferencia);
            $stmtTransferencia->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtTransferencia->bindParam(':cuenta_origen_id', $cuenta_origen_id, PDO::PARAM_INT);
            $stmtTransferencia->bindParam(':cuenta_destino_id', $cuenta_destino_id, PDO::PARAM_INT);
            $stmtTransferencia->bindParam(':monto', $monto);
            $stmtTransferencia->bindParam(':descripcion', $descripcion);
            $stmtTransferencia->bindParam(':fecha', $fecha);
            
            if (!$stmtTransferencia->execute()) {
                throw new Exception("Error al crear registro de transferencia");
            }
            
            $transferencia_id = $this->conn->lastInsertId();
            
            // 2. Crear gasto en cuenta origen
            $descripcionGasto = "Transferencia a " . $nombreDestino;
            if (!empty($descripcion)) {
                $descripcionGasto .= " - " . $descripcion;
            }
            
            $sqlGasto = "INSERT INTO gastos (usuario_id, cuenta_id, es_transferencia, transferencia_id, monto, categoria, descripcion, fecha) 
                        VALUES (:usuario_id, :cuenta_id, 1, :transferencia_id, :monto, 'Transferencia', :descripcion, :fecha)";
            $stmtGasto = $this->conn->prepare($sqlGasto);
            $stmtGasto->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtGasto->bindParam(':cuenta_id', $cuenta_origen_id, PDO::PARAM_INT);
            $stmtGasto->bindParam(':transferencia_id', $transferencia_id, PDO::PARAM_INT);
            $stmtGasto->bindParam(':monto', $monto);
            $stmtGasto->bindParam(':descripcion', $descripcionGasto);
            $stmtGasto->bindParam(':fecha', $fecha);
            
            if (!$stmtGasto->execute()) {
                throw new Exception("Error al crear gasto");
            }
            
            $gasto_id = $this->conn->lastInsertId();
            
            // 3. Crear ingreso en cuenta destino
            $descripcionIngreso = "Transferencia desde " . $nombreOrigen;
            if (!empty($descripcion)) {
                $descripcionIngreso .= " - " . $descripcion;
            }
            
            $sqlIngreso = "INSERT INTO ingresos (usuario_id, cuenta_id, es_transferencia, transferencia_id, monto, categoria, descripcion, fecha) 
                          VALUES (:usuario_id, :cuenta_id, 1, :transferencia_id, :monto, 'Transferencia', :descripcion, :fecha)";
            $stmtIngreso = $this->conn->prepare($sqlIngreso);
            $stmtIngreso->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtIngreso->bindParam(':cuenta_id', $cuenta_destino_id, PDO::PARAM_INT);
            $stmtIngreso->bindParam(':transferencia_id', $transferencia_id, PDO::PARAM_INT);
            $stmtIngreso->bindParam(':monto', $monto);
            $stmtIngreso->bindParam(':descripcion', $descripcionIngreso);
            $stmtIngreso->bindParam(':fecha', $fecha);
            
            if (!$stmtIngreso->execute()) {
                throw new Exception("Error al crear ingreso");
            }
            
            $ingreso_id = $this->conn->lastInsertId();
            
            // 4. Actualizar transferencia con los IDs de gasto e ingreso
            $sqlUpdate = "UPDATE transferencias SET gasto_id = :gasto_id, ingreso_id = :ingreso_id WHERE id = :transferencia_id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':gasto_id', $gasto_id, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':ingreso_id', $ingreso_id, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':transferencia_id', $transferencia_id, PDO::PARAM_INT);
            
            if (!$stmtUpdate->execute()) {
                throw new Exception("Error al actualizar transferencia");
            }
            
            // Commit transacción
            $this->conn->commit();
            return $transferencia_id;
            
        } catch (Exception $e) {
            // Rollback en caso de error
            $this->conn->rollBack();
            error_log("Error en crearTransferencia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todas las transferencias de un usuario
     */
    public function listarTransferencias($usuario_id) {
        if (!$this->conn) return [];
        
        try {
            $sql = "SELECT t.*, 
                           co.nombre as cuenta_origen_nombre, 
                           cd.nombre as cuenta_destino_nombre
                    FROM transferencias t
                    INNER JOIN cuentas co ON t.cuenta_origen_id = co.id
                    INNER JOIN cuentas cd ON t.cuenta_destino_id = cd.id
                    WHERE t.usuario_id = :usuario_id
                    ORDER BY t.fecha DESC, t.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listarTransferencias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener una transferencia por ID
     */
    public function obtenerPorId($id, $usuario_id) {
        if (!$this->conn) return null;
        
        try {
            $sql = "SELECT t.*, 
                           co.nombre as cuenta_origen_nombre, 
                           cd.nombre as cuenta_destino_nombre
                    FROM transferencias t
                    INNER JOIN cuentas co ON t.cuenta_origen_id = co.id
                    INNER JOIN cuentas cd ON t.cuenta_destino_id = cd.id
                    WHERE t.id = :id AND t.usuario_id = :usuario_id
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar una transferencia y sus registros asociados (transacción atómica)
     */
    public function eliminarTransferencia($id, $usuario_id) {
        if (!$this->conn) return false;
        if (empty($id) || empty($usuario_id)) return false;
        
        // Verificar que la transferencia existe y pertenece al usuario
        $transferencia = $this->obtenerPorId($id, $usuario_id);
        if (!$transferencia) {
            return false;
        }
        
        $gasto_id = $transferencia['gasto_id'];
        $ingreso_id = $transferencia['ingreso_id'];
        
        // Iniciar transacción
        $this->conn->beginTransaction();
        
        try {
            // 1. Eliminar gasto (si existe)
            if ($gasto_id) {
                $sqlGasto = "DELETE FROM gastos WHERE id = :gasto_id AND usuario_id = :usuario_id";
                $stmtGasto = $this->conn->prepare($sqlGasto);
                $stmtGasto->bindParam(':gasto_id', $gasto_id, PDO::PARAM_INT);
                $stmtGasto->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                
                if (!$stmtGasto->execute()) {
                    throw new Exception("Error al eliminar gasto");
                }
            }
            
            // 2. Eliminar ingreso (si existe)
            if ($ingreso_id) {
                $sqlIngreso = "DELETE FROM ingresos WHERE id = :ingreso_id AND usuario_id = :usuario_id";
                $stmtIngreso = $this->conn->prepare($sqlIngreso);
                $stmtIngreso->bindParam(':ingreso_id', $ingreso_id, PDO::PARAM_INT);
                $stmtIngreso->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                
                if (!$stmtIngreso->execute()) {
                    throw new Exception("Error al eliminar ingreso");
                }
            }
            
            // 3. Eliminar transferencia
            $sqlTransferencia = "DELETE FROM transferencias WHERE id = :id AND usuario_id = :usuario_id";
            $stmtTransferencia = $this->conn->prepare($sqlTransferencia);
            $stmtTransferencia->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtTransferencia->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            
            if (!$stmtTransferencia->execute()) {
                throw new Exception("Error al eliminar transferencia");
            }
            
            // Commit transacción
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback en caso de error
            $this->conn->rollBack();
            error_log("Error en eliminarTransferencia: " . $e->getMessage());
            return false;
        }
    }
}
