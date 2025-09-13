<?php 

	class conectar{
		private $servidor;
		private $usuario;
		private $password;
		private $bd;
		private $puerto;

		public function __construct() {
			if (isset($_SERVER['RAILWAY_ENVIRONMENT'])) {
				// Configuración para Railway (producción)
				$this->servidor = $_SERVER['MYSQLHOST'] ?? $_ENV['MYSQLHOST'] ?? 'localhost';
				$this->usuario = $_SERVER['MYSQLUSER'] ?? $_ENV['MYSQLUSER'] ?? 'root';
				$this->password = $_SERVER['MYSQLPASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? '';
				$this->bd = $_SERVER['MYSQLDATABASE'] ?? $_ENV['MYSQLDATABASE'] ?? 'railway';
				$this->puerto = $_SERVER['MYSQLPORT'] ?? $_ENV['MYSQLPORT'] ?? '3306';
			} else {
				// Configuración local (XAMPP)
				$this->servidor = "localhost";
				$this->usuario = "root";
				$this->password = "";
				$this->bd = "tiquetera2";
				$this->puerto = "3306";
			}
		}

		public function conexion(){
			$conexion = mysqli_connect($this->servidor,
									  $this->usuario,
									  $this->password,
									  $this->bd,
									  $this->puerto);
			
			if (!$conexion) {
				die("Error de conexión: " . mysqli_connect_error());
			}
			
			mysqli_set_charset($conexion, 'utf8');
			return $conexion;
		}
	}

?>