<?php 
	session_start();
	require "conexion.php";

	class Sintoma_model extends Conexion{
		private $param = array();
	    public function __construct(){
	    	parent::__construct();			
	    }	

	    public function gestionar($param){
	    	$this->param = $param;
	    	switch ($this->param['param_opcion'])
			{	
				case 'listaSintomas':
					echo $this->listaSintomas();
					break;	
				case 'datosSintoma':
					echo $this->datosSintoma();
					break;
				case 'modificarSintoma':
					echo $this->modificarSintoma();
					break;		
				case 'eliminarSintoma':
					echo $this->eliminarSintoma();
					break;				
			}
	    }	
	    private function listaSintomas(){
	    	$c = "<table WIDTH='100%'><tr><td>";
			$c .= "<div id='contenidolista'><table WIDTH='100%'>";
			$c .= "<tr><th>Sintoma</th></tr>";
		    $sql = "select sintoma.idsintoma,sintoma.sintoma from sintoma";
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idsintoma = $mostrar['idsintoma'];
				$sintoma = $mostrar['sintoma'];
				$c .= "<td>$sintoma</td>";
				$c .= "<td><a href=\"javascript:datosSintoma($idsintoma)\" >Ver</a></td></tr>";
			}			
			$c .= "</table></div>";			
			$c .= "</td><td><div id='contenidosintoma' style=\"border: thin solid black\"></div></td></table>";			
		    return $c;
	    }
		
	    private function datosSintoma(){
			$idsintoma = $this->param['param_idsintoma'];
	    	$c = "<table>";
		    $sql = "select * from sintoma s where s.idsintoma=$idsintoma";
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$sintoma = $mostrar['sintoma'];
				$c .= "<tr><td>IdSintoma:</td><td>$idsintoma</td></tr>";			
				$c .= "<tr><td>Sintoma:</td><td><input type='text' id='txtsintoma'  value='$sintoma' size=\"35\"></td></tr>";
				$c .= "<tr><td></td><td><a href=\"javascript:modificarSintomaaccion($idsintoma)\" >Modificar</a></td></tr>";
			}			
			$c .= "<table>";			
		    return $c;
	    }
	    private function modificarSintoma(){
			$idsintoma = $this->param['param_idsintoma'];
			$sintoma = $this->param['param_sintoma'];
			$sql = "update sintoma set sintoma.sintoma='$sintoma' where idsintoma=$idsintoma";
			mysqli_query($this->conn,$sql);
	    	return $this->listaSintomas();			
	    }
	    private function eliminarSintoma(){
			$idsintoma = $this->param['param_idsintoma'];
			$sql = "delete from sintoma where idsintoma=$idsintoma";
			mysqli_query($this->conn,$sql);
	    	return $this->listaSintomas();			
	    }
	}
 ?>