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

	// Métodos para sistema de anulación de tickets
	
	public function buscarTicketParaAnular($criterio, $valor){
		$c = new conectar();
		$conexion = $c->conexion();
		
		$sql = "";
		
		switch($criterio) {
			case 'numero_ticket':
				$sql = "SELECT v.*, u.nombre as usuario_venta, t.nombre as ticket_nombre 
						FROM ventas v 
						LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
						LEFT JOIN tickets t ON v.id_ticket = t.id_ticket 
						WHERE v.numero_ticket = '$valor' AND v.estado = 'ACTIVO'";
				break;
			case 'documento':
				$sql = "SELECT v.*, u.nombre as usuario_venta, t.nombre as ticket_nombre 
						FROM ventas v 
						LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
						LEFT JOIN tickets t ON v.id_ticket = t.id_ticket 
						WHERE v.documento_cliente = '$valor' AND v.estado = 'ACTIVO'";
				break;
			case 'fecha_hora':
				$fecha_hora = explode(' ', $valor);
				$fecha = $fecha_hora[0];
				$hora = isset($fecha_hora[1]) ? $fecha_hora[1] : '';
				
				if($hora) {
					$sql = "SELECT v.*, u.nombre as usuario_venta, t.nombre as ticket_nombre 
							FROM ventas v 
							LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
							LEFT JOIN tickets t ON v.id_ticket = t.id_ticket 
							WHERE v.fechaCompra = '$fecha' AND v.horaCompra = '$hora' AND v.estado = 'ACTIVO'";
				} else {
					$sql = "SELECT v.*, u.nombre as usuario_venta, t.nombre as ticket_nombre 
							FROM ventas v 
							LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
							LEFT JOIN tickets t ON v.id_ticket = t.id_ticket 
							WHERE v.fechaCompra = '$fecha' AND v.estado = 'ACTIVO'";
				}
				break;
		}
		
		$result = mysqli_query($conexion, $sql);
		$tickets = array();
		
		while($row = mysqli_fetch_assoc($result)) {
			$tickets[] = $row;
		}
		
		return $tickets;
	}
	
	public function anularTicket($idVenta, $motivo, $idUsuarioAnulacion){
		$c = new conectar();
		$conexion = $c->conexion();
		
		$fechaAnulacion = date('Y-m-d H:i:s');
		
		$sql = "UPDATE ventas SET 
				estado = 'ANULADO',
				id_usuario_anulacion = '$idUsuarioAnulacion',
				fecha_anulacion = '$fechaAnulacion',
				motivo_anulacion = '$motivo'
				WHERE id_venta = '$idVenta' AND estado = 'ACTIVO'";
		
		return mysqli_query($conexion, $sql);
	}
	
	public function verificarTicketAnulable($idVenta){
		$c = new conectar();
		$conexion = $c->conexion();
		
		$sql = "SELECT * FROM ventas WHERE id_venta = '$idVenta' AND estado = 'ACTIVO'";
		$result = mysqli_query($conexion, $sql);
		
		return mysqli_num_rows($result) > 0;
	}
	
	public function obtenerHistorialAnulaciones($filtro = ''){
		$c = new conectar();
		$conexion = $c->conexion();
		
		$whereClause = "WHERE v.estado = 'ANULADO'";
		
		if($filtro) {
			$whereClause .= " AND (v.numero_ticket LIKE '%$filtro%' OR v.documento_cliente LIKE '%$filtro%')";
		}
		
		$sql = "SELECT v.*, 
				u_venta.nombre as usuario_venta,
				u_anulacion.nombre as usuario_anulacion,
				t.nombre as ticket_nombre
				FROM ventas v
				LEFT JOIN usuarios u_venta ON v.id_usuario = u_venta.id_usuario
				LEFT JOIN usuarios u_anulacion ON v.id_usuario_anulacion = u_anulacion.id_usuario
				LEFT JOIN tickets t ON v.id_ticket = t.id_ticket
				$whereClause
				ORDER BY v.fecha_anulacion DESC";
		
		$result = mysqli_query($conexion, $sql);
		$anulaciones = array();
		
		while($row = mysqli_fetch_assoc($result)) {
			$anulaciones[] = $row;
		}
		
		return $anulaciones;
	}
}

?>