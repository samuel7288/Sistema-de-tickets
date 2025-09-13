<?php
session_start();
echo "<h3>DEBUG - Información de Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>DEBUG - Datos POST Recibidos:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Verificar autenticación
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "<h3 style='color: red;'>ERROR - Acceso denegado</h3>";
    echo "Usuario: " . ($_SESSION['usuario'] ?? 'no definido') . "<br>";
    echo "Rol: " . ($_SESSION['rol'] ?? 'no definido') . "<br>";
    exit;
}

if(!isset($_SESSION['iduser'])) {
    echo "<h3 style='color: red;'>ERROR - ID de usuario no disponible</h3>";
    exit;
}

// Verificar datos POST
$idVenta = isset($_POST['idVenta']) ? $_POST['idVenta'] : '';
$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : '';

if(empty($idVenta)) {
    echo "<h3 style='color: red;'>ERROR - ID de venta no especificado</h3>";
    exit;
}

if(empty(trim($motivo))) {
    echo "<h3 style='color: red;'>ERROR - Motivo vacío</h3>";
    exit;
}

echo "<h3 style='color: green;'>✅ Todas las validaciones iniciales pasaron</h3>";
echo "ID Venta: " . $idVenta . "<br>";
echo "Motivo: " . $motivo . "<br>";
echo "ID Usuario Anulación: " . $_SESSION['iduser'] . "<br>";

// Ahora probar la conexión y la clase
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

echo "<h3>Probando conexión a BD...</h3>";
$c = new conectar();
$conexion = $c->conexion();

if($conexion) {
    echo "✅ Conexión a BD exitosa<br>";
} else {
    echo "❌ Error en conexión a BD<br>";
    exit;
}

echo "<h3>Probando clase Ventas...</h3>";
$obj = new ventas();

// Verificar si el ticket existe
echo "Verificando si el ticket es anulable...<br>";
$anulable = $obj->verificarTicketAnulable($idVenta);

if($anulable) {
    echo "✅ Ticket es anulable<br>";
} else {
    echo "❌ Ticket no es anulable o no existe<br>";
    exit;
}

// Proceder con la anulación
echo "<h3>Procediendo con la anulación...</h3>";
$resultado = $obj->anularTicket($idVenta, $motivo, $_SESSION['iduser']);

if($resultado) {
    echo "<h3 style='color: green;'>✅ ¡ANULACIÓN EXITOSA!</h3>";
    echo "1"; // Esto es lo que espera el JavaScript
} else {
    echo "<h3 style='color: red;'>❌ Error en la anulación</h3>";
    echo "Error: No se pudo anular el ticket.";
}
?>