<?php
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once "../config/conexion.php";

$start = mysqli_real_escape_string($conexion, $_GET['start']);
$end = mysqli_real_escape_string($conexion, $_GET['end']);

// Modificar la consulta principal para ser más precisa
$sql = "SELECT DATE(fechaCompra) as fecha, COUNT(*) as total 
        FROM ventas 
        WHERE fechaCompra >= '$start 00:00:00' 
        AND fechaCompra <= '$end 23:59:59'
        GROUP BY DATE(fechaCompra) 
        ORDER BY fecha";

$result = mysqli_query($conexion, $sql);
$datos_reales = [];
while($row = mysqli_fetch_assoc($result)) {
    $datos_reales[$row['fecha']] = intval($row['total']);
}

// Modificar la consulta para el día actual para incluir todo el día
$hoy = date('Y-m-d');
$sql_hoy = "SELECT COUNT(*) as total_ventas 
            FROM ventas 
            WHERE fechaCompra >= '$hoy 00:00:00' 
            AND fechaCompra <= '$hoy 23:59:59'";
$result_hoy = mysqli_query($conexion, $sql_hoy);
$row_hoy = mysqli_fetch_assoc($result_hoy);
$total_ventas_hoy = $row_hoy['total_ventas'];

// Generar array de todas las fechas en el rango
$fechas = [];
$tickets = [];
$fecha_actual = new DateTime($start);
$fecha_fin = new DateTime($end);

while($fecha_actual <= $fecha_fin) {
    $fecha_str = $fecha_actual->format('Y-m-d');
    $fechas[] = $fecha_str;
    
    if(isset($datos_reales[$fecha_str])) {
        // Usar datos reales exactos de ese día
        $tickets[] = $datos_reales[$fecha_str];
    } 
    elseif($fecha_str === $hoy) {
        // Para el día actual, usar el total del día actual
        $tickets[] = $total_ventas_hoy;
    }
    else {
        // Para cualquier fecha futura, mostrar 0
        $tickets[] = 0;
    }
    
    $fecha_actual->modify('+1 day');
}

$response = [
    'fechas' => $fechas,
    'tickets' => $tickets
];

header('Content-Type: application/json');
echo json_encode($response);
?>