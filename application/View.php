<?php
/** View.php **/

class View 
{
    private $_controlador;
	private $_js;
	private $_vars = [];

	public function __construct(Request $peticion) {
		$this->_controlador = $peticion->getControlador();
		if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
		$this->_js = array();
	}

    /**  Esta funcion vuelve a cargar la pagina **/
    public function renderizar($vista, $partial = false)
	{
		$js = array();
		if(count($this->_js)){
			$js = $this->_js;
		}

		$_layoutParams = array(//recorre y jala los js
            'js' => $js
		);

		$rutaView = ROOT . 'views' . DS . $this->_controlador . DS . $vista . '.php'; //direcotiro raiz, carpeta view dx y la carpeta de controlador

		if(is_readable($rutaView)){
			if (!$partial) {//if ( !$item ) {	
				include_once ROOT . 'views'. DS . 'partials' . DS . DEFAULT_LAYOUT . DS . 'header.php';
				include_once $rutaView;
				include_once ROOT . 'views'. DS . 'partials' . DS . DEFAULT_LAYOUT . DS . 'footer.php';
			}
			else
			include_once $rutaView;
		}
		else {
			var_dump($rutaView);
			throw new Exception('Error  no se encontro la vista');
		}
	}

	

    public function __set($key, $value) {
        $this->_vars[$key] = $value;
    }

    public function __get($key) {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : null;
    }
}
