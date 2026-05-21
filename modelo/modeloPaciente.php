<?php 
	session_start();
	require "conexion.php";
	//require "pChart/pData.class";
	//require "pChart/pChart.class";
	
	class Paciente_model extends Conexion{
		private $param = array();
	    public function __construct(){
	    	parent::__construct();			
	    }	

	    public function gestionar($param){
	    	$this->param = $param;
	    	switch ($this->param['param_opcion'])
			{	
				case 'realizarTest':
					return $this->realizarTest();
					break;
				case 'realizarDiagnostico':
					return $this->realizarDiagnostico();
					break;
				case 'misdatosPaciente':
					return $this->misdatosPaciente();
					break;
				case 'evaluacionPaciente':
					echo $this->evaluacionPaciente();
					break;	
				case 'misEvaluaciones':
					echo $this->misEvaluaciones();
					break;	
				case 'misreportesPaciente':
					echo $this->misreportesPaciente();
					break;	
				case 'listaPacientes':
					echo $this->listaPacientes();
					break;	
				case 'listaPacienteslike':
					echo $this->listaPacienteslike();
					break;	
				case 'modificarPacientes':
					echo $this->modificarPacientes();
					break;	
				case 'eliminarPacientes':
					echo $this->eliminarPacientes();
					break;	
				case 'agregarPacienteaccion':
					return $this->agregarPacienteaccion();
					break;
				case 'cargarVistaAereo':
					return $this->cargarVistaAereo();
					break;
				case 'procesarAereo':
					return $this->procesarAereo();
					break;
			}
	    }
		private function evaluarprolog($pares){
			if(!file_exists("seic.pl"))
				  die("No se puede localizar el archivo seic.pl, el directorio actual es: ".__DIR__);
			$X = '[';
			$i=1;
			foreach ($pares as $par) {
				if(strlen($par)>1){
					list($idsintoma,$r) = explode(";",$par);
					if ($r=='s'){
						$X .= 's'.$i.',';
					}
				}
				$i++;
			}
			if (substr($X, -1, 1)==',')
				$X = substr($X, 0, strlen($X)-1);
			$X .= ']';
			/*$X = '[s1,s4,s10,s11]';*/
			$output = `swipl -s seic.pl -g "test($X)." -t halt.`;
			return $output;//'ICTUS - Hemorragico';//
			//ICTUS - Isquémico
		}
		private function realizarDiagnostico(){
			$idpaciente = $_SESSION['param_idpaciente'];			
			$pares = explode("#", $this->param['param_lista']);
			$resultado = $this->evaluarprolog($pares);
			$sql = "insert into atencion(idpaciente,fechahora,resultado) values($idpaciente,(select current_timestamp()),'$resultado')";
		    mysqli_query($this->conn,$sql);
			
	    	foreach ($pares as $par) {
				if(strlen($par)>1){
					list($idsintoma,$r) = explode(";",$par);
					$sql = "insert into atencion_sintoma(idatencion,idsintoma,respuesta) values((select max(a.idatencion) from atencion a),$idsintoma,'$r')";
					mysqli_query($this->conn,$sql);
				}
			}
		    return $resultado;
	    }
		private function realizarTest(){
			$idpaciente = $_SESSION['param_idpaciente'];
	    	$c = "<table WIDTH='100%'>";
	    	$c .= "<tr><th>Síntomas</th><th>Lo padece?</th></tr>";
		    $sql = "select * from sintoma order by idsintoma";
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idsintoma = $mostrar['idsintoma'];				
				$sintoma = $mostrar['sintoma'];
				$c .= "<tr><td>$sintoma</td>";
				$c .= "<td><input type=\"checkbox\" name=\"sintoma\" value=\"$idsintoma\"></td></tr>";
			}		
			$c .= "<tr><td></td><td><a href=\"javascript:realizardiagnostico($idpaciente)\">Evaluar Diagnóstico</a></td></tr>";
			$c .= "</table>";
			$c .= "<div id='resultado'></div>";
		    return $c;
	    }
		private function evaluacionPaciente(){
	    	$idatencion = $this->param['param_idatencion'];
			$c = "<table WIDTH='100%'>";
			$c .= "<tr><th>Síntoma</th><th>Respuesta</th></tr>";
		    $sql = "select a.fechahora,a.resultado,s.sintoma,upper(as_.respuesta) as respuesta from atencion a,atencion_sintoma as_,sintoma s where a.idatencion=as_.idatencion and as_.idsintoma=s.idsintoma and a.idatencion=$idatencion order by s.idsintoma";			
		    $resultado=mysqli_query($this->conn,$sql);
			$fechahora = '';
			$mresultado = '';
			while($mostrar=mysqli_fetch_array($resultado)){
				$sintoma = $mostrar['sintoma'];
				$respuesta = $mostrar['respuesta'];
				$fechahora = $mostrar['fechahora'];
				$mresultado = $mostrar['resultado'];
				$c .= "<tr><td>$sintoma</td>";
				$c .= "<td>$respuesta</td></tr>";
			}
			$c .= "</table>";			
			$c .= "<hr>";			
			$c .= "Fecha: <b>$fechahora</b><br>Resultado: <b>$mresultado</b>";			
		    return $c;
	    }
		private function misEvaluaciones(){
	    	$idpaciente = $this->param['param_idpaciente'];
			$c = "<table WIDTH='100%' border=\"1\"><tr><td>";
	    	$c .= "<table WIDTH='100%'>";
			$c .= "<tr><th>Fecha atención</th><th>Resultado</th><th>Ver</th></tr>";
		    $sql = "select idatencion,fechahora,resultado from atencion where idpaciente=$idpaciente";
		    $resultado=mysqli_query($this->conn,$sql);
			while($mostrar=mysqli_fetch_array($resultado)){
				$idatencion = $mostrar['idatencion'];
				$fechahora = $mostrar['fechahora'];
				$mresultado = $mostrar['resultado'];
				$c .= "<tr><td>$fechahora</td>";
				$c .= "<td>$mresultado</td>";
				$c .= "<td><a href=\"javascript:evalucionpaciente($idatencion)\" >Ver</a></td></tr>";
			}		
			$c .= "</table>";			
			$c .= "</td><td><div id='contenidodetalle'></div></td></tr></table>";			
		    return $c;
	    }
		
		private function misreportesPaciente(){
			sleep(1);
	    	$idpaciente = $this->param['param_idpaciente'];
			$c = "<table WIDTH='100%'><tr><td>";
	    	$c .= "<table WIDTH='100%'>";
			$c .= "<tr><th>Resultado</th><th>Cantidad</th></tr>";
		    $sql = "select resultado,count(*) as cantidad from atencion where idpaciente=$idpaciente group by resultado";
		    $resultado=mysqli_query($this->conn,$sql);
			while($mostrar=mysqli_fetch_array($resultado)){
				$mresultado = $mostrar['resultado'];
				$cantidad = $mostrar['cantidad'];				
				$c .= "<td>$mresultado</td>";
				$c .= "<td>$cantidad</td></tr>";				
			}		
			$c .= "</table>";			
			$c .= "</td><td><img src=\"../modelo/pie.png\"></td></tr></table>";			
		    return $c;
	    }
		private function listaPacientes(){
	    	$c = "<table WIDTH='100%'><tr><td>";
			$c .= "Buscar Peciente: <input type='text' id='txtbuscarpaciente' name='txtbuscarpaciente' onkeyup=\"checkInput(this);\" >";
	    	$c .= "<div id='contenidolista'><table WIDTH='100%'>";
			$c .= "<tr><th>Apellidos y nombres</th><th>DNI</th><th>Usuario</th><th>Accion</th></tr>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,medico m,usuario u where p.idmedico=m.idmedico and p.idusuario=u.idusuario and u.estado='a' and p.idmedico=".$_SESSION['param_idmedico'];
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$idpaciente = $mostrar['idpaciente'];
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<tr><td>$apellidosynombres</td>";
				$c .= "<td>$dni</td>";
				$c .= "<td>$usuario</td>";
				$c .= "<td><a href=\"javascript:datospaciente($idpaciente)\" >Ver</a></td></tr>";
			}			
			$c .= "</table></div>";			
			$c .= "</td><td><div id='contenidopaciente'></div></td></tr></table>";			
		    return $c;
	    }
		private function listaPacienteslike(){
			$idmedico = $_SESSION['param_idmedico'];
	    	$c = "<table WIDTH='100%'>";
			$c .= "<tr><th>Apellidos y nombres</th><th>DNI</th><th>Usuario</th><th>Acción</th></tr>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,medico m,usuario u where p.idmedico=m.idmedico and p.idusuario=u.idusuario and p.idmedico=$idmedico and u.estado='a' and p.apellidos like '%".$this->param['param_q']."%'";
		    $resultado=mysqli_query($this->conn,$sql);
			while($mostrar=mysqli_fetch_array($resultado)){
				$idpaciente = $mostrar['idpaciente'];
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<tr><td>$apellidosynombres</td>";
				$c .= "<td>$dni</td>";
				$c .= "<td>$usuario</td>";
				$c .= "<td><a href=\"javascript:datosPaciente($idpaciente)\" >Ver</a></td></tr>";
			}
			$c .= "</table>";			
			return $c;
	    }
		private function misdatosPaciente(){
			$idpaciente = $this->param['param_idpaciente'];
	    	$c = "<table>";
		    $sql = "select p.idpaciente,p.apellidos,p.nombres,p.dni,u.usuario from paciente p,usuario u where p.idusuario=u.idusuario and p.idpaciente=$idpaciente";
		    $resultado=mysqli_query($this->conn,$sql);
			
			while($mostrar=mysqli_fetch_array($resultado)){
				$apellidosynombres = $mostrar['apellidos']." ".$mostrar['nombres'];
				$dni = $mostrar['dni'];
				$usuario = $mostrar['usuario'];
				$c .= "<tr><td>IdPaciente:</td><td>$idpaciente</td></tr>";			
				$c .= "<tr><td>Apellidos y Nombres:</td><td>$apellidosynombres</td></tr>";
				$c .= "<tr><td>DNI:</td><td>$dni</td></tr>";
				$c .= "<tr><td>Usuario:</td><td>$usuario</td></tr>";
			}			
			$c .= "<table>";				
		    return $c;
	    }
		private function datosPaciente22(){
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
				$c .= "<tr><td>Apellidos:</td><td><input type='text' id='txtapellidos'  value='$apellidos'></td></tr>";
				$c .= "<tr><td>Nombres:</td><td><input type='text' id='txtnombres' name='txtnombres' value='$nombres'></td></tr>";
				$c .= "<tr><td>DNI:</td><td><input type='text' id='txtdni' name='txtdni' value='$dni'></td></tr>";
				$c .= "<tr><td>Usuario:</td><td><input type='text' id='txtusuario' name='txtusuario' value='$usuario'></td></tr>";
				//$c .= "<tr><td></td><td><a href=\"javascript:modificarPacienteaccion($idpaciente)\" >Modificar</a>-  -<a href=\"javascript:eliminarPacienteaccion($idpaciente)\" >Eliminar</a></td></tr>";
			}			
			$c .= "</table>";			
		    return $c;
	    }
		
		private function agregarPacienteaccion(){
			$idmedico = $_SESSION['param_idmedico'];
			$apellidos = $this->param['param_apellidos'];
			$nombres = $this->param['param_nombres'];
			$dni = $this->param['param_dni'];
			$usuario = $this->param['param_usuario'];
			$sql = "insert into usuario(usuario,clave,rol,estado) values('$usuario',md5('$usuario'),'p','a')";
			mysqli_query($this->conn,$sql);
			sleep(1);
			$sql = "insert into paciente(idmedico,apellidos,nombres,dni,fechahora,idusuario) values($idmedico,'$apellidos','$nombres','$dni',(select current_timestamp()),(select max(u.idusuario) from usuario u))";
			mysqli_query($this->conn,$sql);
	    	$this->param['param_q'] ='';	
			return $this->listaPacienteslike();
	    }
		private function modificarPacientes(){
			$idpaciente = $this->param['param_idpaciente'];
			$apellidos = $this->param['param_apellidos'];
			$nombres = $this->param['param_nombres'];
			$dni = $this->param['param_dni'];
			$usuario = $this->param['param_usuario'];
			$sql = "update paciente set apellidos='$apellidos',nombres='$nombres',dni='$dni' where idpaciente=$idpaciente";
			mysqli_query($this->conn,$sql);
	    	$sql = "update usuario set usuario.usuario='$usuario' where usuario.idusuario=(select paciente.idusuario from paciente where paciente.idpaciente=$idpaciente)";
			mysqli_query($this->conn,$sql);
	    	$this->param['param_q'] ='';			
			return $this->listaPacienteslike();			
	    }
		private function eliminarPacientes(){
			$idpaciente = $this->param['param_idpaciente'];
			//$sql = "delete from paciente where idpaciente=$idpaciente";
			$sql = "update usuario set estado ='i' where idusuario=(select p.idusuario from paciente p where p.idpaciente=$idpaciente)";
			mysqli_query($this->conn,$sql);
	    	$this->param['param_q'] ='';			
			return $this->listaPacienteslike();			
	    }		
		
		

		private function cargarVistaAereo(){
			$html = "<h3>Predicción de Supervivencia</h3>";
			$html .= "<div class='card p-4' style='background: #fff; max-width:600px; margin-top:20px;'>";
			$html .= "<form id='formAereo'>";
			$html .= "<div class='row'>";
			$html .= "<div class='col-md-6 mb-3'><label>Género</label><select name='sex' class='form-control'><option value='male'>Male</option><option value='female'>Female</option></select></div>";
			$html .= "<div class='col-md-6 mb-3'><label>Edad</label><input type='number' name='age' class='form-control' value='25' step='1'></div>";
			$html .= "</div><div class='row'>";
			$html .= "<div class='col-md-6 mb-3'><label>Clase</label><select name='class' class='form-control'><option value='First'>First</option><option value='Second'>Second</option><option value='Third'>Third</option></select></div>";
			$html .= "<div class='col-md-6 mb-3'><label>Tarifa</label><input type='number' name='fare' class='form-control' value='30.0' step='0.1'></div>";
			$html .= "</div><div class='row'>";
			$html .= "<div class='col-md-12 mb-3'><label>Cubierta</label><select name='deck' class='form-control'>";
			$html .= "<option value='unknown'>Unknown</option><option value='A'>A</option><option value='B'>B</option><option value='C'>C</option><option value='D'>D</option><option value='E'>E</option><option value='F'>F</option><option value='G'>G</option>";
			$html .= "</select></div>";
			$html .= "</div>";
			$html .= "<button type='button' id='btnAereo' class='btn btn-primary btn-block' onclick='procesarTestAereo()'>Calcular Probabilidad</button>";
			$html .= "</form></div><br>";
			$html .= "<div id='resultadoAereo'></div>";
			return $html;
		}

		private function procesarAereo(){
			$idpaciente = $_SESSION['param_idpaciente'];
			
			// Captura directa y segura de las variables enviadas por el formulario
			$sex = isset($_POST['sex']) ? $_POST['sex'] : 'male';
			$age = isset($_POST['age']) ? floatval($_POST['age']) : 25.0;
			$fare = isset($_POST['fare']) ? floatval($_POST['fare']) : 30.0;
			$class = isset($_POST['class']) ? $_POST['class'] : 'Third';
			$deck = isset($_POST['deck']) ? $_POST['deck'] : 'unknown';
			
			$data_array = array(
				"sex" => $sex,
				"age" => $age,
				"fare" => $fare,
				"class" => $class,
				"deck" => $deck
			);
			
			$payload = json_encode($data_array);
			$url_flask = "http://127.0.0.1:5001/predict";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_flask);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			// Validamos que el servidor Python realmente esté respondiendo
			if(!$response || $http_code != 200){
				return "<div class='alert alert-danger'><b>Error de conexión:</b> Verifica que el servicio de Python (app.py) esté encendido y corriendo en el puerto 5001.</div>";
			}
			
			$json_res = json_decode($response, true);
			
			if(isset($json_res['status']) && $json_res['status'] == 'error'){
				return "<div class='alert alert-danger'><b>Error en el modelo:</b> " . $json_res['message'] . "</div>";
			}
			
			$estado = $json_res['estado'];
			$probabilidad_pct = round($json_res['probabilidad'] * 100, 2);
			
			$resultado_db = "Aereo: " . $estado . " (" . $probabilidad_pct . "%)";
			$sql = "INSERT INTO atencion(idpaciente, fechahora, resultado) VALUES($idpaciente, (select current_timestamp()), '$resultado_db')";
			mysqli_query($this->conn, $sql);
			
			$clase_alerta = ($estado == "SOBREVIVE") ? "alert-success" : "alert-danger";
			$html = "<div class='alert $clase_alerta' style='max-width:600px;'>";
			$html .= "<h4>Resultado: <b>$estado</b></h4><hr>";
			$html .= "<p>Probabilidad de supervivencia: <b>" . $probabilidad_pct . "%</b></p>";
			$html .= "</div>";
			
			return $html;
		}
	}
 ?>