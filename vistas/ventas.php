<?php 
session_start();
if(isset($_SESSION['usuario'])){
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ventas</title>
    <?php require_once "dependencias.php"; ?>
</head>
<body>
    <?php require_once "menu.php"; ?>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  
    <div class="container main-content sales-dashboard animate__animated animate__fadeIn">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="title-section">
                    <i class="fas fa-ticket-alt dashboard-icon"></i>
                    <h1>Venta de Tickets</h1>
                </div>
                <p class="current-date" id="currentDate"></p>
            </div>
        </div>        <div class="action-buttons">
            <button class="btn-action" id="ventaTicketsBtn">
                <i class="fas fa-cart-plus"></i>
                Vender Tickets
            </button>
            <button class="btn-action" id="ventasHechasBtn">
                <i class="fas fa-history"></i>
                Ventas Realizadas
            </button>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
            <button class="btn-action btn-danger" id="anularTicketsBtn">
                <i class="fas fa-ban"></i>
                Anular Tickets
            </button>
            <button class="btn-action btn-warning" id="historialAnulacionesBtn">
                <i class="fas fa-clipboard-list"></i>
                Historial Anulaciones
            </button>
            <?php endif; ?>
        </div>

        <div class="content-panels">
            <div id="ventaTickets" class="panel-section"></div>
            <div id="ventasHechas" class="panel-section"></div>
            <div id="anularTickets" class="panel-section"></div>
            <div id="historialAnulaciones" class="panel-section"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            // Update current date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#currentDate').text(new Date().toLocaleDateString('es-ES', options));

            // Add animation classes on button clicks
            $('#ventaTicketsBtn').click(function(){
                esconderSeccionVenta();
                $('#ventaTickets').load('ventas/ventasDeTickets.php');
                $('#ventaTickets').show().addClass('animate__animated animate__fadeIn');
            });            $('#ventasHechasBtn').click(function(){
                esconderSeccionVenta();
                $('#ventasHechas').load('ventas/ventasyReportes.php');
                $('#ventasHechas').show().addClass('animate__animated animate__fadeIn');
            });

            $('#anularTicketsBtn').click(function(){
                esconderSeccionVenta();
                $('#anularTickets').load('ventas/anularTickets.php');
                $('#anularTickets').show().addClass('animate__animated animate__fadeIn');
            });

            $('#historialAnulacionesBtn').click(function(){
                esconderSeccionVenta();
                $('#historialAnulaciones').load('ventas/historialAnulaciones.php');
                $('#historialAnulaciones').show().addClass('animate__animated animate__fadeIn');
            });
        });

        function esconderSeccionVenta(){
            $('#ventaTickets').hide();
            $('#ventasHechas').hide();
            $('#anularTickets').hide();
            $('#historialAnulaciones').hide();
        }

    </script>
</body>
</html>
<?php 
} else {
    header("location:../index.php");
}
?>