<?php
/* Database.php */
class Database extends PDO
{
    public function __construct()
    {
        //CONEXION CON PDO - MySQL
        parent::__construct(DSN_W, USUARIO, PASSWORD);
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
