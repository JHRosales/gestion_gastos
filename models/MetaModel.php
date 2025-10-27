<?php
class MetaModel extends Model {
    private $conn;
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }
    public function crearMeta($usuario_id, $nombre_meta, $categoria, $monto_objetivo, $fecha_inicio, $fecha_fin) {
        if (!$this->conn) return false;
        if (empty($usuario_id) || empty($nombre_meta) || empty($categoria) || empty($monto_objetivo) || empty($fecha_inicio) || empty($fecha_fin)) {
            return false;
        }
        try {
            $sql = "INSERT INTO metas (usuario_id, nombre_meta, categoria, monto_objetivo, fecha_inicio, fecha_fin) VALUES (:usuario_id, :nombre_meta, :categoria, :monto_objetivo, :fecha_inicio, :fecha_fin)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_meta', $nombre_meta);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':monto_objetivo', $monto_objetivo);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    public function listarMetas($usuario_id) {
        if (!$this->conn) return [];
        try {
            $sql = "SELECT * FROM metas WHERE usuario_id = :usuario_id ORDER BY fecha_inicio DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function verificarCumplimientoMetas($usuario_id) {
        if (!$this->conn) return [];
        try {
            $sql = "SELECT id, nombre_meta, categoria, monto_objetivo FROM metas WHERE usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $resultados = [];
            foreach ($metas as $meta) {
                $sqlGasto = "SELECT SUM(monto) as total FROM gastos WHERE usuario_id = :usuario_id AND categoria = :categoria";
                $stmtGasto = $this->conn->prepare($sqlGasto);
                $stmtGasto->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmtGasto->bindParam(':categoria', $meta['categoria']);
                $stmtGasto->execute();
                $gastoCategoria = $stmtGasto->fetch(PDO::FETCH_ASSOC);
                
                $totalGastoCategoria = $gastoCategoria && $gastoCategoria['total'] !== null ? (float)$gastoCategoria['total'] : 0;
                $estado = ($totalGastoCategoria >= $meta['monto_objetivo']) ? 'cumplida' : 'pendiente';
                $porcentaje = $meta['monto_objetivo'] > 0 ? min(100, round(($totalGastoCategoria / $meta['monto_objetivo']) * 100)) : 0;
                
                $resultados[] = [
                    'id' => $meta['id'],
                    'nombre_meta' => $meta['nombre_meta'],
                    'categoria' => $meta['categoria'],
                    'monto_objetivo' => $meta['monto_objetivo'],
                    'estado' => $estado,
                    'porcentaje' => $porcentaje
                ];
            }
            return $resultados;
        } catch (PDOException $e) {
            return [];
        }
    }
}
