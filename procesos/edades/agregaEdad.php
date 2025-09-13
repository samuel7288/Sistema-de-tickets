<?php 

session_start();
	require_once "../../clases/Conexion.php";
	require_once "../../clases/Edades.php";

	$obj= new edades();
	
	// Validar y limpiar datos de entrada
	$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
	$edadMin = isset($_POST['edadMin']) ? trim($_POST['edadMin']) : '';
	$edadMax = isset($_POST['edadMax']) ? trim($_POST['edadMax']) : '';
	
	// Validaciones básicas
	if (empty($nombre) || empty($edadMin) || empty($edadMax)) {
		echo "ERROR: Todos los campos son obligatorios";
		exit;
	}
	
	if (strlen($nombre) < 2) {
		echo "ERROR: El nombre debe tener al menos 2 caracteres";
		exit;
	}

	$datos=array(
			$nombre,
			$edadMin,
			$edadMax			
				);

	$resultado = $obj->agregaEdad($datos);
	
	// Si el resultado es un string que comienza con "ERROR:", es un mensaje de error
	if (is_string($resultado) && strpos($resultado, 'ERROR:') === 0) {
		echo $resultado;
	} else {
		echo $resultado; // 1 para éxito, 0 para error de BD
	}

	
	
 ?>