<?php
// Configuración para Railway y local
if (isset($_SERVER['RAILWAY_ENVIRONMENT'])) {
    // Configuración para Railway (producción)
    $host = $_SERVER['MYSQLHOST'] ?? $_ENV['MYSQLHOST'] ?? 'localhost';
    $user = $_SERVER['MYSQLUSER'] ?? $_ENV['MYSQLUSER'] ?? 'root';
    $password = $_SERVER['MYSQLPASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? '';
    $database = $_SERVER['MYSQLDATABASE'] ?? $_ENV['MYSQLDATABASE'] ?? 'railway';
    $port = $_SERVER['MYSQLPORT'] ?? $_ENV['MYSQLPORT'] ?? '3306';
} else {
    // Configuración local (XAMPP)
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "tiquetera2";
    $port = "3306";
}

// Crear conexión con puerto
$conexion = mysqli_connect($host, $user, $password, $database, $port);

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Configurar charset
mysqli_set_charset($conexion, 'utf8');
?>