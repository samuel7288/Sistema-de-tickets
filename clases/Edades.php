<?php 

	class edades{

		public function validarRangoEdad($edadMin, $edadMax, $nombre, $idEdadActual = null){
			$c= new conectar();
			$conexion=$c->conexion();
			
			// Validaciones básicas
			if (!is_numeric($edadMin) || !is_numeric($edadMax)) {
				return "ERROR: Las edades deben ser números válidos";
			}
			
			$edadMin = intval($edadMin);
			$edadMax = intval($edadMax);
			
			if ($edadMin < 0 || $edadMax < 0) {
				return "ERROR: Las edades no pueden ser números negativos";
			}
			
			if ($edadMin > 150 || $edadMax > 150) {
				return "ERROR: Las edades no pueden ser mayores a 150 años";
			}
			
			if ($edadMin >= $edadMax) {
				return "ERROR: La edad mínima ($edadMin) debe ser menor que la edad máxima ($edadMax)";
			}
			
			// Verificar solapamiento con rangos existentes
			$whereClause = $idEdadActual ? "WHERE id_edad != '$idEdadActual'" : "";
			$sql = "SELECT id_edad, nombre, edadMin, edadMax FROM edad $whereClause";
			$result = mysqli_query($conexion, $sql);
			
			while ($row = mysqli_fetch_assoc($result)) {
				$existeMin = intval($row['edadMin']);
				$existeMax = intval($row['edadMax']);
				
				// Verificar solapamiento
				if (($edadMin >= $existeMin && $edadMin <= $existeMax) || 
					($edadMax >= $existeMin && $edadMax <= $existeMax) ||
					($edadMin <= $existeMin && $edadMax >= $existeMax)) {
					return "ERROR: El rango de edad ($edadMin-$edadMax) se solapa con el rango existente '{$row['nombre']}' ($existeMin-$existeMax)";
				}
			}
			
			return "VALIDO";
		}

		public function agregaEdad($datos){
			$c= new conectar();
			$conexion=$c->conexion();

			$idusuario=$_SESSION['iduser'];
			
			// Validar datos antes de insertar
			$validacion = $this->validarRangoEdad($datos[1], $datos[2], $datos[0]);
			if ($validacion !== "VALIDO") {
				return $validacion; // Retorna el mensaje de error
			}

			$sql="INSERT into edad (id_usuario,
										nombre,
										edadMin,
										edadMax)
							values ('$idusuario',
									'$datos[0]',
									'$datos[1]',
									'$datos[2]')";
			return mysqli_query($conexion,$sql);	
		}

		public function obtenDatosEdad($idedad){
			$c= new conectar();
			$conexion=$c->conexion();

			$sql="SELECT id_edad, 
							nombre,
							edadMin,
							edadMax							
				from edad WHERE id_edad='$idedad'";
			$result=mysqli_query($conexion,$sql);
			
			if($result && mysqli_num_rows($result) > 0) {
				$ver=mysqli_fetch_row($result);

				$datos=array(
						'id_edad' => $ver[0], 
						'nombre' => $ver[1],
						'edadMin' => $ver[2],
						'edadMax' => $ver[3]					
							);
				return $datos;
			}
			return null;
		}

		public function actualizaEdad($datos){
			$c= new conectar();
			$conexion=$c->conexion();
			
			// Validar datos antes de actualizar
			$validacion = $this->validarRangoEdad($datos[2], $datos[3], $datos[1], $datos[0]);
			if ($validacion !== "VALIDO") {
				return $validacion; // Retorna el mensaje de error
			}
			
			$sql="UPDATE edad set nombre='$datos[1]',
										edadMin='$datos[2]',
										edadMax='$datos[3]'										
								where id_edad='$datos[0]'";
			return mysqli_query($conexion,$sql);
		}

		public function eliminaEdad($idedad){
			$c= new conectar();
			$conexion=$c->conexion();

			$sql="DELETE from edad where id_edad='$idedad'";

			return mysqli_query($conexion,$sql);
		}
	}

 ?>