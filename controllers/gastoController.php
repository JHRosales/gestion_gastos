<?php
class GastoController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Gasto');
        parent::__construct();
    }

    public function index() {
        $this->redireccionar('dashboard/index');
    }

    public function editar($id = null) {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $monto = isset($_POST['monto']) ? trim($_POST['monto']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
            $cuenta_id = isset($_POST['cuenta_id']) && $_POST['cuenta_id'] !== '' ? intval($_POST['cuenta_id']) : null;

            if ($monto === '' || $categoria === '' || $fecha === '') {
                $_SESSION['error_gasto'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->actualizarGasto($id, $usuario_id, $monto, $categoria, $descripcion, $fecha, $cuenta_id);
            if ($ok) {
                $_SESSION['success_gasto'] = 'Gasto actualizado correctamente.';
            } else {
                $_SESSION['error_gasto'] = 'Error al actualizar el gasto.';
            }
            $this->redireccionar('dashboard/index');
            return;
        }

        $gasto = $this->_modelo->obtenerPorId($id, $_SESSION['user']['id']);
        if (!$gasto) {
            $_SESSION['error_gasto'] = 'Gasto no encontrado.';
            $this->redireccionar('dashboard/index');
            return;
        }

        $this->_view->gasto = $gasto;
        // Cargar datos necesarios para la vista
        $categoriaModel = $this->loadModel('Categoria');
        $this->_view->categorias = $categoriaModel->listarPorTipo('gasto');
        
        // Cargar cuentas para el selector
        $cuentaModel = $this->loadModel('Cuenta');
        $this->_view->cuentas = $cuentaModel->listarCuentas($_SESSION['user']['id']);
        
        $this->_view->esEdicion = true;
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

        $ok = $this->_modelo->eliminarGasto($id, $_SESSION['user']['id']);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Gasto eliminado correctamente' : 'Error al eliminar el gasto'
        ]);
    }

    public function registrar() {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $monto = isset($_POST['monto']) ? trim($_POST['monto']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
            $cuenta_id = isset($_POST['cuenta_id']) && $_POST['cuenta_id'] !== '' ? intval($_POST['cuenta_id']) : null;

            if ($monto === '' || $categoria === '' || $fecha === '') {
                $_SESSION['error_gasto'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->crearGasto($usuario_id, $monto, $categoria, $descripcion, $fecha, $cuenta_id);
            if ($ok) {
                $_SESSION['success_gasto'] = 'Gasto registrado correctamente.';
            } else {
                $_SESSION['error_gasto'] = 'Error al registrar el gasto.';
                $log = date('Y-m-d H:i:s') . " | Error al registrar gasto | usuario_id: $usuario_id | monto: $monto | categoria: $categoria | descripcion: $descripcion | fecha: $fecha | cuenta_id: $cuenta_id\n";
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
        $categoriaModel = $this->loadModel('Categoria');
        $this->_view->categorias = $categoriaModel->listarPorTipo('gasto');
        
        // Cargar cuentas para el selector
        $cuentaModel = $this->loadModel('Cuenta');
        $this->_view->cuentas = $cuentaModel->listarCuentas($_SESSION['user']['id']);
        
        $this->_view->esEdicion = false;
        $this->_view->renderizar('registrar');
    }

    public function listarGastos() {
        if (!isset($_SESSION['user'])) {
            return [];
        }
        $usuario_id = $_SESSION['user']['id'];
        return $this->_modelo->listarGastos($usuario_id);
    }
} 