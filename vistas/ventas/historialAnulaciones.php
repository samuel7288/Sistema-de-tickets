<?php 
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

// Verificar que el usuario esté logueado y sea administrador
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo '<div class="alert alert-danger">Acceso denegado. Solo administradores pueden ver el historial de anulaciones.</div>';
    exit;
}

$obj = new ventas();
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$anulaciones = $obj->obtenerHistorialAnulaciones($filtro);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Anulaciones</title>
    <link rel="stylesheet" type="text/css" href="../../css/styles.css">
    <link rel="stylesheet" type="text/css" href="../../librerias/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .status-anulado {
            color: #dc3545;
            font-weight: bold;
        }
        .info-anulacion {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 10px;
            margin: 5px 0;
        }
        .search-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        /* Estilos para mostrar motivo sin modal */
        .motivo-detalle {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        
        .motivo-detalle.show {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .motivo-header {
            background: #1976d2;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 15px -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .motivo-texto {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            line-height: 1.6;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .btn-cerrar-motivo {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        
        .btn-cerrar-motivo:hover {
            color: #ffeb3b;
        }
        
        .motivo-meta {
            color: #666;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">    <div class="page-header">
        <h2><i class="fas fa-history"></i> Historial de Tickets Anulados</h2>
        <p class="text-muted">Consulta y seguimiento de tickets anulados en el sistema</p>
    </div>
    
    <!-- Filtro de búsqueda -->
    <div class="search-container">
        <h4><i class="fas fa-search"></i> Filtrar Historial</h4>
        <form method="GET" class="form-inline">
            <div class="form-group mr-3">
                <label for="filtro" class="mr-2">Filtrar por:</label>
                <input type="text" class="form-control" name="filtro" id="filtro" 
                       value="<?php echo htmlspecialchars($filtro); ?>" 
                       placeholder="Número de ticket o documento del cliente...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
            <a href="?" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> Limpiar
            </a>
        </form>
    </div>
    
    <!-- Panel para mostrar motivo de anulación -->
    <div id="motivoDetalle" class="motivo-detalle">
        <div class="motivo-header">
            <h5><i class="fas fa-comment-alt"></i> Motivo de Anulación</h5>
            <button type="button" class="btn-cerrar-motivo" onclick="ocultarMotivo()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="motivo-texto" id="textoMotivo">
            <!-- El motivo se mostrará aquí -->
        </div>
        <div class="motivo-meta" id="metaMotivo">
            <!-- Información adicional del ticket -->
        </div>
    </div>

    <!-- Tabla de anulaciones -->
    <div class="row">
        <div class="col-sm-12">
            <?php if(count($anulaciones) > 0): ?>                <div class="table-responsive">
                    <table class="table table-hover table-condensed table-bordered" style="text-align: center;">
                        <thead class="thead-dark">
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-ticket-alt"></i> Número</th>
                                <th><i class="fas fa-tag"></i> Ticket</th>
                                <th><i class="fas fa-user"></i> Cliente</th>
                                <th><i class="fas fa-calendar"></i> Compra</th>
                                <th><i class="fas fa-dollar-sign"></i> Precio</th>
                                <th><i class="fas fa-user-tie"></i> Vendedor</th>
                                <th><i class="fas fa-ban"></i> Anulación</th>
                                <th><i class="fas fa-user-shield"></i> Anulado por</th>
                                <th><i class="fas fa-comment"></i> Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($anulaciones as $anulacion): ?>
                            <tr>
                                <td><?php echo $anulacion['id_venta']; ?></td>
                                <td><?php echo $anulacion['numero_ticket'] ?: 'N/A'; ?></td>
                                <td><?php echo $anulacion['ticket_nombre'] ?: 'N/A'; ?></td>
                                <td><?php echo $anulacion['documento_cliente'] ?: 'N/A'; ?></td>
                                <td>
                                    <?php echo $anulacion['fechaCompra']; ?>
                                    <?php if($anulacion['horaCompra']): ?>
                                        <br><small><?php echo $anulacion['horaCompra']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo number_format($anulacion['precio'], 2); ?></td>
                                <td><?php echo $anulacion['usuario_venta']; ?></td>
                                <td>
                                    <?php 
                                    $fechaAnulacion = new DateTime($anulacion['fecha_anulacion']);
                                    echo $fechaAnulacion->format('d/m/Y H:i:s');
                                    ?>
                                </td>
                                <td><?php echo $anulacion['usuario_anulacion']; ?></td>                                <td>
                                    <button class="btn btn-sm btn-info" onclick="mostrarMotivo('<?php echo htmlspecialchars($anulacion['motivo_anulacion']); ?>', '<?php echo $anulacion['numero_ticket']; ?>', '<?php echo $anulacion['id_venta']; ?>', '<?php echo $anulacion['usuario_anulacion']; ?>', '<?php echo $fechaAnulacion->format('d/m/Y H:i:s'); ?>')">
                                        <i class="fas fa-eye"></i> Ver Motivo
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Total de tickets anulados:</strong> <?php echo count($anulaciones); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> 
                    <?php if($filtro): ?>
                        No se encontraron tickets anulados con el filtro "<?php echo htmlspecialchars($filtro); ?>".
                    <?php else: ?>
                        No hay tickets anulados registrados en el sistema.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../../librerias/jquery-3.2.1.min.js"></script>
<script src="../../librerias/bootstrap/js/bootstrap.js"></script>

<script>
function mostrarMotivo(motivo, numeroTicket, idVenta, usuarioAnulacion, fechaAnulacion) {
    // Limpiar motivo anterior
    $('#motivoDetalle').removeClass('show');
    
    // Establecer el motivo
    $('#textoMotivo').text(motivo || 'Sin motivo especificado');
    
    // Establecer metainformación
    var meta = '<strong>Ticket:</strong> ' + (numeroTicket || 'N/A') + ' | ' +
               '<strong>ID Venta:</strong> ' + idVenta + ' | ' +
               '<strong>Anulado por:</strong> ' + usuarioAnulacion + ' | ' +
               '<strong>Fecha:</strong> ' + fechaAnulacion;
    $('#metaMotivo').html(meta);
    
    // Mostrar el panel
    $('#motivoDetalle').addClass('show');
    
    // Desplazar hacia el panel
    $('html, body').animate({
        scrollTop: $("#motivoDetalle").offset().top - 100
    }, 500);
}

function ocultarMotivo() {
    $('#motivoDetalle').removeClass('show');
    
    // Desplazar hacia arriba
    $('html, body').animate({
        scrollTop: 0
    }, 500);
}

// Cerrar panel con ESC
$(document).keyup(function(e) {
    if (e.keyCode == 27) { // ESC
        ocultarMotivo();
    }
});
</script>

</body>
</html>
