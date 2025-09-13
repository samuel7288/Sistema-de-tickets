<?php 

session_start();
	require_once "../../clases/Conexion.php";
	require_once "../../clases/Edades.php";

	$obj= new edades();
	
	// Validar y limpiar datos de entrada
	$idEdad = isset($_POST['idedadU']) ? trim($_POST['idedadU']) : '';
	$nombre = isset($_POST['nombreU']) ? trim($_POST['nombreU']) : '';
	$edadMin = isset($_POST['edadMinU']) ? trim($_POST['edadMinU']) : '';
	$edadMax = isset($_POST['edadMaxU']) ? trim($_POST['edadMaxU']) : '';
	
	// Validaciones básicas
	if (empty($idEdad) || empty($nombre) || empty($edadMin) || empty($edadMax)) {
		echo "ERROR: Todos los campos son obligatorios";
		exit;
	}
	
	if (strlen($nombre) < 2) {
		echo "ERROR: El nombre debe tener al menos 2 caracteres";
		exit;
	}

	$datos=array(
			$idEdad,
			$nombre,
			$edadMin,
			$edadMax	
				);

	$resultado = $obj->actualizaEdad($datos);
	
	// Si el resultado es un string que comienza con "ERROR:", es un mensaje de error
	if (is_string($resultado) && strpos($resultado, 'ERROR:') === 0) {
		echo $resultado;
	} else {
		echo $resultado; // 1 para éxito, 0 para error de BD
	}

	
	
 ?>