<?php 
	//session_start();  
	include_once '../modelo/modeloMedico.php';

	$param = array();
	if(isset($_POST['param_idmedico']))
	{
		$param['param_idmedico'] = $_POST['param_idmedico'];
	}
	if(isset($_POST['param_idpaciente']))
	{
		$param['param_idpaciente'] = $_POST['param_idpaciente'];
	}
	if(isset($_POST['param_q'])){
		$param['param_q'] = $_POST['param_q'];
	}
	$param['param_opcion'] = $_POST['param_opcion'];
	if(isset($_POST['param_sintoma'])) { $param['param_sintoma'] = $_POST['param_sintoma']; }
	if(isset($_POST['param_enfermedad'])) { $param['param_enfermedad'] = $_POST['param_enfermedad']; }
	if(isset($_POST['matriz'])) { $param['matriz'] = $_POST['matriz']; }
	
	$Medico = new Medico_model();
	echo $Medico->gestionar($param);
?>