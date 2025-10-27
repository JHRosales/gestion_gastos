<?php
class BalanceMensualController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('BalanceMensual');
        parent::__construct();
    }

    public function index() {
        $this->redireccionar('dashboard/index');
    }

    public function obtenerMeses() {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $meses = $this->_modelo->obtenerMesesDisponibles($_SESSION['user']['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'meses' => $meses]);
    }

    public function obtenerDatosMes($mes = null) {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        if (!$mes) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Mes no especificado']);
            return;
        }

        $datos = $this->_modelo->obtenerDatosMes($_SESSION['user']['id'], $mes);
        header('Content-Type: application/json');
        echo json_encode($datos);
    }
} 