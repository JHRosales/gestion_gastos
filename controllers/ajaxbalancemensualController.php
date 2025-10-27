<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

header('Content-Type: application/json');
// TODO: Validar si se esta usando. 

if (isset($_POST['action']) && isset($_SESSION['user']['id'])) {
    $action = $_POST['action'];
    $usuario_id = $_SESSION['user']['id'];
    try {
        $database = new Database();
        $db = $database->getConnection();
        if ($action == 'obtenerMeses') {
            $query = "SELECT DISTINCT YEAR(fecha) as anio, MONTH(fecha) as mes FROM (SELECT fecha FROM gastos WHERE usuario_id = :usuario_id1 UNION ALL SELECT fecha FROM ingresos WHERE usuario_id = :usuario_id2) as movimientos ORDER BY anio ASC, mes ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id1', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id2', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $meses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $meses[] = $row['anio'] . '-' . str_pad($row['mes'], 2, '0', STR_PAD_LEFT);
            }
            echo json_encode(['meses' => $meses]);
            exit;
        }
        if ($action == 'obtenerDatosMes' && isset($_POST['mes'])) {
            $mesAnio = explode('-', $_POST['mes']);
            $anio = intval($mesAnio[0]);
            $mes = intval($mesAnio[1]);
            $query = "SELECT c.nombre, c.color, c.icono, SUM(g.monto) as total FROM gastos g INNER JOIN categorias c ON g.categoria = c.nombre WHERE g.usuario_id = :usuario_id AND MONTH(g.fecha) = :mes AND YEAR(g.fecha) = :anio and c.tipo = 'gasto' GROUP BY c.nombre, c.color, c.icono ORDER BY total DESC LIMIT 10";
            $stmt = $db->prepare($query);
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
            echo json_encode([
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
                'iconos' => $iconos,
                'resumen' => $resumen,
                'total' => $totalGeneral
            ]);
            exit;
        }
        echo json_encode(['error' => 'Acción no válida o parámetros insuficientes.']);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Parámetros insuficientes o sesión expirada.']);
}
