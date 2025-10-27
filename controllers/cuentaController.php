<?php
class CuentaController extends Controller {
    private $_modelo;
    
    public function __construct() {
        $this->_modelo = $this->loadModel('Cuenta');
        parent::__construct();
    }

    /**
     * Listar todas las cuentas del usuario
     */
    public function index() {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        $usuario_id = $_SESSION['user']['id'];
        $cuentas = $this->_modelo->listarCuentas($usuario_id);
        
        // Calcular saldo para cada cuenta
        foreach ($cuentas as &$cuenta) {
            $cuenta['saldo_calculado'] = $this->_modelo->calcularSaldo($cuenta['id'], $usuario_id);
        }
        
        $this->_view->cuentas = $cuentas;
        $this->_view->renderizar('index');
    }

    /**
     * Registrar una nueva cuenta
     */
    public function registrar() {
        if (!isset($_SESSION['user'])) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
                return;
            }
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
            $saldo_inicial = isset($_POST['saldo_inicial']) ? floatval($_POST['saldo_inicial']) : 0;
            $moneda = isset($_POST['moneda']) ? trim($_POST['moneda']) : 'PEN';

            if (empty($nombre) || empty($tipo)) {
                $_SESSION['error_cuenta'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('cuenta/index');
                return;
            }

            $ok = $this->_modelo->crearCuenta($usuario_id, $nombre, $tipo, $saldo_inicial, $moneda);
            if ($ok) {
                $_SESSION['success_cuenta'] = 'Cuenta registrada correctamente.';
            } else {
                $_SESSION['error_cuenta'] = 'Error al registrar la cuenta.';
            }
            
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => $ok,
                    'message' => $ok ? 'Cuenta registrada correctamente' : 'Error al registrar la cuenta'
                ]);
                return;
            }
            
            $this->redireccionar('cuenta/index');
            return;
        }

        $this->_view->esEdicion = false;
        $this->_view->renderizar('registrar');
    }

    /**
     * Editar una cuenta existente
     */
    public function editar($id = null) {
        if (!isset($_SESSION['user'])) {
            $this->redireccionar('login/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
            $saldo_inicial = isset($_POST['saldo_inicial']) ? floatval($_POST['saldo_inicial']) : 0;
            $moneda = isset($_POST['moneda']) ? trim($_POST['moneda']) : 'PEN';

            if (empty($id) || empty($nombre) || empty($tipo)) {
                $_SESSION['error_cuenta'] = 'Todos los campos obligatorios deben completarse.';
                $this->redireccionar('cuenta/index');
                return;
            }

            $ok = $this->_modelo->actualizarCuenta($id, $usuario_id, $nombre, $tipo, $saldo_inicial, $moneda);
            if ($ok) {
                $_SESSION['success_cuenta'] = 'Cuenta actualizada correctamente.';
            } else {
                $_SESSION['error_cuenta'] = 'Error al actualizar la cuenta.';
            }
            
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => $ok,
                    'message' => $ok ? 'Cuenta actualizada correctamente' : 'Error al actualizar la cuenta'
                ]);
                return;
            }
            
            $this->redireccionar('cuenta/index');
            return;
        }

        $cuenta = $this->_modelo->obtenerPorId($id, $_SESSION['user']['id']);
        if (!$cuenta) {
            $_SESSION['error_cuenta'] = 'Cuenta no encontrada.';
            $this->redireccionar('cuenta/index');
            return;
        }

        $this->_view->cuenta = $cuenta;
        $this->_view->esEdicion = true;
        $this->_view->renderizar('registrar');
    }

    /**
     * Eliminar una cuenta
     */
    public function eliminar($id = null) {
        if (!isset($_SESSION['user'])) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
                return;
            }
            $this->redireccionar('login/login');
            return;
        }

        if (!$id) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => 'ID inválido']);
                return;
            }
            $this->redireccionar('cuenta/index');
            return;
        }

        $ok = $this->_modelo->eliminarCuenta($id, $_SESSION['user']['id']);
        
        if ($this->isAjaxRequest()) {
            $this->jsonResponse([
                'success' => $ok,
                'message' => $ok ? 'Cuenta eliminada correctamente' : 'Error al eliminar la cuenta'
            ]);
            return;
        }
        
        if ($ok) {
            $_SESSION['success_cuenta'] = 'Cuenta eliminada correctamente.';
        } else {
            $_SESSION['error_cuenta'] = 'Error al eliminar la cuenta.';
        }
        
        $this->redireccionar('cuenta/index');
    }

    /**
     * Cambiar cuenta seleccionada (usado en dashboard)
     */
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
        $cuenta = $this->_modelo->obtenerPorId($cuenta_id, $_SESSION['user']['id']);
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
}

