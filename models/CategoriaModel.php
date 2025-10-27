<?php

class CategoriaModel extends Model {
    private $conn;
    private $lastError;

    public function __construct() {
        try {
            $this->conn = new Database();
        } catch (PDOException $e) {
            $this->lastError = "Error de conexión: " . $e->getMessage();
            error_log($this->lastError);
            $this->conn = null;
        }
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function crearCategoria($nombre, $icono, $color, $tipo) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return false;
        }
        if (empty($nombre) || empty($icono) || empty($color) || empty($tipo)) {
            $this->lastError = "Todos los campos son obligatorios";
            return false;
        }
        if (!in_array($tipo, ['ingreso', 'gasto'])) {
            $this->lastError = "El tipo debe ser 'ingreso' o 'gasto'";
            return false;
        }
        try {
            $stmt = $this->conn->prepare('INSERT INTO categorias (nombre, icono, color, tipo) VALUES (?, ?, ?, ?)');
            $result = $stmt->execute([$nombre, $icono, $color, $tipo]);
            if (!$result) {
                $this->lastError = "Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo());
                return false;
            }
            return true;
        } catch (PDOException $e) {
            $this->lastError = "Error al crear la categoría: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    public function editarCategoria($id, $nombre, $icono, $color, $tipo) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return false;
        }
        if (empty($id) || empty($nombre) || empty($icono) || empty($color) || empty($tipo)) {
            $this->lastError = "Todos los campos son obligatorios";
            return false;
        }
        if (!in_array($tipo, ['ingreso', 'gasto'])) {
            $this->lastError = "El tipo debe ser 'ingreso' o 'gasto'";
            return false;
        }
        try {
            $stmt = $this->conn->prepare('UPDATE categorias SET nombre = ?, icono = ?, color = ?, tipo = ? WHERE id = ?');
            $result = $stmt->execute([$nombre, $icono, $color, $tipo, $id]);
            if (!$result) {
                $this->lastError = "Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo());
                return false;
            }
            return true;
        } catch (PDOException $e) {
            $this->lastError = "Error al editar la categoría: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    public function obtenerPorId($id) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return null;
        }
        try {
            $stmt = $this->conn->prepare('SELECT * FROM categorias WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = "Error al obtener la categoría: " . $e->getMessage();
            error_log($this->lastError);
            return null;
        }
    }

    public function listarTodas() {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return [];
        }
        try {
            $stmt = $this->conn->prepare('SELECT * FROM categorias ORDER BY nombre');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = "Error al listar las categorías: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }

    public function listarPorTipo($tipo) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return [];
        }
        if (!in_array($tipo, ['ingreso', 'gasto'])) {
            $this->lastError = "El tipo debe ser 'ingreso' o 'gasto'";
            return [];
        }
        try {
            $stmt = $this->conn->prepare('SELECT * FROM categorias WHERE tipo = ? ORDER BY nombre');
            $stmt->execute([$tipo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = "Error al listar las categorías por tipo: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }

    public function actualizarCategoria($id, $nombre, $icono, $color, $tipo) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return false;
        }
        if (empty($id) || empty($nombre) || empty($icono) || empty($color) || empty($tipo)) {
            $this->lastError = "Todos los campos son obligatorios";
            return false;
        }
        if (!in_array($tipo, ['ingreso', 'gasto'])) {
            $this->lastError = "El tipo debe ser 'ingreso' o 'gasto'";
            return false;
        }
        try {
            $stmt = $this->conn->prepare('UPDATE categorias SET nombre = ?, icono = ?, color = ?, tipo = ? WHERE id = ?');
            $result = $stmt->execute([$nombre, $icono, $color, $tipo, $id]);
            if (!$result) {
                $this->lastError = "Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo());
                return false;
            }
            return true;
        } catch (PDOException $e) {
            $this->lastError = "Error al editar la categoría: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    public function eliminarCategoria($id) {
        if (!$this->conn) {
            $this->lastError = "No hay conexión a la base de datos";
            return false;
        }
        if (empty($id)) {
            $this->lastError = "ID de categoría no proporcionado";
            return false;
        }
        try {
            $stmt = $this->conn->prepare('DELETE FROM categorias WHERE id = ?');
            $result = $stmt->execute([$id]);
            if (!$result) {
                $this->lastError = "Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo());
                return false;
            }
            return true;
        } catch (PDOException $e) {
            $this->lastError = "Error al eliminar la categoría: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }
}
