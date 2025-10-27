<?php
/*Request.php*/
class Request {
    // se define propiedades privadas
    private $_controlador;
    private $_metodo;
    private $_argumentos;


     public function __construct() {	//metodo constructor se ejecuta al momento de instanciarlo
        if(isset($_GET['url'])){	// si es que existe la variable url pasa por aqui y lo primero que va hacer el filter_input: es una forma de validad
        	// que la url este bien escrita, tiene una constante que se llamata filter_sanitize_url, ejemplo espacio en blando se reeanokaza oir ese sabatuze
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL); //a sido ingresada por un imput get
            $url = explode('/', $url);	//se ingresa el una variable
            $url = array_filter($url);  // elimina los elementos vacios

            $controlador = array_shift($url);
            $metodo = array_shift($url);
            
            $this->_controlador = $controlador ? strtolower($controlador) : null;//convierte a minuscula y se almacena el products en el controlador
            $this->_metodo = $metodo ? strtolower($metodo) : null;//almacen ale medodo eliminar
            $this->_argumentos = $url;//resto de parametros
        }

        if(!$this->_controlador){
            $this->_controlador = DEFAULT_CONTROLLER;   // index
        }

        if(!$this->_metodo){
            $this->_metodo = DEFAULT_METODO;//index
        }

        if(!isset($this->_argumentos)){
            $this->_argumentos = array();// si no hay argumentos se manda un array vacio
        }
    }

    public function getControlador()//metodo publico q a su ves llaman al metros y argumentos
    {
        return $this->_controlador;
    }
    public function getMetodo() {
        return $this->_metodo;
    }
    public function getArgs() {
        return $this->_argumentos;
    }

}
