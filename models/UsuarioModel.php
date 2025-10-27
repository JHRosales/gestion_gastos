
<?php
class UsuarioModel extends Model{

    public function __construct() {
        parent::__construct();
    }
    public function registrar($nombre, $correo, $contrasena) {
        if (!$this->_db) return false;
        try {
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (:nombre, :correo, :contrasena)";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            // var_dump($e);
            return false;
        }
    }
    public function login($correo, $contrasena) {
        if (!$this->_db) return false;
        try {
        $sql = "SELECT id, nombre, correo, contrasena FROM usuarios WHERE correo = :correo LIMIT 1";
        $result = $this->_db->prepare( $sql );
        $result->bindParam(':correo', $correo);
        $result->execute();
        $usuario = $result->fetch(PDO::FETCH_ASSOC);          
        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                return [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'correo' => $usuario['correo']
                ];
            }
            return false;
        } catch (PDOException $e) {
            // var_dump($e);
            return false;
        }
    }
}
