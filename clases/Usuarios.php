<?php 
	class usuarios{
		public function registroUsuario($datos){
			$c=new conectar();
			$conexion=$c->conexion();

			$fecha=date('Y-m-d');

			$sql="INSERT into usuarios (nombre,
								apellido,
								email,
								password,
								rol,
								fechaCaptura)
						values ('$datos[0]',
								'$datos[1]',
								'$datos[2]',
								'$datos[3]',
								'$datos[4]',
								'$fecha')";
			return mysqli_query($conexion,$sql);
		}		public function loginUser($datos){
			$c=new conectar();
			$conexion=$c->conexion();
			$password=sha1($datos[1]);

			$sql="SELECT * 
					from usuarios 
				where email='$datos[0]'
				and password='$password'";
			$result=mysqli_query($conexion,$sql);

			if(mysqli_num_rows($result) > 0){
				$_SESSION['usuario']=$datos[0];
				$idUsuario = self::traeID($datos);
				$rol = self::traeRol($datos);
				
				// Verificar que se obtuvieron correctamente
				if($idUsuario && $rol) {
					$_SESSION['iduser'] = $idUsuario;
					$_SESSION['rol'] = $rol;
					return 1;
				} else {
					// Error al obtener datos del usuario
					return 0;
				}
			}else{
				return 0;
			}
		}		public function traeID($datos){
			$c=new conectar();
			$conexion=$c->conexion();

			$password=sha1($datos[1]);

			$sql="SELECT id_usuario 
					from usuarios 
					where email='$datos[0]'
					and password='$password'"; 
			$result=mysqli_query($conexion,$sql);

			if($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_row($result);
				return $row[0];
			}
			return null;
		}

		public function traeRol($datos){
			$c=new conectar();
			$conexion=$c->conexion();

			$password=sha1($datos[1]);

			$sql="SELECT rol 
					from usuarios 
					where email='$datos[0]'
					and password='$password'"; 
			$result=mysqli_query($conexion,$sql);

			if($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_row($result);
				return $row[0];
			}
			return null;
		}
		public function obtenDatosUsuario($idusuario){

			$c=new conectar();
			$conexion=$c->conexion();

			$sql="SELECT id_usuario,
							nombre,
							apellido,
							email,
							rol
					from usuarios 
					where id_usuario='$idusuario'";
			$result=mysqli_query($conexion,$sql);

			$ver=mysqli_fetch_row($result);

			$datos=array(
						'id_usuario' => $ver[0],
							'nombre' => $ver[1],
							'apellido' => $ver[2],
							'email' => $ver[3],
							'rol' => $ver[4]
						);

			return $datos;
		}
		public function actualizaUsuario($datos){
			$c=new conectar();
			$conexion=$c->conexion();

			$sql="UPDATE usuarios set nombre='$datos[1]',
									apellido='$datos[2]',
									email='$datos[3]',
									rol='$datos[4]'
						where id_usuario='$datos[0]'";
			return mysqli_query($conexion,$sql);	
		}

		public function eliminaUsuario($idusuario){
			$c=new conectar();
			$conexion=$c->conexion();

			$sql="DELETE from usuarios 
					where id_usuario='$idusuario'";
			return mysqli_query($conexion,$sql);
		}
	}

 ?>