<?php 
session_start();
if(isset($_SESSION['usuario'])){
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Ticket</title>
		<?php require_once "menu.php"; ?>
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
		<?php require_once "../clases/Conexion.php"; 
		$c= new conectar();
		$conexion=$c->conexion();
		$sql="SELECT id_categoria,nombreCategoria
		from categorias";
		$result=mysqli_query($conexion,$sql);
		?>
	</head>
	<body>
		<div class="container">
			<!-- Título Principal con Icono -->
			<div class="page-header">
				<div class="header-content">
					<i class="fas fa-ticket-alt page-icon"></i>
					<h1 class="page-title">Gestión de Tickets</h1>
					<p class="page-subtitle">Administra los tickets disponibles para la feria</p>
				</div>
			</div>

			<div class="row">
				<!-- FORMULARIO MODERNIZADO -->
				<div class="col-sm-4">
					<div class="form-card">
						<div class="form-header">
							<i class="fas fa-plus-circle form-icon"></i>
							<h3 class="form-title">Nuevo Ticket</h3>
						</div>
						<form id="frmTickets" class="modern-form" enctype="multipart/form-data">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-tags label-icon"></i>
									Categoría
								</label>
								<select class="form-control modern-input" id="categoriaSelect" name="categoriaSelect">
									<option value="A">Selecciona Categoría</option>
									<?php while($ver=mysqli_fetch_row($result)): ?>
										<option value="<?php echo $ver[0] ?>"><?php echo $ver[1]; ?></option>
									<?php endwhile; ?>
								</select>
								<div class="input-feedback" id="categoriaSelectFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-signature label-icon"></i>
									Nombre del Ticket
								</label>
								<input type="text" 
									   class="form-control modern-input" 
									   id="nombre" 
									   name="nombre"
									   placeholder="Ej: Montaña Rusa, Rueda de la Fortuna">
								<div class="input-feedback" id="nombreFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-align-left label-icon"></i>
									Descripción
								</label>
								<textarea class="form-control modern-input" 
									      id="descripcion" 
									      name="descripcion" 
									      rows="3"
									      placeholder="Describe la atracción o actividad"></textarea>
								<div class="input-feedback" id="descripcionFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-warehouse label-icon"></i>
									Cantidad Disponible
								</label>
								<input type="number" 
									   class="form-control modern-input" 
									   id="cantidad" 
									   name="cantidad"
									   min="1"
									   placeholder="Ej: 100">
								<small class="input-help">
									<i class="fas fa-info-circle"></i>
									Número de tickets disponibles para venta
								</small>
								<div class="input-feedback" id="cantidadFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-dollar-sign label-icon"></i>
									Precio
								</label>
								<input type="number" 
									   class="form-control modern-input" 
									   id="precio" 
									   name="precio"
									   step="0.01"
									   min="0"
									   placeholder="Ej: 15.50">
								<small class="input-help">
									<i class="fas fa-info-circle"></i>
									Precio en pesos mexicanos
								</small>
								<div class="input-feedback" id="precioFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-image label-icon"></i>
									Imagen del Ticket
								</label>
								<input type="file" 
									   class="form-control modern-input" 
									   id="imagen" 
									   name="imagen"
									   accept="image/*">
								<small class="input-help">
									<i class="fas fa-info-circle"></i>
									Formatos: JPG, PNG, GIF (máximo 2MB)
								</small>
								<div class="input-feedback" id="imagenFeedback"></div>
							</div>

							<div class="form-actions">
								<button type="button" id="btnAgregaTicket" class="btn btn-primary modern-btn">
									<i class="fas fa-plus btn-icon"></i>
									<span class="btn-text">Crear Ticket</span>
									<div class="btn-loading" style="display: none;">
										<i class="fas fa-spinner fa-spin"></i>
									</div>
								</button>
							</div>
						</form>
					</div>
				</div>

				<!-- TABLA MODERNIZADA -->
				<div class="col-sm-8">
					<div class="table-card">
						<div class="table-header">
							<i class="fas fa-list table-icon"></i>
							<h3 class="table-title">Tickets Registrados</h3>
						</div>
						<div class="table-responsive-custom">
							<div id="tablaTicketsLoad"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Button trigger modal -->
		
		<!-- Modal -->
		<div class="modal fade" id="abremodalUpdateTicket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualiza Ticket</h4>
					</div>
					<div class="modal-body">
						<form id="frmTicketsU" enctype="multipart/form-data">
							<input type="text" id="idTicket" hidden="" name="idTicket">
							<label>Categoria</label>
							<select class="form-control input-sm" id="categoriaSelectU" name="categoriaSelectU">
								<option value="A">Selecciona Categoria</option>
								<?php 
								$sql="SELECT id_categoria,nombreCategoria
								from categorias";
								$result=mysqli_query($conexion,$sql);
								?>
								<?php while($ver=mysqli_fetch_row($result)): ?>
									<option value="<?php echo $ver[0] ?>"><?php echo $ver[1]; ?></option>
								<?php endwhile; ?>
							</select>
							<label>Nombre</label>
							<input type="text" class="form-control input-sm" id="nombreU" name="nombreU">
							<label>Descripcion</label>
							<input type="text" class="form-control input-sm" id="descripcionU" name="descripcionU">
							<label>Cantidad</label>
							<input type="text" class="form-control input-sm" id="cantidadU" name="cantidadU">
							<label>Precio</label>
							<input type="text" class="form-control input-sm" id="precioU" name="precioU">
							
						</form>
					</div>
					<div class="modal-footer">
						<button id="btnActualizaTicket" type="button" class="btn btn-warning" data-dismiss="modal">Actualizar</button>

					</div>
				</div>
			</div>
		</div>

	</body>
	</html>

	<script type="text/javascript">
		function agregaDatosTicket(idticket){
			$.ajax({
				type:"POST",
				data:"idtic=" + idticket,
				url:"../procesos/tickets/obtenDatosTicket.php",
				success:function(r){
					
					dato=jQuery.parseJSON(r);
					$('#idTicket').val(dato['id_ticket']);
					$('#categoriaSelectU').val(dato['id_categoria']);
					$('#nombreU').val(dato['nombre']);
					$('#descripcionU').val(dato['descripcion']);
					$('#cantidadU').val(dato['cantidad']);
					$('#precioU').val(dato['precio']);

				},
				error: function() {
					alertify.error("Error al obtener datos del ticket.");
				}
			});
		}

		function eliminaTicket(idTicket){
			alertify.confirm('¿Desea eliminar este Ticket?', function(){ 
				$.ajax({
					type:"POST",
					data:"idticket=" + idTicket,
					url:"../procesos/tickets/eliminarTicket.php",
					success:function(r){
						if(r==1){
							$('#tablaTicketsLoad').load("tickets/tablaTickets.php");
							alertify.success("Eliminado con exito!!");
						}else{
							alertify.error("No se pudo eliminar :(");
						}
					},
					error: function() {
						alertify.error("Error al eliminar el ticket.");
					}
				});
			}, function(){ 
				alertify.error('Cancelo !')
			});
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#btnActualizaTicket').click(function(){

				datos=$('#frmTicketsU').serialize();
				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/tickets/actualizaTickets.php",
					success:function(r){
						if(r==1){
							$('#tablaTicketsLoad').load("tickets/tablaTickets.php");
							alertify.success("Actualizado con exito :D");
						}else{
							alertify.error("Error al actualizar :(");
						}
					},
					error: function() {
						alertify.error("Error al actualizar el ticket.");
					}
				});
			});
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#tablaTicketsLoad').load("tickets/tablaTickets.php");

			$('#btnAgregaTicket').click(function(e){
				e.preventDefault(); // Prevenir comportamiento por defecto

				// Validación específica de campos
				if($('#categoriaSelect').val() === 'A'){
					alertify.error("Selecciona una categoría válida");
					return false;
				}

				if(!$('#imagen').val()){
					alertify.error("Selecciona una imagen");
					return false;
				}

				// Validación general de campos vacíos
				vacios = validarFormVacio('frmTickets');
				if(vacios > 0){
					alertify.alert("Debes llenar todos los campos!");
					return false;
				}

				// Crear FormData y agregar datos
				var formData = new FormData(document.getElementById("frmTickets"));

				// Mostrar indicador de carga
				alertify.message('Procesando...');

				$.ajax({
					url: "../procesos/tickets/insertaTickets.php",
					type: "POST",
					dataType: "json", // Cambiar a json
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(response){
						if(response == 1){
							$('#frmTickets')[0].reset();
							$('#tablaTicketsLoad').load("tickets/tablaTickets.php");
							alertify.success("Ticket agregado correctamente");
						} else {
							alertify.error("Error al agregar el ticket: " + response.message);
						}
					},
					error: function(xhr, status, error) {
						alertify.error("Error en la solicitud: " + error);
						console.error("Error details:", xhr.responseText);
					}
				});
			});
		});
	</script>

	<?php 
}else{
	header("location:../index.php");
}
?>