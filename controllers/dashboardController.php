
<?php
class DashboardController extends Controller{
    /**
 * Pagina principal,
 * Dashboard
 */
    public $_modelo;
    public function __construct() {
        $this->_modelo = $this->loadModel('Dashboard');
        parent::__construct();
    }

    public function index() {
        $this->mostrarDashboard();
    }
    public function mostrarDashboard() {

        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
        }
        $usuario_id = $_SESSION['user']['id'];
        $nombre = $_SESSION['user']['nombre'];
        
        // Obtener cuenta seleccionada (si existe)
        $cuenta_id = isset($_SESSION['cuenta_seleccionada']) && $_SESSION['cuenta_seleccionada'] > 0 ? $_SESSION['cuenta_seleccionada'] : null;
        
        // Cargar cuentas del usuario
        $cuentaModel = $this->loadModel('Cuenta');
        $cuentas = $cuentaModel->listarCuentas($usuario_id);
        
        // Debug: Verificar si hay cuentas
        if (empty($cuentas)) {
            error_log("No se encontraron cuentas para el usuario ID: $usuario_id");
        }
        
        $this->_view->cuentas = $cuentas;
        
        $metaModel = $this->loadModel('Meta');
        $metas = $metaModel->listarMetas($usuario_id);
        $ingresoModel = $this->loadModel('Ingreso');
        $gastoModel = $this->loadModel('Gasto');
        $categoriaModel = $this->loadModel('Categoria');
        
        // Filtrar por cuenta si está seleccionada
        $ingresos = $ingresoModel->listarIngresos($usuario_id, $cuenta_id);
        $gastos = $gastoModel->listarGastos($usuario_id, $cuenta_id);
        $categorias = $categoriaModel->listarTodas();
        $ingresosPorCategoria = [];
        foreach ($ingresos as $ing) {
            $cat = $ing['categoria'] ?: 'Sin categoría';
            $ingresosPorCategoria[$cat] = isset($ingresosPorCategoria[$cat]) ? $ingresosPorCategoria[$cat] + $ing['monto'] : $ing['monto'];
        }
        $gastosPorCategoria = [];
        foreach ($gastos as $gas) {
            $cat = $gas['categoria'] ?: 'Sin categoría';
            $gastosPorCategoria[$cat] = isset($gastosPorCategoria[$cat]) ? $gastosPorCategoria[$cat] + $gas['monto'] : $gas['monto'];
        }
        $saldo = $this->calcularSaldo($usuario_id, $cuenta_id);
        $resumenMetas = $this->verificarCumplimientoMetas($usuario_id);

        $this->_view->nombre = $nombre;
        $this->_view->saldo = $saldo;
        $this->_view->ingresosPorCategoria = $ingresosPorCategoria;
        $this->_view->gastosPorCategoria = $gastosPorCategoria;
        $this->_view->metas = $metas;
        $this->_view->resumenMetas = $resumenMetas;
        $this->_view->ingresos = $ingresos;
        $this->_view->gastos = $gastos;
        $this->_view->categorias = $categorias;
        $this->_view->renderizar('index');
    }
    public function calcularSaldo($usuario_id, $cuenta_id = null) {
        try {
            $ingresos = $this->_modelo->ingresos($usuario_id, $cuenta_id); 
            $gastos = $this->_modelo->gastos($usuario_id, $cuenta_id);
            $totalIngresos = $ingresos && $ingresos['total'] !== null ? (float)$ingresos['total'] : 0;
            $totalGastos = $gastos && $gastos['total'] !== null ? (float)$gastos['total'] : 0;
            
            // Si hay una cuenta específica seleccionada, agregar el saldo inicial
            if ($cuenta_id !== null && $cuenta_id > 0) {
                $cuentaModel = $this->loadModel('Cuenta');
                return $cuentaModel->calcularSaldo($cuenta_id, $usuario_id);
            }
            
            return $totalIngresos - $totalGastos;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function cambiarCuenta() {
        if (!isset($_SESSION['user'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $cuenta_id = isset($_GET['cuenta_id']) ? intval($_GET['cuenta_id']) : null;
        
        // Si cuenta_id es 0 o 'todos', limpiar la selección
        if ($cuenta_id === 0 || $cuenta_id === 'todos' || $cuenta_id === 'all') {
            unset($_SESSION['cuenta_seleccionada']);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Ver todas las cuentas'
            ]);
            return;
        }

        // Verificar que la cuenta pertenece al usuario
        $cuentaModel = $this->loadModel('Cuenta');
        $cuenta = $cuentaModel->obtenerPorId($cuenta_id, $_SESSION['user']['id']);
        if (!$cuenta) {
            $this->jsonResponse(['success' => false, 'message' => 'Cuenta no encontrada']);
            return;
        }

        // Guardar cuenta seleccionada en sesión
        $_SESSION['cuenta_seleccionada'] = $cuenta_id;
        
        $this->jsonResponse([
            'success' => true,
            'message' => 'Cuenta cambiada correctamente',
            'cuenta' => $cuenta
        ]);
    }
    public function verificarCumplimientoMetas($usuario_id) {
        try {
            $metaModel = $this->loadModel('Meta');
            return $metaModel->verificarCumplimientoMetas($usuario_id);
        } catch (PDOException $e) {
            return [];
        }
    }
}
