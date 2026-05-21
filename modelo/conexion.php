<?php
class Conexion
{
    protected $conn;
    public function __construct(){
        $this->conn = new mysqli("localhost","root","","bdic");
		if($this->conn->connect_errno)
		{
			echo "No hay conexión: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
		$this->conn->set_charset("utf8");
    }
}
?>
