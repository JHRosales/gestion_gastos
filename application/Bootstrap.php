<?php
/** Bootstrap.php*/
class Bootstrap 
{
    public static function run(Request $peticion) // el metodo requiere de una instancia de request que se llama peticion
    {
        $controller = $peticion->getControlador() . 'Controller'; //se concatena el controlador  //  ejem:  indexController o postController
        $rutaControlador = ROOT . 'controllers' . DS . $controller . '.php';  // ejem:  .../controllers/indexController.php
        $metodo = $peticion->getMetodo();
        $args = $peticion->getArgs();


        if(is_readable($rutaControlador)){//es leible la ruta de controlados si es si agrega a la ruta del controlador
            require_once $rutaControlador;
            $controller = new $controller;

            if(is_callable(array($controller, $metodo))){//es llamable el nombre del metodo si dice que si se instancia el metodo
                $metodo = $peticion->getMetodo();
            }
          	else{
                $metodo = DEFAULT_METODO;//index
            }

            if(isset($args)){
                call_user_func_array(array($controller, $metodo), $args);//se llama si hay varios argumentos, aqui se manda el controlador metodos y arg
            }
            else{
                call_user_func(array($controller, $metodo));// si no hay argumentos se manda el controlador y el medotod
            }

        } else {
            throw new Exception('Controlador no encontrado');//sino sale el mensaje
        }

    }
}
