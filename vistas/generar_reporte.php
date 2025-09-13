<?php
session_start();
if(!isset($_SESSION['usuario'])) {
    header("location:../index.php");
    exit;
}

require_once "../vendor/autoload.php";
require_once "../config/conexion.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true); // Habilitar carga de imágenes remotas
$options->setChroot(getcwd()); // Establecer el directorio raíz

$dompdf = new Dompdf($options);

$fecha_actual = date('Y-m-d');

// Obtener datos de la base de datos
$sql = "SELECT COUNT(*) as total_ventas FROM ventas WHERE fechaCompra = '$fecha_actual'";
$result = mysqli_query($conexion, $sql);
$row = mysqli_fetch_assoc($result);
$total_ventas = $row['total_ventas'];

$sql_revenue = "SELECT SUM(precio) as total_dinero FROM ventas WHERE fechaCompra = '$fecha_actual'";
$result_revenue = mysqli_query($conexion, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($result_revenue);
$total_dinero = $row_revenue['total_dinero'];

// Obtener los tickets más vendidos
$sql_top_tickets = "SELECT 
    t.nombre,
    COUNT(*) as total_vendidos,
    SUM(v.precio) as total_ingresos
    FROM ventas v
    JOIN tickets t ON v.id_ticket = t.id_ticket
    GROUP BY t.id_ticket
    ORDER BY total_vendidos DESC
    LIMIT 5";
$result_top_tickets = mysqli_query($conexion, $sql_top_tickets);

// Get the CSS content
$css = file_get_contents('../css/styles.css');

// Cambiar la forma de cargar la imagen
$rutaLogo = realpath(__DIR__ . '/../img/logo2.jpg');
$imageData = base64_encode(file_get_contents($rutaLogo));

// Preparar el contenido HTML con diseño optimizado para una página
$html = '
<html>
<head>
    <style>
        /* Estilos inline para garantizar la compatibilidad con PDF */
        .report-container { padding: 10px; font-size: 12px; font-family: Arial, sans-serif; }
        .report-header { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #3498db; }
        .report-logo { width: 80px; height: auto; margin-right: 15px; }
        .report-title { color: #2c3e50; font-size: 18px; margin: 0; }
        .report-info { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .report-info p { margin: 5px 0; }
        .report-category { padding: 8px; margin: 5px 0; border-left: 3px solid #3498db; }
        .report-category-title { font-weight: bold; margin-bottom: 5px; }
        .category-stats { display: flex; justify-content: space-between; }
        .report-total { margin-top: 15px; text-align: right; border-top: 1px solid #3498db; padding-top: 10px; }
        .stat-value { color: #2c3e50; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 5px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <img src="data:image/jpeg;base64,' . $imageData . '" class="report-logo" alt="Logo">
            <h1 class="report-title">Reporte de Ventas Diario - ' . date('d/m/Y') . '</h1>
        </div>
        
        <div class="report-info">
            <table>
                <tr>
                    <td width="50%"><strong>Total de Tickets:</strong> ' . number_format($total_ventas) . '</td>
                    <td width="50%"><strong>Ingresos Totales:</strong> $' . number_format($total_dinero, 2) . '</td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Tickets</th>
                    <th>Ingresos</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>';

// Obtener y mostrar datos por categoría de manera más dinámica
$categorias = [
    ["Niños", "id_edad = 1", "#3498db"],
    ["Adultos", "id_edad = 2", "#2ecc71"],
    ["Adultos Mayores", "id_edad = 3", "#e74c3c"]
];

foreach($categorias as $categoria) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(precio) as ingresos
            FROM ventas 
            WHERE {$categoria[1]} 
            AND fechaCompra = '$fecha_actual'";
    
    $result = mysqli_query($conexion, $sql);
    $row = mysqli_fetch_assoc($result);
    
    $porcentaje = $total_ventas > 0 ? ($row['total'] / $total_ventas) * 100 : 0;

    $html .= '<tr>
        <td>' . $categoria[0] . '</td>
        <td>' . number_format($row['total']) . '</td>
        <td>$' . number_format($row['ingresos'], 2) . '</td>
        <td>' . number_format($porcentaje, 1) . '%</td>
    </tr>';
}

// Agregar estadísticas adicionales si están disponibles
$sql_promedio = "SELECT AVG(precio) as promedio FROM ventas WHERE fechaCompra = '$fecha_actual'";
$result_promedio = mysqli_query($conexion, $sql_promedio);
$row_promedio = mysqli_fetch_assoc($result_promedio);

$html .= '</tbody></table>

        <div class="report-section">
            <h2 style="color: #2c3e50; margin: 15px 0;">Top 5 Tickets Más Vendidos</h2>
            <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 8px; text-align: left; border-bottom: 2px solid #3498db;">Ticket</th>
                        <th style="padding: 8px; text-align: right; border-bottom: 2px solid #3498db;">Cantidad</th>
                        <th style="padding: 8px; text-align: right; border-bottom: 2px solid #3498db;">Ingresos</th>
                    </tr>
                </thead>
                <tbody>';

while($ticket = mysqli_fetch_assoc($result_top_tickets)) {
    $html .= '<tr>
        <td style="padding: 8px; border-bottom: 1px solid #eee;">' . $ticket['nombre'] . '</td>
        <td style="padding: 8px; text-align: right; border-bottom: 1px solid #eee;">' . number_format($ticket['total_vendidos']) . '</td>
        <td style="padding: 8px; text-align: right; border-bottom: 1px solid #eee;">$' . number_format($ticket['total_ingresos'], 2) . '</td>
    </tr>';
}

$html .= '</tbody>
            </table>
        </div>

        <div class="report-total">
            <p><strong>Ticket Promedio:</strong> $' . number_format($row_promedio['promedio'], 2) . '</p>
            <p><strong>Total General:</strong> $' . number_format($total_dinero, 2) . '</p>
        </div>
    </div>
</body>
</html>';

// Limpiar completamente todos los buffers de salida
while (ob_get_level()) {
    ob_end_clean();
}

// Configurar headers más seguros
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Reporte_Ventas_'.date('Y-m-d').'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
header('Pragma: private');
header('Expires: 0');

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Enviar el PDF al navegador de forma segura
echo $dompdf->output();
die();
?>