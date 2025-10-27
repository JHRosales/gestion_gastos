<?php
class BalanceMensualModel extends Model {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->conn = null;
        }
    }

    public function obtenerMesesDisponibles($usuario_id) {
        if (!$this->conn) return [];
        try {
            $query = "SELECT DISTINCT YEAR(fecha) as anio, MONTH(fecha) as mes 
                     FROM (
                         SELECT fecha FROM gastos WHERE usuario_id = :usuario_id1 
                         UNION ALL 
                         SELECT fecha FROM ingresos WHERE usuario_id = :usuario_id2
                     ) as movimientos 
                     ORDER BY anio ASC, mes ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id1', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id2', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $meses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $meses[] = $row['anio'] . '-' . str_pad($row['mes'], 2, '0', STR_PAD_LEFT);
            }
            return $meses;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerDatosMes($usuario_id, $mes) {
        if (!$this->conn) return [];
        try {
            $mesAnio = explode('-', $mes);
            $anio = intval($mesAnio[0]);
            $mes = intval($mesAnio[1]);

            $query = "SELECT c.nombre, c.color, c.icono, SUM(g.monto) as total 
                     FROM gastos g 
                     INNER JOIN categorias c ON g.categoria = c.nombre 
                     WHERE g.usuario_id = :usuario_id 
                     AND MONTH(g.fecha) = :mes 
                     AND YEAR(g.fecha) = :anio 
                     AND c.tipo = 'gasto' 
                     GROUP BY c.nombre, c.color, c.icono 
                     ORDER BY total DESC 
                     LIMIT 10";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            $labels = [];
            $data = [];
            $colors = [];
            $iconos = [];
            $resumen = [];
            $totalGeneral = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $labels[] = $row['nombre'];
                $data[] = (float)$row['total'];
                $colors[] = $row['color'];
                $iconos[] = $row['icono'];
                $resumen[] = [
                    'nombre' => $row['nombre'],
                    'icono' => $row['icono'],
                    'total' => (float)$row['total'],
                    'color' => $row['color']
                ];
                $totalGeneral += (float)$row['total'];
            }

            return [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
                'iconos' => $iconos,
                'resumen' => $resumen,
                'total' => $totalGeneral
            ];
        } catch (PDOException $e) {
            return [];
        }
    }
} 