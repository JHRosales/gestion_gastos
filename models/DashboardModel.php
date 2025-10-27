
<?php
class DashboardModel extends Model{

    public function __construct() {
        parent::__construct();
    }
    public function ingresos($usuario_id, $cuenta_id = null) {
        if (!$this->_db) return false;
        try {
            $sql = 'SELECT SUM(monto) as total FROM ingresos WHERE usuario_id = ?';
            $params = [$usuario_id];
            
            if ($cuenta_id !== null) {
                $sql .= ' AND cuenta_id = ?';
                $params[] = $cuenta_id;
            }
            
            $stmtIngresos = $this->_db->prepare($sql);
            $stmtIngresos->execute($params);
            $ingresos = $stmtIngresos->fetch(PDO::FETCH_ASSOC);
            return $ingresos;
        } catch (PDOException $e) {
            // var_dump($e);
            return false;
        }
    }
    public function gastos($usuario_id, $cuenta_id = null) {
        if (!$this->_db) return false;
        try {
            $sql = 'SELECT SUM(monto) as total FROM gastos WHERE usuario_id = ?';
            $params = [$usuario_id];
            
            if ($cuenta_id !== null) {
                $sql .= ' AND cuenta_id = ?';
                $params[] = $cuenta_id;
            }
            
            $stmtGastos = $this->_db->prepare($sql);
            $stmtGastos->execute($params);
            $gastos = $stmtGastos->fetch(PDO::FETCH_ASSOC);
            return $gastos;
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
