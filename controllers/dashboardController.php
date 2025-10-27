
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
        $metaModel = $this->loadModel('Meta');
        $metas = $metaModel->listarMetas($usuario_id);
        $ingresoModel = $this->loadModel('Ingreso');
        $gastoModel = $this->loadModel('Gasto');
        $categoriaModel = $this->loadModel('Categoria');
        $ingresos = $ingresoModel->listarIngresos($usuario_id);
        $gastos = $gastoModel->listarGastos($usuario_id);
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
        $saldo = $this->calcularSaldo($usuario_id);
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
    public function calcularSaldo($usuario_id) {
        try {
            $ingresos = $this->_modelo->ingresos($usuario_id); 
            $gastos = $this->_modelo->gastos($usuario_id);
            $totalIngresos = $ingresos && $ingresos['total'] !== null ? (float)$ingresos['total'] : 0;
            $totalGastos = $gastos && $gastos['total'] !== null ? (float)$gastos['total'] : 0;
            return $totalIngresos - $totalGastos;
        } catch (PDOException $e) {
            return 0;
        }
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
