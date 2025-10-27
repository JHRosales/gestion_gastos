<?php
/*<!--  Sirve para incluir los archivos en cada app que se ingresa--> */
// spl_autoload_register(function($class){
//     $paths = [
//         APP_PATH,
//         APP_PATH . 'controllers' . DS,
//         APP_PATH . 'models' . DS
//     ];
//     foreach ($paths as $path) {
//         $file = $path . $class . '.php';
//         if (file_exists($file)) {
//             echo "[Loader 1] Incluyendo: $file\n";
//             require_once $file;
//             return;
//         }
//     }
// });



spl_autoload_register(function($class) {
    $file = dirname(__FILE__).'/'. $class. ".php";
    if(file_exists($file)) {
        // echo "[Loader 2] Incluyendo: $file\n";
        include $file;
    }
});


?>
