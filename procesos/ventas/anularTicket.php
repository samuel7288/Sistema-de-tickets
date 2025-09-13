<?php
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "Error: Acceso denegado. Solo administradores pueden anular tickets.";
    exit;
}

if(!isset($_SESSION['iduser'])) {
    echo "Error: ID de usuario no disponible en la sesión.";
    exit;
}

$obj = new ventas();

$idVenta = isset($_POST['idVenta']) ? $_POST['idVenta'] : '';
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$idUsuarioAnulacion = $_SESSION['iduser'];

// Validar que se recibieron los datos necesarios
if(empty($idVenta)) {
    echo "Error: ID de venta no especificado";
    exit;
}

// Verificar que el ticket existe y está activo
if(!$obj->verificarTicketAnulable($idVenta)) {
    echo "Error: El ticket no existe o ya está anulado";
    exit;
}

// Validar que el motivo no esté vacío y tenga al menos 10 caracteres
if(empty($motivo)) {
    echo "Error: Debe especificar un motivo para la anulación";
    exit;
}

if(strlen($motivo) < 10) {
    echo "Error: El motivo debe tener al menos 10 caracteres";
    exit;
}

// Proceder con la anulación
$resultado = $obj->anularTicket($idVenta, $motivo, $idUsuarioAnulacion);

if($resultado) {
    echo "1"; // Éxito
} else {
    echo "Error: No se pudo anular el ticket. Intente nuevamente.";
}
?>
