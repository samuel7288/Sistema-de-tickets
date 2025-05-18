<?php 

class ventas{
	public function obtenDatosTicket($idticket){
		$c=new conectar();
		$conexion=$c->conexion();

		$sql = "SELECT 
				    tic.nombre,
				    tic.descripcion,
				    tic.cantidad,
				    img.ruta,
				    tic.precio
				FROM
				    tickets AS tic
				        INNER JOIN
				    imagenes AS img ON tic.id_imagen = img.id_imagen
				        AND tic.id_ticket = '$idticket'";
		$result=mysqli_query($conexion,$sql);

		$ver=mysqli_fetch_row($result);

		$d=explode('/', $ver[3]);

		$img=$d[1].'/'.$d[2].'/'.$d[3];

		$data=array(
			'nombre' => $ver[0],
			'descripcion' => $ver[1],
			'cantidad' => $ver[2],
			'ruta' => $img,
			'precio' => $ver[4]
		);		
		return $data;
	}

	public function crearVenta(){
		$c = new conectar();
        $conexion = $c->conexion();

        $fecha = date('Y-m-d');
        $idventa = self::creaFolio();
        $datos = $_SESSION['tablaComprasTemp'];
        $idusuario = $_SESSION['iduser'];
        $r = 0;

        // Start transaction
        mysqli_begin_transaction($conexion);

        // Process each item in the cart
        foreach ($datos as $key => $value) {
            $d = explode("||", $value);
            $idTicket = $d[0];
            $cantidadVendida = 1; // Set quantity sold to 1

            // Check current stock
            $sqlCheck = "SELECT cantidad FROM tickets WHERE id_ticket = '$idTicket'";
            $resultCheck = mysqli_query($conexion, $sqlCheck);
            $row = mysqli_fetch_assoc($resultCheck);
            $cantidadActual = $row['cantidad'];

            if ($cantidadActual >= 1) {
                // Sufficient stock, proceed to update
                $sqlUpdate = "UPDATE tickets SET cantidad = cantidad - 1 WHERE id_ticket = '$idTicket'";
                $updateResult = mysqli_query($conexion, $sqlUpdate);

                if (!$updateResult) {
                    mysqli_rollback($conexion);
                    return "Error al actualizar la cantidad del ticket ID $idTicket.";
                }

                // Check if quantity reaches zero after update
                $sqlCheckQuantity = "SELECT cantidad FROM tickets WHERE id_ticket = '$idTicket'";
                $resultCheckQuantity = mysqli_query($conexion, $sqlCheckQuantity);
                $rowQuantity = mysqli_fetch_assoc($resultCheckQuantity);

                if ($rowQuantity['cantidad'] == 0) {
                    // Get image path and ID
                    $sqlImage = "SELECT img.ruta, img.id_imagen FROM tickets AS tic 
                                 INNER JOIN imagenes AS img ON tic.id_imagen = img.id_imagen 
                                 WHERE tic.id_ticket = '$idTicket'";
                    $resultImage = mysqli_query($conexion, $sqlImage);
                    $rowImage = mysqli_fetch_assoc($resultImage);
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $rowImage['ruta']; // Use absolute path
                    $imageId = $rowImage['id_imagen'];

                    // Debugging: Confirm image path
                    // echo "Image path: " . $imagePath;

                    // Check if file exists and delete
                    if (file_exists($imagePath)) {
                        if (!unlink($imagePath)) {
                            // Could not delete the file, handle error
                            mysqli_rollback($conexion);
                            return "Error al eliminar la imagen del ticket ID $idTicket.";
                        }
                    } else {
                        // File does not exist
                        // You may choose to log this information
                    }

                    // Delete image record from database
                    $sqlDeleteImage = "DELETE FROM imagenes WHERE id_imagen = '$imageId'";
                    if (!mysqli_query($conexion, $sqlDeleteImage)) {
                        mysqli_rollback($conexion);
                        return "Error al eliminar el registro de imagen para el ticket ID $idTicket.";
                    }

                    // Optionally, delete ticket record
                    $sqlDeleteTicket = "DELETE FROM tickets WHERE id_ticket = '$idTicket'";
                    if (!mysqli_query($conexion, $sqlDeleteTicket)) {
                        mysqli_rollback($conexion);
                        return "Error al eliminar el ticket ID $idTicket.";
                    }
                }
            } else {
                // Insufficient stock
                mysqli_rollback($conexion);
                return "No hay stock disponible para el ticket ID $idTicket.";
            }

            // Insert sale record
            $sql = "INSERT INTO ventas (
                        id_venta,
                        id_edad,
                        id_ticket,
                        id_usuario,
                        precio,
                        fechaCompra
                    ) VALUES (
                        '$idventa',
                        '$d[5]',
                        '$d[0]',
                        '$idusuario',
                        '$d[3]',
                        '$fecha'
                    )";
            $r += mysqli_query($conexion, $sql);
        }

        // Commit transaction
        if (!mysqli_commit($conexion)) {
            return "Error al confirmar la transacción.";
        }

        return $r;
	}

	public function creaFolio(){
		$c= new conectar();
		$conexion=$c->conexion();

		$sql="SELECT id_venta from ventas group by id_venta desc";

		$resul=mysqli_query($conexion,$sql);
		$id=mysqli_fetch_row($resul)[0];

		if($id=="" or $id==null or $id==0){
			return 1;
		}else{
			return $id + 1;
		}
	}
	public function nombreEdad($idEdad){
		$c= new conectar();
		$conexion=$c->conexion();

		 $sql="SELECT nombre, edadMin
			from edad
			where id_edad='$idEdad'";
		$result=mysqli_query($conexion,$sql);

		$ver=mysqli_fetch_row($result);

		return $ver[0]." ".$ver[1];
	}

	public function obtenerTotal($idVenta) {
		$c = new conectar();
		$conexion = $c->conexion();
		$sql = "SELECT SUM(precio) as total FROM ventas WHERE id_venta='$idVenta'";
		$result = mysqli_query($conexion, $sql);
		$total = mysqli_fetch_row($result)[0];
		return $total;
	}
}

?>