<?php
class TransferenciaController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Transferencia');
        parent::__construct();
    }

    public function index() {
        $this->redireccionar('dashboard/index');
    }

    public function registrar() {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $cuenta_origen_id = isset($_POST['cuenta_origen_id']) && $_POST['cuenta_origen_id'] !== '' ? intval($_POST['cuenta_origen_id']) : null;
            $cuenta_destino_id = isset($_POST['cuenta_destino_id']) && $_POST['cuenta_destino_id'] !== '' ? intval($_POST['cuenta_destino_id']) : null;
            $monto = isset($_POST['monto']) ? trim($_POST['monto']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';

            // Validaciones
            if ($cuenta_origen_id === null || $cuenta_destino_id === null || $monto === '' || $fecha === '') {
                $_SESSION['error_transferencia'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('transferencia/registrar');
                return;
            }

            if ($cuenta_origen_id == $cuenta_destino_id) {
                $_SESSION['error_transferencia'] = 'La cuenta origen y destino deben ser diferentes.';
                $this->redireccionar('transferencia/registrar');
                return;
            }

            if (!is_numeric($monto) || floatval($monto) <= 0) {
                $_SESSION['error_transferencia'] = 'El monto debe ser un número mayor a 0.';
                $this->redireccionar('transferencia/registrar');
                return;
            }

            $monto = floatval($monto);

            $transferencia_id = $this->_modelo->crearTransferencia($usuario_id, $cuenta_origen_id, $cuenta_destino_id, $monto, $descripcion, $fecha);
            
            if ($transferencia_id) {
                $_SESSION['success_transferencia'] = 'Transferencia realizada correctamente.';
            } else {
                $_SESSION['error_transferencia'] = 'Error al realizar la transferencia. Verifique que ambas cuentas sean válidas y diferentes.';
                $log = date('Y-m-d H:i:s') . " | Error al registrar transferencia | usuario_id: $usuario_id | cuenta_origen_id: $cuenta_origen_id | cuenta_destino_id: $cuenta_destino_id | monto: $monto | fecha: $fecha\n";
                $logDir = __DIR__ . '/../logs';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }
                file_put_contents($logDir . '/errores.log', $log, FILE_APPEND);
            }
            
            $this->redireccionar('dashboard/index');
            return;
        }

        // Cargar datos necesarios para la vista
        $cuentaModel = $this->loadModel('Cuenta');
        $this->_view->cuentas = $cuentaModel->listarCuentas($_SESSION['user']['id']);
        
        $this->_view->renderizar('registrar');
    }

    public function eliminar($id = null) {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $ok = $this->_modelo->eliminarTransferencia($id, $_SESSION['user']['id']);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Transferencia eliminada correctamente' : 'Error al eliminar la transferencia'
        ]);
    }
}
