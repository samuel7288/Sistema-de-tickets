<?php
// GENERADOR DE REPORTE EXCEL MEJORADO - Exportar datos del dashboard con mejor formato
require_once "../config/error_handler.php";

session_start();
if(!isset($_SESSION['usuario'])) {
    header("location:../index.php");
    exit;
}

require_once "../config/conexion.php";

$fecha_actual = date('Y-m-d');

// Configurar headers para descarga de archivo Excel
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_ventas_' . date('Y-m-d_His') . '.xls');

// Crear el output con HTML para mejor formato
$html = '
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body { font-family: Arial, sans-serif; }
        
        /* Título principal */
        .titulo-principal {
            background-color: #e74c3c;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border: 2px solid #c0392b;
        }
        
        .subtitulo {
            background-color: #34495e;
            color: white;
            font-size: 14px;
            text-align: center;
            padding: 10px;
            border: 1px solid #2c3e50;
        }
        
        /* Encabezados de sección */
        .seccion-header {
            background-color: #3498db;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border: 2px solid #2980b9;
            margin-top: 20px;
        }
        
        /* Tablas */
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }
        
        /* Encabezados de tabla */
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 12px;
            border: 1px solid #34495e;
            text-align: center;
        }
        
        /* Celdas normales */
        td {
            padding: 10px;
            border: 1px solid #bdc3c7;
            text-align: left;
        }
        
        /* Filas alternadas */
        tr:nth-child(even) {
            background-color: #ecf0f1;
        }
        
        /* Totales */
        .total-row {
            background-color: #27ae60 !important;
            color: white;
            font-weight: bold;
        }
        
        .total-row td {
            border: 2px solid #229954;
        }
        
        /* Números */
        .numero {
            text-align: right;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .dinero {
            text-align: right;
            font-weight: bold;
            color: #27ae60;
        }
        
        .porcentaje {
            text-align: center;
            font-weight: bold;
            color: #e67e22;
        }
        
        /* Cards de resumen */
        .card-resumen {
            background-color: #3498db;
            color: white;
            padding: 15px;
            text-align: center;
            border: 2px solid #2980b9;
            margin: 5px;
        }
        
        .card-valor {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        /* Categorías con colores */
        .cat-ninos { background-color: #3498db; color: white; }
        .cat-adultos { background-color: #2ecc71; color: white; }
        .cat-mayores { background-color: #e67e22; color: white; }
        
        /* Footer */
        .footer {
            background-color: #95a5a6;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            margin-top: 30px;
            border: 1px solid #7f8c8d;
        }
        
        /* Destacados */
        .destacado {
            background-color: #f39c12;
            color: white;
            font-weight: bold;
        }
        
        .separador {
            height: 20px;
            background-color: transparent;
        }
    </style>
</head>
<body>';

// Título principal
$html .= '
<table width="100%">
    <tr>
        <td class="titulo-principal" colspan="4">
            🎪 REPORTE DE VENTAS - FERIA PLAZA MUNDO 🎪
        </td>
    </tr>
    <tr>
        <td class="subtitulo" colspan="4">
            Fecha de Generación: ' . date('d/m/Y H:i:s') . ' | Usuario: ' . $_SESSION['usuario'] . '
        </td>
    </tr>
</table>
<div class="separador"></div>';

// OBTENER DATOS
$sql = "SELECT COUNT(*) as total_ventas FROM ventas WHERE fechaCompra = '$fecha_actual'";
$result = mysqli_query($conexion, $sql);
$row = mysqli_fetch_assoc($result);
$total_ventas = $row['total_ventas'];

$sql_revenue = "SELECT SUM(precio) as total_dinero FROM ventas WHERE fechaCompra = '$fecha_actual'";
$result_revenue = mysqli_query($conexion, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($result_revenue);
$total_dinero = $row_revenue['total_dinero'] ?? 0;

// SECCIÓN 1: RESUMEN DEL DÍA (Cards)
$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="3">
            📊 RESUMEN DEL DÍA - ' . date('d/m/Y', strtotime($fecha_actual)) . '
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td class="card-resumen" style="background-color: #3498db;">
            <div>🎟️ TICKETS VENDIDOS</div>
            <div class="card-valor">' . number_format($total_ventas) . '</div>
            <div>Total del día</div>
        </td>
        <td class="card-resumen" style="background-color: #27ae60;">
            <div>💰 INGRESOS</div>
            <div class="card-valor">$' . number_format($total_dinero, 2) . '</div>
            <div>Total recaudado</div>
        </td>
        <td class="card-resumen" style="background-color: #e67e22;">
            <div>📈 PROMEDIO</div>
            <div class="card-valor">$' . ($total_ventas > 0 ? number_format($total_dinero / $total_ventas, 2) : '0.00') . '</div>
            <div>Por ticket</div>
        </td>
    </tr>
</table>
<div class="separador"></div>';

// SECCIÓN 2: Ventas por Edad
$sql_children = "SELECT COUNT(*) as total_ninos FROM ventas WHERE id_edad = 1 AND fechaCompra = '$fecha_actual'";
$result_children = mysqli_query($conexion, $sql_children);
$row_children = mysqli_fetch_assoc($result_children);
$total_ninos = $row_children['total_ninos'];

$sql_adults = "SELECT COUNT(*) as total_adultos FROM ventas WHERE id_edad = 2 AND fechaCompra = '$fecha_actual'";
$result_adults = mysqli_query($conexion, $sql_adults);
$row_adults = mysqli_fetch_assoc($result_adults);
$total_adultos = $row_adults['total_adultos'];

$sql_seniors = "SELECT COUNT(*) as total_adultos_mayores FROM ventas WHERE id_edad = 3 AND fechaCompra = '$fecha_actual'";
$result_seniors = mysqli_query($conexion, $sql_seniors);
$row_seniors = mysqli_fetch_assoc($result_seniors);
$total_adultos_mayores = $row_seniors['total_adultos_mayores'];

$total_general = $total_ninos + $total_adultos + $total_adultos_mayores;

$porcentaje_ninos = $total_general > 0 ? round(($total_ninos * 100) / $total_general, 2) : 0;
$porcentaje_adultos = $total_general > 0 ? round(($total_adultos * 100) / $total_general, 2) : 0;
$porcentaje_mayores = $total_general > 0 ? round(($total_adultos_mayores * 100) / $total_general, 2) : 0;

$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="4">
            👥 ANÁLISIS DE VENTAS POR CATEGORÍA DE EDAD
        </td>
    </tr>
    <tr>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Porcentaje</th>
        <th>Gráfico</th>
    </tr>
    <tr class="cat-ninos">
        <td style="background-color: #3498db; color: white; font-weight: bold;">👶 Niños</td>
        <td class="numero" style="color: white;">' . number_format($total_ninos) . '</td>
        <td class="porcentaje" style="color: white;">' . $porcentaje_ninos . '%</td>
        <td style="color: white;">' . str_repeat('█', (int)($porcentaje_ninos / 2)) . '</td>
    </tr>
    <tr class="cat-adultos">
        <td style="background-color: #2ecc71; color: white; font-weight: bold;">👤 Adultos</td>
        <td class="numero" style="color: white;">' . number_format($total_adultos) . '</td>
        <td class="porcentaje" style="color: white;">' . $porcentaje_adultos . '%</td>
        <td style="color: white;">' . str_repeat('█', (int)($porcentaje_adultos / 2)) . '</td>
    </tr>
    <tr class="cat-mayores">
        <td style="background-color: #e67e22; color: white; font-weight: bold;">👴 Adultos Mayores</td>
        <td class="numero" style="color: white;">' . number_format($total_adultos_mayores) . '</td>
        <td class="porcentaje" style="color: white;">' . $porcentaje_mayores . '%</td>
        <td style="color: white;">' . str_repeat('█', (int)($porcentaje_mayores / 2)) . '</td>
    </tr>
    <tr class="total-row">
        <td><strong>TOTAL</strong></td>
        <td class="numero"><strong>' . number_format($total_general) . '</strong></td>
        <td class="porcentaje"><strong>100%</strong></td>
        <td></td>
    </tr>
</table>
<div class="separador"></div>';

// SECCIÓN 3: Resumen por Período
$sql_semana = "SELECT COUNT(*) as total_semana, SUM(precio) as dinero_semana 
               FROM ventas 
               WHERE fechaCompra >= DATE_SUB('$fecha_actual', INTERVAL 7 DAY)";
$result_semana = mysqli_query($conexion, $sql_semana);
$row_semana = mysqli_fetch_assoc($result_semana);
$total_semana = $row_semana['total_semana'];
$dinero_semana = $row_semana['dinero_semana'] ?? 0;

$sql_mes = "SELECT COUNT(*) as total_mes, SUM(precio) as dinero_mes 
            FROM ventas 
            WHERE MONTH(fechaCompra) = MONTH('$fecha_actual') 
            AND YEAR(fechaCompra) = YEAR('$fecha_actual')";
$result_mes = mysqli_query($conexion, $sql_mes);
$row_mes = mysqli_fetch_assoc($result_mes);
$total_mes = $row_mes['total_mes'];
$dinero_mes = $row_mes['dinero_mes'] ?? 0;

$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="3">
            📅 RESUMEN DE VENTAS POR PERÍODO
        </td>
    </tr>
    <tr>
        <th>Período</th>
        <th>Total Tickets</th>
        <th>Total Dinero</th>
    </tr>
    <tr style="background-color: #e8f5e9;">
        <td><strong>📆 HOY</strong></td>
        <td class="numero">' . number_format($total_ventas) . '</td>
        <td class="dinero">$' . number_format($total_dinero, 2) . '</td>
    </tr>
    <tr style="background-color: #fff3e0;">
        <td><strong>📊 ESTA SEMANA (7 días)</strong></td>
        <td class="numero">' . number_format($total_semana) . '</td>
        <td class="dinero">$' . number_format($dinero_semana, 2) . '</td>
    </tr>
    <tr style="background-color: #e3f2fd;">
        <td><strong>📈 ESTE MES</strong></td>
        <td class="numero">' . number_format($total_mes) . '</td>
        <td class="dinero">$' . number_format($dinero_mes, 2) . '</td>
    </tr>
</table>
<div class="separador"></div>';

// SECCIÓN 4: Top 5 Tickets Más Vendidos
$sql_top_tickets = "SELECT t.nombre, COUNT(*) as total_vendidos, SUM(v.precio) as total_ingresos
                    FROM ventas v
                    JOIN tickets t ON v.id_ticket = t.id_ticket
                    GROUP BY t.id_ticket, t.nombre
                    ORDER BY total_vendidos DESC
                    LIMIT 5";
$result_top_tickets = mysqli_query($conexion, $sql_top_tickets);

$colores_medallas = ['#FFD700', '#C0C0C0', '#CD7F32', '#4CAF50', '#2196F3'];
$medallas = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];

$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="5">
            🏆 TOP 5 TICKETS MÁS VENDIDOS
        </td>
    </tr>
    <tr>
        <th style="width: 10%;">Posición</th>
        <th style="width: 40%;">Nombre del Ticket</th>
        <th style="width: 20%;">Cantidad Vendida</th>
        <th style="width: 20%;">Ingresos Generados</th>
        <th style="width: 10%;">% del Total</th>
    </tr>';

$posicion = 0;
$total_top_tickets = 0;
while($row = mysqli_fetch_assoc($result_top_tickets)) {
    $porcentaje_ticket = $total_ventas > 0 ? round(($row['total_vendidos'] * 100) / $total_ventas, 2) : 0;
    $total_top_tickets += $row['total_vendidos'];
    $bg_color = $colores_medallas[$posicion];
    $text_color = $posicion < 3 ? 'white' : 'white';
    
    $html .= '
    <tr style="background-color: ' . $bg_color . '; color: ' . $text_color . ';">
        <td style="text-align: center; font-size: 20px; font-weight: bold;">' . $medallas[$posicion] . '</td>
        <td style="font-weight: bold;">' . htmlspecialchars($row['nombre']) . '</td>
        <td class="numero">' . number_format($row['total_vendidos']) . '</td>
        <td class="dinero">$' . number_format($row['total_ingresos'], 2) . '</td>
        <td class="porcentaje">' . $porcentaje_ticket . '%</td>
    </tr>';
    $posicion++;
}

$html .= '
</table>
<div class="separador"></div>';

// SECCIÓN 5: Capacidad del Lugar
$capacidad_total = 10000;
$tickets_disponibles = $capacidad_total - $total_ventas;
$porcentaje_ocupacion = round(($total_ventas * 100) / $capacidad_total, 2);

$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="3">
            🎪 CAPACIDAD Y DISPONIBILIDAD DEL LUGAR
        </td>
    </tr>
    <tr>
        <th style="width: 40%;">Concepto</th>
        <th style="width: 30%;">Valor</th>
        <th style="width: 30%;">Indicador</th>
    </tr>
    <tr style="background-color: #e3f2fd;">
        <td><strong>🏟️ Capacidad Total</strong></td>
        <td class="numero" style="font-size: 16px;">' . number_format($capacidad_total) . '</td>
        <td style="text-align: center; font-size: 20px;">🏟️</td>
    </tr>
    <tr style="background-color: #e8f5e9;">
        <td><strong>✅ Tickets Vendidos Hoy</strong></td>
        <td class="numero" style="font-size: 16px; color: #27ae60;">' . number_format($total_ventas) . '</td>
        <td style="text-align: center; font-size: 20px;">📊</td>
    </tr>
    <tr style="background-color: #fff3e0;">
        <td><strong>🎟️ Tickets Disponibles</strong></td>
        <td class="numero" style="font-size: 16px; color: #e67e22;">' . number_format($tickets_disponibles) . '</td>
        <td style="text-align: center; font-size: 20px;">🎫</td>
    </tr>
    <tr class="destacado">
        <td><strong>📈 Porcentaje de Ocupación</strong></td>
        <td class="porcentaje" style="font-size: 18px;"><strong>' . $porcentaje_ocupacion . '%</strong></td>
        <td style="text-align: center; font-size: 12px;">' . str_repeat('█', (int)($porcentaje_ocupacion / 2)) . '</td>
    </tr>
</table>
<div class="separador"></div>';

// SECCIÓN 6: Detalle de Ventas del Día
$sql_detalle = "SELECT 
                v.id_venta,
                v.folio,
                t.nombre as ticket,
                e.edad as categoria_edad,
                v.precio,
                v.fechaCompra,
                u.nombre as vendedor
                FROM ventas v
                JOIN tickets t ON v.id_ticket = t.id_ticket
                JOIN edades e ON v.id_edad = e.id_edad
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.fechaCompra = '$fecha_actual'
                ORDER BY v.id_venta DESC
                LIMIT 100";

$result_detalle = mysqli_query($conexion, $sql_detalle);

$html .= '
<table width="100%">
    <tr>
        <td class="seccion-header" colspan="7">
            📋 DETALLE DE VENTAS DEL DÍA (Últimas 100 transacciones)
        </td>
    </tr>';

if($result_detalle && mysqli_num_rows($result_detalle) > 0) {
    $html .= '
    <tr>
        <th style="width: 8%;">ID</th>
        <th style="width: 12%;">Folio</th>
        <th style="width: 30%;">Ticket</th>
        <th style="width: 15%;">Categoría</th>
        <th style="width: 12%;">Precio</th>
        <th style="width: 13%;">Fecha</th>
        <th style="width: 10%;">Vendedor</th>
    </tr>';
    
    $contador = 0;
    $suma_detalle = 0;
    while($row = mysqli_fetch_assoc($result_detalle)) {
        $bg_color = ($contador % 2 == 0) ? '#f8f9fa' : '#ffffff';
        $suma_detalle += $row['precio'];
        
        $html .= '
        <tr style="background-color: ' . $bg_color . ';">
            <td style="text-align: center;">' . $row['id_venta'] . '</td>
            <td style="text-align: center; font-weight: bold;">' . htmlspecialchars($row['folio']) . '</td>
            <td>' . htmlspecialchars($row['ticket']) . '</td>
            <td style="text-align: center;">' . htmlspecialchars($row['categoria_edad']) . '</td>
            <td class="dinero">$' . number_format($row['precio'], 2) . '</td>
            <td style="text-align: center;">' . $row['fechaCompra'] . '</td>
            <td style="text-align: center;">' . htmlspecialchars($row['vendedor'] ?? 'N/A') . '</td>
        </tr>';
        $contador++;
    }
    
    $html .= '
    <tr class="total-row">
        <td colspan="4"><strong>SUBTOTAL MOSTRADO (' . $contador . ' ventas)</strong></td>
        <td class="dinero"><strong>$' . number_format($suma_detalle, 2) . '</strong></td>
        <td colspan="2"></td>
    </tr>';
} else {
    $html .= '
    <tr>
        <td colspan="7" style="text-align: center; padding: 20px; background-color: #fff3cd; color: #856404;">
            ⚠️ No hay ventas registradas para este día
        </td>
    </tr>';
}

$html .= '
</table>
<div class="separador"></div>';

// FOOTER
$html .= '
<table width="100%">
    <tr>
        <td class="footer">
            <strong>🎪 FERIA PLAZA MUNDO 🎪</strong><br>
            Sistema de Gestión de Tickets<br>
            Reporte generado el ' . date('d/m/Y H:i:s') . '<br>
            Usuario: ' . $_SESSION['usuario'] . '<br>
            <em>Este documento es generado automáticamente por el sistema</em>
        </td>
    </tr>
</table>';

$html .= '
</body>
</html>';

echo $html;
exit;
?>
