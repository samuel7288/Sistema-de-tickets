<?php 
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

// Verificar que el usuario esté logueado y sea administrador
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo '<div class="alert alert-danger">Acceso denegado. Solo administradores pueden anular tickets.</div>';
    exit;
}

$c = new conectar();
$conexion = $c->conexion();
$obj = new ventas();

// Obtener ventas activas para mostrar en la tabla
$sql = "SELECT v.*, 
        u.nombre as usuario_venta,
        t.nombre as ticket_nombre
        FROM ventas v
        LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
        LEFT JOIN tickets t ON v.id_ticket = t.id_ticket
        WHERE v.estado = 'ACTIVO'
        ORDER BY v.fechaCompra DESC, v.horaCompra DESC
        LIMIT 50";
$result = mysqli_query($conexion, $sql);

if (!$result) {
    echo '<div class="alert alert-danger">Error en la consulta: ' . mysqli_error($conexion) . '</div>';
    echo '<div class="alert alert-info">Posiblemente necesite ejecutar la actualización de la base de datos. Por favor, verifique que la tabla ventas tenga los campos: estado, numero_ticket, documento_cliente, horaCompra, etc.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anular Tickets</title>
    <link rel="stylesheet" type="text/css" href="../../css/styles.css">
    <link rel="stylesheet" type="text/css" href="../../librerias/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../../librerias/alertifyjs/css/alertify.css">
    <link rel="stylesheet" type="text/css" href="../../librerias/alertifyjs/css/themes/default.css">
    <style>
        .search-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .btn-anular {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-anular:hover {
            background: #c82333;
        }
        .status-activo {
            color: #28a745;
            font-weight: bold;
        }
        .status-anulado {
            color: #dc3545;
            font-weight: bold;
        }
        .search-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .search-form .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }
        .search-form .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            height: 20px; /* Altura fija para las etiquetas */
            line-height: 20px;
        }
        .search-form .form-group .form-control {
            height: 38px;
        }
        .search-form .form-group .form-text {
            height: 16px; /* Altura fija para el texto de ayuda */
            margin-top: 2px;
            margin-bottom: 0;
        }
        .search-form .form-group:last-child {
            flex: 0 0 auto;
            min-width: auto;
        }
        .search-form .form-group:last-child .form-control {
            width: 120px;
        }
        /* Asegurar que todos los campos tengan la misma estructura de altura */
        .search-form .form-group:last-child label {
            height: 20px;
            line-height: 20px;
        }
        .search-form .form-group:last-child::after {
            content: "";
            height: 16px;
            display: block;
            margin-top: 2px;
        }
        
        /* Estilos para el formulario de anulación */
        .anulacion-form {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
        }
        
        .anulacion-form.show {
            display: block;
            animation: slideDown 0.3s ease-out;
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
        
        .ticket-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #2196f3;
        }
        
        .warning-alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        
        .char-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }
        
        .char-counter.warning {
            color: #ffc107;
        }
        
        .char-counter.danger {
            color: #dc3545;
        }
        
        .motivo-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-group-anular {
            text-align: right;
            margin-top: 15px;
        }
        
        .btn-group-anular .btn {
            margin-left: 10px;
        }
        
        /* Estilos para validación en tiempo real */
        .validation-status {
            margin-left: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .validation-status.valid {
            color: #28a745;
        }
        
        .validation-status.invalid {
            color: #dc3545;
        }
        
        .validation-status.warning {
            color: #ffc107;
        }
        
        .validation-message {
            font-size: 12px;
            margin-top: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            display: none;
        }
        
        .validation-message.show {
            display: block;
        }
        
        .validation-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f1aeb5;
        }
        
        .validation-message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .validation-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .motivo-textarea.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .motivo-textarea.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">            <div class="page-header">
                <h2><i class="fas fa-ban"></i> Anulación de Tickets</h2>
                <p class="text-muted">Sistema de anulación de tickets para administradores</p>
            </div>
        </div>
    </div>
    
    <!-- Formulario de búsqueda -->
    <div class="row">
        <div class="col-md-12">
            <div class="search-container">
                <h4><i class="fas fa-search"></i> Buscar Ticket para Anular</h4>
                <form id="formBuscarTicket" class="search-form">
                    <div class="form-group">
                        <label for="criterio">Criterio de búsqueda:</label>
                        <select class="form-control" name="criterio" id="criterio">
                            <option value="numero_ticket">Número de Ticket</option>
                            <option value="documento">Documento del Cliente</option>
                            <option value="fecha_hora">Fecha/Hora de Compra</option>
                        </select>
                        <small class="form-text text-muted">&nbsp;</small>
                    </div>
                    <div class="form-group">
                        <label for="valor">Valor a buscar:</label>
                        <input type="text" class="form-control" name="valor" id="valor" placeholder="Ingrese el valor..." required>
                        <small class="form-text text-muted" id="helpText">Ingrese el número de ticket</small>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resultados de búsqueda -->
    <div class="row" id="resultadosBusqueda" style="display: none;">
        <div class="col-md-12">
            <h4><i class="fas fa-list"></i> Tickets Encontrados</h4>
            <div id="tablaResultados"></div>
        </div>
    </div>
    
    <!-- Formulario de anulación -->
    <div class="row">
        <div class="col-md-12">
            <div id="formularioAnulacion" class="anulacion-form">
                <h4><i class="fas fa-exclamation-triangle text-warning"></i> Confirmar Anulación de Ticket</h4>
                
                <div class="warning-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer. El ticket será marcado como anulado permanentemente.
                </div>
                
                <form id="formAnulacion">
                    <input type="hidden" id="idVentaAnular" name="idVenta">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label><strong>Información del Ticket a Anular:</strong></label>
                            <div class="ticket-info" id="infoTicketAnular">
                                <!-- La información se llenará dinámicamente -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Usuario que realiza la anulación:</strong></label>
                            <div class="user-info">
                                <i class="fas fa-user"></i>
                                <?php 
                                echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario no identificado'; 
                                echo ' <span class="badge badge-primary">';
                                echo isset($_SESSION['rol']) ? ucfirst($_SESSION['rol']) : 'Rol no definido';
                                echo '</span>';
                                ?>
                                <br><small class="text-muted">
                                    <i class="fas fa-clock"></i> Fecha y hora: <span id="fechaAnulacion"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivoAnulacion"><strong>Motivo de anulación <span class="text-danger">*</span>:</strong></label>
                        <textarea class="form-control motivo-textarea" 
                                  id="motivoAnulacion" 
                                  name="motivo" 
                                  rows="4" 
                                  required 
                                  maxlength="500"
                                  placeholder="Describa detalladamente el motivo de la anulación del ticket. Este campo es obligatorio y debe tener al menos 10 caracteres."></textarea>
                        <div class="char-counter">
                            <span id="contadorCaracteres">0</span>/500 caracteres
                            <span id="estadoValidacion" class="validation-status"></span>
                        </div>
                        <div id="mensajeValidacion" class="validation-message"></div>
                    </div>
                    
                    <div class="btn-group-anular">
                        <button type="button" class="btn btn-secondary" id="cancelarAnulacion">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmarAnulacion">
                            <i class="fas fa-ban"></i> Confirmar Anulación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de ventas recientes -->
    <div class="row">
        <div class="col-md-12">
            <h4><i class="fas fa-clock"></i> Ventas Recientes (Últimas 50)</h4>
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-bordered" style="text-align: center;">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Venta</th>
                            <th>Número Ticket</th>
                            <th>Ticket</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Precio</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($ver = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $ver['id_venta']; ?></td>
                            <td><?php echo $ver['numero_ticket'] ?: 'N/A'; ?></td>
                            <td><?php echo $ver['ticket_nombre'] ?: 'N/A'; ?></td>
                            <td><?php echo $ver['documento_cliente'] ?: 'N/A'; ?></td>
                            <td><?php echo $ver['fechaCompra']; ?></td>
                            <td><?php echo $ver['horaCompra'] ?: 'N/A'; ?></td>
                            <td>$<?php echo number_format($ver['precio'], 2); ?></td>
                            <td><?php echo $ver['usuario_venta']; ?></td>
                            <td>
                                <span class="status-<?php echo strtolower($ver['estado']); ?>">
                                    <?php echo $ver['estado']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($ver['estado'] == 'ACTIVO'): ?>
                                    <button class="btn-anular" onclick="mostrarFormularioAnulacion('<?php echo $ver['id_venta']; ?>', '<?php echo $ver['numero_ticket']; ?>', '<?php echo addslashes($ver['ticket_nombre']); ?>', '<?php echo $ver['documento_cliente']; ?>', '<?php echo $ver['fechaCompra']; ?>', '<?php echo $ver['horaCompra']; ?>', '<?php echo $ver['precio']; ?>')">
                                        <i class="fas fa-ban"></i> Anular
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Anulado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../../librerias/jquery-3.2.1.min.js"></script>
<script src="../../librerias/bootstrap/js/bootstrap.js"></script>
<script src="../../librerias/alertifyjs/alertify.js"></script>

<script>
$(document).ready(function(){
    // Inicializar fecha y hora actual
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Contador de caracteres y validación en tiempo real para el textarea
    $('#motivoAnulacion').on('input', function() {
        var count = $(this).val().length;
        var counter = $('#contadorCaracteres');
        var statusElement = $('#estadoValidacion');
        var messageElement = $('#mensajeValidacion');
        var textarea = $(this);
        
        counter.text(count);
        
        // Remover clases previas
        counter.removeClass('warning danger');
        statusElement.removeClass('valid invalid warning');
        messageElement.removeClass('show error warning success');
        textarea.removeClass('is-invalid is-valid');
        
        if (count === 0) {
            statusElement.addClass('invalid').text('⚠️ Campo requerido');
            messageElement.addClass('show error').text('Debe escribir un motivo para la anulación del ticket.');
            textarea.addClass('is-invalid');
        } else if (count < 10) {
            var faltantes = 10 - count;
            statusElement.addClass('invalid').text('❌ Faltan ' + faltantes + ' caracteres');
            messageElement.addClass('show warning').text('Necesita escribir al menos ' + faltantes + ' caracteres más. Mínimo requerido: 10 caracteres.');
            textarea.addClass('is-invalid');
            if (count > 5) {
                counter.addClass('warning');
            }
        } else if (count >= 10 && count <= 500) {
            statusElement.addClass('valid').text('✅ Válido');
            messageElement.addClass('show success').text('El motivo es válido y tiene la longitud adecuada.');
            textarea.addClass('is-valid');
        } else if (count > 480) {
            counter.addClass('danger');
            statusElement.addClass('warning').text('⚠️ Cerca del límite');
            messageElement.addClass('show warning').text('Se está acercando al límite máximo de 500 caracteres.');
            textarea.addClass('is-valid');
        } else if (count > 400) {
            counter.addClass('warning');
            statusElement.addClass('valid').text('✅ Válido');
            textarea.addClass('is-valid');
        }
        
        // Actualizar estado del botón de confirmar
        updateConfirmButtonState();
    });
    
    // Función para actualizar el estado del botón de confirmar
    function updateConfirmButtonState() {
        var motivo = $('#motivoAnulacion').val().trim();
        var idVenta = $('#idVentaAnular').val();
        var confirmButton = $('#confirmarAnulacion');
        
        if (idVenta && motivo.length >= 10) {
            confirmButton.prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            confirmButton.prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
        }
    }
    
    // Limpiar validación al enfocar
    $('#motivoAnulacion').on('focus', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Cambiar ayuda según criterio seleccionado
    $('#criterio').change(function(){
        var criterio = $(this).val();
        var helpText = '';
        var placeholder = '';
        
        switch(criterio) {
            case 'numero_ticket':
                helpText = 'Ingrese el número de ticket (ej: TICK-20241209-001)';
                placeholder = 'TICK-20241209-001';
                break;
            case 'documento':
                helpText = 'Ingrese el documento del cliente';
                placeholder = '12345678';
                break;
            case 'fecha_hora':
                helpText = 'Formato: YYYY-MM-DD HH:MM:SS o solo YYYY-MM-DD';
                placeholder = '2024-12-09 14:30:00';
                break;
        }
        
        $('#helpText').text(helpText);
        $('#valor').attr('placeholder', placeholder);
    });
    
    // Buscar tickets
    $('#formBuscarTicket').submit(function(e){
        e.preventDefault();
        
        var datos = $(this).serialize();
        
        $.ajax({
            type: "POST",
            data: datos,
            url: "../../procesos/ventas/buscarTicketAnular.php",
            success: function(r){
                if(r.trim() !== '') {
                    $('#tablaResultados').html(r);
                    $('#resultadosBusqueda').show();
                    $('html, body').animate({
                        scrollTop: $("#resultadosBusqueda").offset().top - 100
                    }, 1000);
                } else {
                    alertify.warning("No se encontraron tickets con los criterios especificados");
                    $('#resultadosBusqueda').hide();
                }
            },
            error: function(){
                alertify.error("Error al buscar tickets");
            }
        });
    });
    
    // Cancelar anulación
    $('#cancelarAnulacion').click(function(){
        ocultarFormularioAnulacion();
    });
    
    // Confirmar anulación
    $('#confirmarAnulacion').click(function(){
        // Solo permitir click si el botón está habilitado
        if ($(this).prop('disabled')) {
            return false;
        }
        
        console.log("DEBUG - Botón confirmar anulación clickeado");
        
        var motivo = $('#motivoAnulacion').val().trim();
        var idVenta = $('#idVentaAnular').val();
        
        console.log("DEBUG - Motivo:", motivo, "Longitud:", motivo.length);
        console.log("DEBUG - ID Venta:", idVenta);
        
        // Validaciones finales (por seguridad)
        if(!idVenta || motivo.length < 10) {
            alertify.error("Por favor, complete correctamente todos los campos requeridos.");
            return false;
        }
        
        console.log("DEBUG - Todas las validaciones pasaron, mostrando confirmación");
        
        // Confirmación adicional
        alertify.confirm('Confirmar Anulación', 
            '¿Está seguro de que desea anular este ticket? Esta acción no se puede deshacer.',
            function(){
                console.log("DEBUG - Usuario confirmó la anulación");
                
                // Deshabilitar botón para evitar doble envío
                $('#confirmarAnulacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                
                var datos = $('#formAnulacion').serialize();
                console.log("DEBUG - Datos a enviar:", datos);
                
                $.ajax({
                    type: "POST",
                    data: datos,
                    url: "../../procesos/ventas/anularTicket.php",
                    beforeSend: function() {
                        console.log("DEBUG - Enviando petición AJAX...");
                    },
                    success: function(r){
                        console.log("DEBUG - Respuesta recibida del servidor:");
                        console.log("Contenido:", r.trim());
                        
                        // Rehabilitar botón
                        $('#confirmarAnulacion').prop('disabled', false).html('<i class="fas fa-ban"></i> Confirmar Anulación');
                        
                        if(r.trim() == "1") {
                            console.log("DEBUG - Anulación exitosa");
                            alertify.success("¡Ticket anulado exitosamente!");
                            ocultarFormularioAnulacion();
                            setTimeout(function() {
                                location.reload(); // Recargar para actualizar la tabla
                            }, 2000);
                        } else {
                            console.log("DEBUG - Error en anulación:", r.trim());
                            alertify.error("Error al anular el ticket: " + r.trim());
                        }
                    },
                    error: function(xhr, status, error){
                        console.log("DEBUG - Error AJAX:", status, error);
                        console.log("DEBUG - Response Text:", xhr.responseText);
                        
                        // Rehabilitar botón
                        $('#confirmarAnulacion').prop('disabled', false).html('<i class="fas fa-ban"></i> Confirmar Anulación');
                        
                        alertify.error("Error en la comunicación con el servidor. Revise la consola para más detalles.");
                    }
                });
            },
            function(){
                console.log("DEBUG - Usuario canceló la confirmación");
                alertify.message('Anulación cancelada');
            }
        ).set('labels', {ok:'Sí, Anular Ticket', cancel:'Cancelar'});
    });
});

function updateDateTime() {
    var now = new Date();
    var formatted = now.toLocaleString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    $('#fechaAnulacion').text(formatted);
}

function mostrarFormularioAnulacion(idVenta, numeroTicket, nombreTicket, documento, fecha, hora, precio) {
    console.log("Mostrando formulario para:", idVenta, numeroTicket, nombreTicket);
    
    // Limpiar formulario y validaciones
    $('#motivoAnulacion').val('').removeClass('is-invalid is-valid');
    $('#contadorCaracteres').text('0').removeClass('warning danger');
    $('#estadoValidacion').removeClass('valid invalid warning').text('');
    $('#mensajeValidacion').removeClass('show error warning success').text('');
    
    // Establecer valores
    $('#idVentaAnular').val(idVenta);
    
    var infoHtml = '<p><i class="fas fa-ticket-alt"></i> <strong>ID Venta:</strong> ' + idVenta + '</p>' +
                   '<p><i class="fas fa-hashtag"></i> <strong>Número:</strong> ' + (numeroTicket || 'N/A') + '</p>' +
                   '<p><i class="fas fa-tag"></i> <strong>Ticket:</strong> ' + (nombreTicket || 'N/A') + '</p>' +
                   '<p><i class="fas fa-id-card"></i> <strong>Cliente:</strong> ' + (documento || 'N/A') + '</p>' +
                   '<p><i class="fas fa-calendar"></i> <strong>Fecha:</strong> ' + fecha + '</p>' +
                   '<p><i class="fas fa-clock"></i> <strong>Hora:</strong> ' + (hora || 'N/A') + '</p>' +
                   '<p><i class="fas fa-dollar-sign"></i> <strong>Precio:</strong> $' + parseFloat(precio).toFixed(2) + '</p>';
    
    $('#infoTicketAnular').html(infoHtml);
    
    // Mostrar formulario
    $('#formularioAnulacion').addClass('show');
    
    // Inicializar estado del botón (deshabilitado)
    $('#confirmarAnulacion').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
    
    // Mostrar mensaje inicial de validación
    $('#estadoValidacion').addClass('invalid').text('⚠️ Campo requerido');
    $('#mensajeValidacion').addClass('show error').text('Debe escribir un motivo para la anulación del ticket.');
    
    // Desplazar hacia el formulario
    $('html, body').animate({
        scrollTop: $("#formularioAnulacion").offset().top - 100
    }, 1000, function() {
        // Enfocar el textarea después de la animación
        $('#motivoAnulacion').focus();
    });
}

function ocultarFormularioAnulacion() {
    $('#formularioAnulacion').removeClass('show');
    
    // Desplazar hacia arriba
    $('html, body').animate({
        scrollTop: 0
    }, 500);
}
</script>

</body>
</html>
