<?php

class LoginController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->login();
    }

    public function login() {
        
        if (isset($_SESSION['user'])) {
            $this->redireccionar('dashboard/index');
            return;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {            
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $usuarioModel = $this->loadModel('Usuario');
            $usuario = $usuarioModel->login($email, $password);

            if ($usuario) {
                $_SESSION['user'] = $usuario;
                $this->redireccionar('dashboard/index');
                return;
            } else {
                $error = 'Correo o contraseña incorrectos.';
                $this->_view->renderizar('login');
            }
        }

        $this->_view->error = $error;
        $this->_view->renderizar('login');
    }

    public function register() {
        if (isset($_SESSION['user'])) {
            $this->_view->renderizar('dashboard/index');
            return;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            if ($password !== $confirm) {
                $error = 'Las contraseñas no coinciden.';
            } elseif (!$nombre || !$email || !$password) {
                $error = 'Completa todos los campos.';
            } else {              
                $usuarioModel = $this->loadModel('Usuario');
                $registrado = $usuarioModel->registrar($nombre, $email, $password);

                if ($registrado) {
                    $usuario = $usuarioModel->login($email, $password);
                    $_SESSION['user'] = $usuario;
                    $this->redireccionar('dashboard/index');
                    return;
                } else {               
                    $error = 'No se pudo registrar el usuario. ¿Ya existe el correo?';
                }                
            }
        }

        $this->_view->error = $error;
        $this->_view->renderizar('register');
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->_view->renderizar('login');
    }
}
