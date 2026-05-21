<?php 
	//session_start();  
	function agregarPaciente(){
	    	$c = "<table>";		    
			$c .= "<tr><td>Apellidos:</td><td><input type='text' id='txtapellidos' size=\"40\" onkeypress=\"return lettersOnly(event)\"></td></tr>";
			$c .= "<tr><td>Nombres:</td><td><input type='text' id='txtnombres' name='txtnombres' size=\"40\" onkeypress=\"return lettersOnly(event)\"></td></tr>";
			$c .= "<tr><td>DNI:</td><td><input type='text' id='txtdni' name='txtdni'  size=\"40\" onkeypress=\"return isNumber(event)\" maxlength=\"8\"></td></tr>";
			$c .= "<tr><td>Usuario:</td><td><input type='text' id='txtusuario' name='txtusuario' size=\"40\" onkeypress=\"return lettersOnly(event)\"></td></tr>";
			$c .= "<tr><td></td><td><a href=\"javascript:agregarPacienteaccion()\" >Agregar</a></td></tr>";					
		    return $c;
	    }
	echo agregarPaciente();	
?>