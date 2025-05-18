<?php 
	session_start();
	$iduser=$_SESSION['iduser'];
	require_once "../../clases/Conexion.php";
	require_once "../../clases/Tickets.php";

	$c = new conectar();
	$conexion = $c->conexion();

	// Validar que todos los campos estén presentes
	if(empty($_POST['nombre']) || empty($_POST['descripcion']) || 
	   empty($_POST['cantidad']) || empty($_POST['precio']) || 
	   empty($_POST['categoriaSelect'])) {
		echo "campos_vacios";
		exit;
	}

	$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
	$descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
	$cantidad = (int)$_POST['cantidad'];
	$precio = (float)$_POST['precio'];
	$categoria = (int)$_POST['categoriaSelect'];

	if(!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
		echo "error_imagen";
		exit;
	}

	$imagen = $_FILES['imagen']['name'];
	$rutaAlmacenamiento = $_FILES['imagen']['tmp_name'];
	$carpeta = '../../archivos/';

	// Crear carpeta si no existe
	if (!file_exists($carpeta)) {
		if (!mkdir($carpeta, 0777, true)) {
			echo "error_carpeta";
			exit;
		}
	}

	// Generar nombre único para la imagen
	$extension = pathinfo($imagen, PATHINFO_EXTENSION);
	$nombreUnico = uniqid() . '.' . $extension;
	$rutaFinal = $carpeta . $nombreUnico;

	if (move_uploaded_file($rutaAlmacenamiento, $rutaFinal)) {
		$sql = "INSERT INTO imagenes (ruta) VALUES (?)";
		$stmt = mysqli_prepare($conexion, $sql);
		mysqli_stmt_bind_param($stmt, "s", $rutaFinal);
		
		if(mysqli_stmt_execute($stmt)) {
			$idImagen = mysqli_insert_id($conexion);

			$sql = "INSERT INTO tickets (nombre, descripcion, cantidad, precio, id_imagen, id_categoria) 
					VALUES (?, ?, ?, ?, ?, ?)";
			$stmt = mysqli_prepare($conexion, $sql);
			mysqli_stmt_bind_param($stmt, "ssidii", $nombre, $descripcion, $cantidad, $precio, $idImagen, $categoria);
			
			if(mysqli_stmt_execute($stmt)) {
				echo "1";
			} else {
				echo "error_tickets";
			}
		} else {
			echo "error_imagen_db";
		}
	} else {
		echo "error_upload";
	}
?>