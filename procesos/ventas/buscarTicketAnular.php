<?php
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "Error: Acceso denegado";
    exit;
}

$obj = new ventas();

$criterio = $_POST['criterio'];
$valor = $_POST['valor'];

$tickets = $obj->buscarTicketParaAnular($criterio, $valor);

if(count($tickets) > 0) {
    echo '<div class="table-responsive">
            <table class="table table-hover table-condensed table-bordered" style="text-align: center;">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Número Ticket</th>
                        <th>Ticket</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Precio</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach($tickets as $ticket) {
        echo '<tr>
                <td>' . $ticket['id_venta'] . '</td>
                <td>' . ($ticket['numero_ticket'] ?: 'N/A') . '</td>
                <td>' . ($ticket['ticket_nombre'] ?: 'N/A') . '</td>
                <td>' . ($ticket['documento_cliente'] ?: 'N/A') . '</td>
                <td>' . $ticket['fechaCompra'] . '</td>
                <td>' . ($ticket['horaCompra'] ?: 'N/A') . '</td>
                <td>$' . number_format($ticket['precio'], 2) . '</td>
                <td>' . $ticket['usuario_venta'] . '</td>
                <td>
                    <button class="btn-anular" onclick="abrirModalAnulacion(\'' . $ticket['id_venta'] . '\', \'' . $ticket['numero_ticket'] . '\', \'' . $ticket['ticket_nombre'] . '\')">
                        <i class="fas fa-ban"></i> Anular
                    </button>
                </td>
              </tr>';
    }
    
    echo '</tbody>
          </table>
        </div>';
} else {
    // No mostrar nada si no hay resultados - se manejará con JavaScript
}
?>
