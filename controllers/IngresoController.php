<?php
class IngresoController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Ingreso');
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

            if ($monto === '' || $categoria === '' || $fecha === '') {
                $_SESSION['error_ingreso'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->actualizarIngreso($id, $usuario_id, $monto, $categoria, $descripcion, $fecha);
            if ($ok) {
                $_SESSION['success_ingreso'] = 'Ingreso actualizado correctamente.';
            } else {
                $_SESSION['error_ingreso'] = 'Error al actualizar el ingreso.';
            }
            $this->redireccionar('dashboard/index');
            return;
        }

        $ingreso = $this->_modelo->obtenerPorId($id, $_SESSION['user']['id']);
        if (!$ingreso) {
            $_SESSION['error_ingreso'] = 'Ingreso no encontrado.';
            $this->redireccionar('dashboard/index');
            return;
        }

        $this->_view->ingreso = $ingreso;
        // Cargar datos necesarios para la vista
        $categoriaModel = $this->loadModel('Categoria');
        $this->_view->categorias = $categoriaModel->listarPorTipo('ingreso');
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

        $ok = $this->_modelo->eliminarIngreso($id, $_SESSION['user']['id']);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Ingreso eliminado correctamente' : 'Error al eliminar el ingreso'
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

            if ($monto === '' || $categoria === '' || $fecha === '') {
                $_SESSION['error_ingreso'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->crearIngreso($usuario_id, $monto, $categoria, $descripcion, $fecha);
            if ($ok) {
                $_SESSION['success_ingreso'] = 'Ingreso registrado correctamente.';
            } else {
                $_SESSION['error_ingreso'] = 'Error al registrar el ingreso.';
                $log = date('Y-m-d H:i:s') . " | Error al registrar ingreso | usuario_id: $usuario_id | monto: $monto | categoria: $categoria | descripcion: $descripcion | fecha: $fecha\n";
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
        $this->_view->categorias = $categoriaModel->listarPorTipo('ingreso');
        $this->_view->esEdicion = false;
        $this->_view->renderizar('registrar');
    }

    public function listarIngresos() {
        if (!isset($_SESSION['user']['id'])) {
            return [];
        }
        $usuario_id = $_SESSION['user']['id'];
        return $this->_modelo->listarIngresos($usuario_id);
    }
}
