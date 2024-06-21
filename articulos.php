<?php
use Symfony\Component\Dotenv\Dotenv;
require_once 'vendor/autoload.php';

class articulos {
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        $dotenv = new Dotenv();
        $dotenv->load('.env');
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->database = $_ENV['DB_NAME'];
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Error al conectar a la base de datos: " . $this->conn->connect_error);
        }
    }

    public function getAll() {
        $query = "SELECT * FROM articulos";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else 
            return array();
    }
    public function getById($id) {
        $query = "SELECT * FROM articulos WHERE id = " . $id;
                $result = $this->conn->query($query);
        
                if ($result->num_rows > 0) {
                    return $result->fetch_assoc();
                } else 
                    return null;
            }
        
        public function insertar($data) {
        $id = (int) $data['id'];
        $des = $this->conn->real_escape_string($data['des']);
        $cant = (int) $data['cant'];
        $vr_u = (float) $data['vr_u'];
        if ($this->getById($id) == null) {
        $query = "INSERT INTO articulos (id, des, cant, vru) VALUES ($id, '$des', $cant, $vr_u)";
            $result = $this->conn->query($query);
            return "Articulo agregado: ".json_encode($data);
        }
        else
            return "Ya existe este articulo con el id = ".$id;
    }
    public function delete($id) {
        settype($id, "integer");
        $query = "DELETE FROM articulos WHERE id = ".$id;
        $result = $this->conn->query($query);
        return $result;
    }
    
    public function update($nid, $data) {
        $des = $this->conn->real_escape_string($data['des']);
        $cant = (int) $data['cant'];
        $vr_u = (float) $data['vr_u'];
        if ($this->getById($nid) == null)
            return "No existe este articulo con el id = ".$nid;
        else{
        settype($nid, "integer");
        $query = "UPDATE articulos SET des = '$des', cant = $cant, vru = $vr_u WHERE id = ".$nid;
        $result = $this->conn->query($query);
        return "Articulo actualizado: ".json_encode($data);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

}
