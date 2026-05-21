<?php 
	session_start();
	require "conexion.php";

	class Medico_model extends Conexion{
		private $param = array();
	    public function __construct(){
	    	parent::__construct();			
	    }	

	    public function gestionar($param){
	    	$this->param = $param;
	    	switch ($this->param['param_opcion'])
			{	
				case 'misdatosMedico':
					echo $this->misdatosMedico();
					break;
				case 'reporteGeneral':
					echo $this->reporteGeneral();
					break;
				case 'datosPaciente':
					echo $this->datosPaciente();
					break;
				case 'listaPacientes':
					echo $this->listaPacientes();
					break;	
				case 'listaPacienteslike':
					echo $this->listaPacienteslike();
					break;
				case 'cargarMatriz':
					return $this->cargarMatriz();
					break;
				case 'agregarSintomaMatriz':
					return $this->agregarSintomaMatriz();
					break;
				case 'agregarEnfermedadMatriz':
					return $this->agregarEnfermedadMatriz();
					break;
				case 'guardarMatriz':
					return $this->guardarMatriz();
					break;
			}
	    }
		private function reporteGeneral(){
			sleep(1);
	    	//$idpaciente = $this->param['param_idpaciente'];
			$c = "<table WIDTH='100%'><tr><td>";
	    	$c .= "<table WIDTH='100%'>";
			$c .= "<tr><th>Resultado</th><th>Cantidad</th></tr>";
		    $sql = "select resultado,count(*) as cantidad from atencion group by resultado";
		    $resultado=mysqli_query($this->conn,$sql);
			while($mostrar=mysqli_fetch_array($resultado)){
				$mresultado = $mostrar['resultado'];
				$cantidad = $mostrar['cantidad'];
				$c .= "<td>$mresultado</td>";
				$c .= "<td>$cantidad</td></tr>";				
			}		
			$c .= "</table>";			
			$c .= "</td><td><img src=\"../modelo/general1.png\"></td></tr></table>";			
		    return $c;
	    }
		private function datosPaciente(){
			$idpaciente = $this->param['param_idpaciente'];
	    	$c = "<table>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,usuario u where p.idusuario=u.idusuario and p.idpaciente=$idpaciente";
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idpaciente = $mostrar['idpaciente'];
				$apellidos = $mostrar['apellidos'];
				$nombres = $mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<tr><td>IdPaciente:</td><td>$idpaciente</td></tr>";			
				$c .= "<tr><td>Apellidos:</td><td><input type='text' id='txtapellidos' size=\"40\" onkeypress=\"return lettersOnly(event)\" value='$apellidos'></td></tr>";
				$c .= "<tr><td>Nombres:</td><td><input type='text' id='txtnombres' name='txtnombres' size=\"40\" onkeypress=\"return lettersOnly(event)\" value='$nombres'></td></tr>";
				$c .= "<tr><td>DNI:</td><td><input type='text' id='txtdni' name='txtdni' size=\"40\" onkeypress=\"return isNumber(event)\" maxlength=\"8\" value='$dni'></td></tr>";
				$c .= "<tr><td>Usuario:</td><td><input type='text' id='txtusuario' name='txtusuario' size=\"40\" onkeypress=\"return lettersOnly(event)\" value='$usuario'></td></tr>";
				$c .= "<tr><td></td><td><a href=\"javascript:modificarPacienteaccion($idpaciente)\" >Modificar</a>-  -<a href=\"javascript:eliminarPacienteaccion($idpaciente)\" >Eliminar</a></td></tr>";
			}			
			$c .= "</table>";			
		    return $c;
	    }
		private function listaPacientes(){
	    	$c = "<table WIDTH='100%'><tr><th>";
			$c .= "Buscar Peciente: <input type='text' id='txtbuscarpaciente' name='txtbuscarpaciente' onkeyup=\"checkInput(this);\" >";
	    	$c .= "<hr>";
			$c .= "<div id='contenidolista'><table WIDTH='100%' >";
			$c .= "<tr><th>Apellidos y nombres</th> <th>DNI</th> <th>Usuario</th> <th>Acción</th> </tr>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,medico m,usuario u where p.idmedico=m.idmedico and p.idusuario=u.idusuario and p.idmedico=".$_SESSION['param_idmedico'];
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idpaciente = $mostrar['idpaciente'];
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<td>$apellidosynombres</td>";
				$c .= "<td>$dni</td>";
				$c .= "<td>$usuario</td>";
				$c .= "<td><a href=\"javascript:datosPaciente($idpaciente)\" >Ver</a></td></tr>";
			}			
			$c .= "</table></div>";			
			$c .= "</th><th><div id='contenidopaciente' style=\"border: thin solid black\"></div></th></tr></table>";			
		    return $c;
	    }
		private function listaPacienteslike(){
			$idmedico = $_SESSION['param_idmedico'];
	    	$c = "<table WIDTH='100%'>";
			$c .= "<tr><th>Apellidos y nombres</th><th>DNI</th><th>Usuario</th><th>Acción</th></tr>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,medico m,usuario u where p.idmedico=m.idmedico and p.idusuario=u.idusuario and p.idmedico=".$_SESSION['param_idmedico']." and p.apellidos like '%".$this->param['param_q']."%'";
		    $resultado=mysqli_query($this->conn,$sql);
			while($mostrar=mysqli_fetch_array($resultado)){
				$idpaciente = $mostrar['idpaciente'];
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<td>$apellidosynombres</td>";
				$c .= "<td>$dni</td>";
				$c .= "<td>$usuario</td>";
				$c .= "<td><a href=\"javascript:datosPaciente($idpaciente)\" >Ver</a></td></tr>";
			}			
			$c .= "</table>";			
			return $c;
	    }
		private function misdatosMedico(){
	    	$c = "<table>";
		    $sql = "select m.idmedico as idmedico,m.nombres as nombres,m.apellidos as apellidos,m.colegiado as colegiado,u.usuario as usuario from medico m,usuario u where u.idusuario=m.idusuario and m.idmedico=".$_SESSION['param_idmedico'];
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idmedico = $mostrar['idmedico'];
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$colegiado = $mostrar['colegiado'];
				$usuario = $mostrar['usuario'];
				$c .= "<tr><td>IdMedico:</td><td>$idmedico</td></tr>";			
				$c .= "<tr><td>Apellidos y Nombres:</td><td>$apellidosynombres</td></tr>";
				$c .= "<tr><td>Colegiado:</td><td>$colegiado</td></tr>";
				$c .= "<tr><td>Usuario:</td><td>$usuario</td></tr>";
			}			
			$c .= "<table>";			
		    return $c;
	    }



		

		private function cargarMatriz(){
			$html = "<h3>1. Catálogo de Síntomas</h3><br>";
			$html .= "<div style='margin-bottom:20px;'><input type='text' id='nuevoSintoma' placeholder='Describir nuevo síntoma' style='width:300px; padding:5px;'> <button type='button' class='btn btn-success' onclick='agregarSintomaMatriz()'>Añadir Síntoma</button></div>";
			
			$sqlSintomas = "SELECT * FROM sintoma ORDER BY idsintoma";
			$resSintomas = mysqli_query($this->conn, $sqlSintomas);
			$sintomas = [];
			
			$html .= "<table class='table table-bordered' style='background:#fff;'>";
			while($row = mysqli_fetch_assoc($resSintomas)){
				$sintomas[] = $row;
				$html .= "<tr><td width='50'><b>S" . $row['idsintoma'] . "</b></td><td>" . $row['sintoma'] . "</td></tr>";
			}
			$html .= "</table><hr>";

			$html .= "<h3>2. Matriz de Enfermedades y Síntomas Asociados</h3><br>";
			$html .= "<div style='margin-bottom:20px;'><input type='text' id='nuevaEnfermedad' placeholder='Nombre de la enfermedad' style='width:300px; padding:5px;'> <button type='button' class='btn btn-success' onclick='agregarEnfermedadMatriz()'>Añadir Enfermedad</button></div>";

			$sqlEnfermedades = "SELECT * FROM enfermedad ORDER BY idenfermedad";
			$resEnfermedades = mysqli_query($this->conn, $sqlEnfermedades);
			
			$html .= "<form id='formMatriz'><div style='overflow-x:auto;'><table class='table table-bordered' style='background:#fff;'><tr><th>Enfermedad</th>";
			foreach($sintomas as $s){
				$html .= "<th>S" . $s['idsintoma'] . "</th>";
			}
			$html .= "</tr>";

			while($enf = mysqli_fetch_assoc($resEnfermedades)){
				$idEnf = $enf['idenfermedad'];
				$html .= "<tr><td style='white-space: nowrap;'><b>" . $enf['nombre'] . "</b></td>";
				foreach($sintomas as $s){
					$idSin = $s['idsintoma'];
					
					$sqlCheck = "SELECT * FROM enfermedad_sintoma WHERE idenfermedad=$idEnf AND idsintoma=$idSin";
					$resCheck = mysqli_query($this->conn, $sqlCheck);
					$checked = (mysqli_num_rows($resCheck) > 0) ? "checked" : "";
					
					$html .= "<td style='text-align:center;'><input type='checkbox' name='matriz[$idEnf][$idSin]' $checked></td>";
				}
				$html .= "</tr>";
			}
			$html .= "</table></div></form><br>";
			$html .= "<button type='button' class='btn btn-primary btn-lg' onclick='guardarMatriz()'>Guardar Matriz y Generar Reglas</button>";

			return $html;
		}

		private function agregarSintomaMatriz(){
			$sintoma = $this->param['param_sintoma'];
			$sql = "INSERT INTO sintoma (sintoma) VALUES ('$sintoma')";
			mysqli_query($this->conn, $sql);
		}

		private function agregarEnfermedadMatriz(){
			$enfermedad = $this->param['param_enfermedad'];
			$sql = "INSERT INTO enfermedad (nombre) VALUES ('$enfermedad')";
			mysqli_query($this->conn, $sql);
		}

		private function guardarMatriz(){
			mysqli_query($this->conn, "DELETE FROM enfermedad_sintoma");
			if(isset($this->param['matriz'])){
				foreach($this->param['matriz'] as $idEnf => $sintomas){
					foreach($sintomas as $idSin => $val){
						mysqli_query($this->conn, "INSERT INTO enfermedad_sintoma (idenfermedad, idsintoma) VALUES ($idEnf, $idSin)");
					}
				}
			}
			$this->generarProlog();
		}

		private function generarProlog(){
			$file = "../controlador/seic.pl";
			$content = ":-dynamic tiene/1.\n\n";
			$content .= "lista([]):-enfermedad(E),write(E).\n";
			$content .= "lista([H|T]):-assert(tiene(H)),lista(T).\n\n";
			$content .= "test(X) :-limpiar,lista(X).\n\n";

			$sql = "SELECT e.nombre, GROUP_CONCAT(es.idsintoma SEPARATOR ',') as sintomas FROM enfermedad e JOIN enfermedad_sintoma es ON e.idenfermedad = es.idenfermedad GROUP BY e.idenfermedad ORDER BY e.idenfermedad";
			$res = mysqli_query($this->conn, $sql);
			while($row = mysqli_fetch_assoc($res)) {
				$sintomas = explode(',', $row['sintomas']);
				$regla = "enfermedad('" . $row['nombre'] . "'):-";
				$condiciones = [];
				foreach($sintomas as $s) {
					$condiciones[] = "tiene(s$s)";
				}
				$regla .= implode(',', $condiciones) . ".\n";
				$content .= $regla;
			}

			$content .= "\nenfermedad('No Determinado (Sin patron claro)').\n\n";
			$content .= "limpiar:-retract(tiene(_)),fail.\n";
			$content .= "limpiar.\n";

			file_put_contents($file, $content);
		}



	}
 ?>