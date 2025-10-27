<?php
class CategoriaController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Categoria');
        parent::__construct();
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        // Cargar los iconos y colores para el modal
        $this->_view->iconosAgrupados = require __DIR__ . '/../views/categoria/iconos.php';
        $this->_view->colores = require __DIR__ . '/../views/categoria/colores.php';

        // Obtener el tipo seleccionado (por defecto 'gasto')
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'gasto';
        
        // Obtener las categorías según el tipo
        if ($tipo === 'ingreso') {
            $categorias = $this->_modelo->listarPorTipo('ingreso', $_SESSION['user']['id']);
        } else {
            $categorias = $this->_modelo->listarPorTipo('gasto', $_SESSION['user']['id']);
        }

        // Si es una petición AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'categorias' => $categorias
            ]);
            return;
        }

        // Si no es AJAX, renderizar la vista
        $this->_view->categorias = $categorias;
        $this->_view->renderizar('index');
    }

    public function registrar() {
        try {
            // Verificar si es una petición AJAX
            if (!$this->isAjaxRequest()) {
                throw new Exception('Método no permitido');
            }

            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user'])) {
                throw new Exception('Debe iniciar sesión para realizar esta acción');
            }

            // Obtener y validar datos
            $nombre = $this->getPost('nombre');
            $tipo = $this->getPost('tipo');
            $icono = $this->getPost('icono');
            $color = $this->getPost('color');

            // Validar campos requeridos
            if (empty($nombre) || empty($tipo) || empty($icono) || empty($color)) {
                throw new Exception('Todos los campos son requeridos');
            }

            // Validar tipo
            if (!in_array($tipo, ['ingreso', 'gasto'])) {
                throw new Exception('Tipo de categoría inválido');
            }

            // Intentar crear la categoría usando el modelo
            $ok = $this->_modelo->crearCategoria($nombre, $icono, $color, $tipo);
            
            if (!$ok) {
                throw new Exception($this->_modelo->getLastError() ?: 'Error al guardar la categoría');
            }


            // Retornar respuesta exitosa
            $this->jsonResponse([
                'success' => true,
                'message' => 'Categoría registrada exitosamente',
                'data' => [
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'icono' => $icono,
                    'color' => $color
                ]
            ]);

        } catch (Exception $e) {
            // Retornar error
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al registrar la categoría: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($id = null) {
        try {
            // Verificar si es una petición AJAX
            if (!$this->isAjaxRequest()) {
                throw new Exception('Método no permitido');
            }

            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user'])) {
                throw new Exception('Debe iniciar sesión para realizar esta acción');
            }

            // Si es GET, devolver los datos de la categoría
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $categoria = $this->_modelo->obtenerPorId($id);
                if (!$categoria) {
                    throw new Exception('Categoría no encontrada');
                }
                $this->jsonResponse(['success' => true, 'data' => $categoria]);
                return;
            }

            // Si es POST, actualizar la categoría
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Obtener y validar datos
                $nombre = $this->getPost('nombre');
                $tipo = $this->getPost('tipo');
                $icono = $this->getPost('icono');
                $color = $this->getPost('color');

                // Validar campos requeridos
                if (empty($nombre) || empty($tipo) || empty($icono) || empty($color)) {
                    throw new Exception('Todos los campos son requeridos');
                }

                // Validar tipo
                if (!in_array($tipo, ['ingreso', 'gasto'])) {
                    throw new Exception('Tipo de categoría inválido');
                }

                // Intentar actualizar la categoría
                $ok = $this->_modelo->actualizarCategoria($id, $nombre, $icono, $color, $tipo);
                
                if (!$ok) {
                    throw new Exception($this->_modelo->getLastError() ?: 'Error al actualizar la categoría');
                }

                // Registrar en el log
                error_log("Categoría actualizada exitosamente: " . json_encode([
                    'id' => $id,
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'icono' => $icono,
                    'color' => $color
                ]));

                // Retornar respuesta exitosa
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente'
                ]);
                return;
            }

        } catch (Exception $e) {

            // Retornar error
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al editar la categoría: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id = null) {
        try {
            // Verificar si es una petición AJAX
            if (!$this->isAjaxRequest()) {
                throw new Exception('Método no permitido');
            }

            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user'])) {
                throw new Exception('Debe iniciar sesión para realizar esta acción');
            }

            // Verificar que se proporcionó un ID
            if (!$id) {
                throw new Exception('ID de categoría no proporcionado');
            }

            // Verificar que la categoría existe
            $categoria = $this->_modelo->obtenerPorId($id);
            if (!$categoria) {
                throw new Exception('Categoría no encontrada');
            }

            // Intentar eliminar la categoría
            $ok = $this->_modelo->eliminarCategoria($id);
            if (!$ok) {
                throw new Exception($this->_modelo->getLastError() ?: 'Error al eliminar la categoría');
            }

            // Retornar respuesta exitosa
            $this->jsonResponse([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);

        } catch (Exception $e) {

            // Retornar error
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ]);
        }
    }

    public function listarPorTipo($tipo) {
        if (!isset($_SESSION['user'])) {
            return [];
        }
        $usuario_id = $_SESSION['user']['id'];
        return $this->_modelo->listarPorTipo($tipo, $usuario_id);
    }

    public function listarCategorias() {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]);
            return;
        }

        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'gasto';
        $usuario_id = $_SESSION['user']['id'];
        
        $categorias = $this->_modelo->listarPorTipo($tipo, $usuario_id);
        
        if ($categorias === null) {
            $error = $this->_modelo->getLastError();
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar categorías',
                'error' => $error
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'categorias' => $categorias
        ]);
    }

}
