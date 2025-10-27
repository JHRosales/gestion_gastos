<?php
/* Controller.php */
abstract class Controller  //la clase es abstracta cuando hay 1 metodo abstrato minimo, se puede llaman directamente sin instanciarlo
{
    protected $_view;//puede ser utilizada dentro o en las clases heredadas
    
    public function __construct() {// se genera una instancia
		$this->_view = new View(new Request);
	}

    abstract public function index();// siempre se tiene que definir el metodo index

    protected function loadModel($modelo)// se utiliza para cargar el modelo
	{
		$modelo = $modelo . 'Model';
		$rutaModelo = ROOT . 'models' . DS . $modelo . '.php';

		if(is_readable($rutaModelo)){
			require_once $rutaModelo;
			$modelo = new $modelo;
			return $modelo;
		}
		else {
			var_dump($rutaModelo);
			throw new Exception('Error no se encontro el modelo');
		}
	}
	protected function redireccionar($ruta = false)
	{
		if($ruta){
			header('location:' . BASE_URL . $ruta);
			exit;
		}
		else{
			header('location:' . BASE_URL);
			exit;
		}
	}

    protected function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function getPost($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }
}
