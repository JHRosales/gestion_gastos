<?php
class MetaController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Meta');
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
            $nombre = isset($_POST['nombre_meta']) ? trim($_POST['nombre_meta']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $monto_objetivo = isset($_POST['monto_objetivo']) ? trim($_POST['monto_objetivo']) : '';
            $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
            $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';

            if ($nombre === '' || $categoria === '' || $monto_objetivo === '' || $fecha_inicio === '' || $fecha_fin === '') {
                $_SESSION['error_meta'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->actualizarMeta($id, $usuario_id, $nombre, $categoria, $monto_objetivo, $fecha_inicio, $fecha_fin);
            if ($ok) {
                $_SESSION['success_meta'] = 'Meta actualizada correctamente.';
            } else {
                $_SESSION['error_meta'] = 'Error al actualizar la meta.';
            }
            $this->redireccionar('dashboard/index');
            return;
        }

        $meta = $this->_modelo->obtenerPorId($id, $_SESSION['user']['id']);
        if (!$meta) {
            $_SESSION['error_meta'] = 'Meta no encontrada.';
            $this->redireccionar('dashboard/index');
            return;
        }

        $this->_view->meta = $meta;
        // Cargar categorías para la vista
        $categoriaModel = $this->loadModel('Categoria');
        $this->_view->categorias = $categoriaModel->listarPorTipo('gasto');
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

        $ok = $this->_modelo->eliminarMeta($id, $_SESSION['user']['id']);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Meta eliminada correctamente' : 'Error al eliminar la meta'
        ]);
    }

    public function registrar() {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $nombre = isset($_POST['nombre_meta']) ? trim($_POST['nombre_meta']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $monto_objetivo = isset($_POST['monto_objetivo']) ? trim($_POST['monto_objetivo']) : '';
            $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
            $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';
            $monto_actual = 0; // Inicializamos el monto actual en 0 para nuevas metas

            if ($nombre === '' || $categoria === '' || $monto_objetivo === '' || $fecha_inicio === '' || $fecha_fin === '') {
                $_SESSION['error_meta'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('dashboard/index');
                return;
            }

            $ok = $this->_modelo->crearMeta($usuario_id, $nombre, $categoria, $monto_objetivo, $fecha_inicio, $fecha_fin, $monto_actual);
            if ($ok) {
                $_SESSION['success_meta'] = 'Meta registrada correctamente.';
            } else {
                $_SESSION['error_meta'] = 'Error al registrar la meta.';
            }
            $this->redireccionar('dashboard/index');
            return;
        }

        // Cargar categorías para la vista
        $categoriaModel = $this->loadModel('Categoria');
        $this->_view->categorias = $categoriaModel->listarPorTipo('gasto');
        $this->_view->esEdicion = false;
        $this->_view->renderizar('registrar');
    }

    public function listarMetas() {
        if (!isset($_SESSION['user'])) {
            return [];
        }
        $usuario_id = $_SESSION['user']['id'];
        return $this->_modelo->listarMetas($usuario_id);
    }
} 