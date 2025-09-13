<?php 
session_start();
if(isset($_SESSION['usuario']) and isset($_SESSION['rol']) and $_SESSION['rol']=='administrador'){

	?>


	<!DOCTYPE html>
	<html>
	<head>
		<title>Edades</title>
		<?php require_once "menu.php"; ?>
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
	</head>
	<body>
		<div class="container">
			<!-- Título Principal con Icono -->
			<div class="page-header">
				<div class="header-content">
					<i class="fas fa-users page-icon"></i>
					<h1 class="page-title">Gestión de Edades</h1>
					<p class="page-subtitle">Administra los rangos de edad para los tickets</p>
				</div>
			</div>

			<div class="row">
				<!-- FORMULARIO MODERNIZADO -->
				<div class="col-sm-4">
					<div class="form-card">
						<div class="form-header">
							<i class="fas fa-plus-circle form-icon"></i>
							<h3 class="form-title">Nueva Categoría de Edad</h3>
						</div>
						<form id="frmEdades" class="modern-form">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-tag label-icon"></i>
									Nombre de la Categoría
								</label>
								<input type="text" 
									   class="form-control modern-input" 
									   id="nombre" 
									   name="nombre" 
									   placeholder="Ej: Niños, Adultos, Tercera Edad">
								<div class="input-feedback" id="nombreFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-arrow-down label-icon"></i>
									Edad Mínima
								</label>
								<input type="number" 
									   class="form-control modern-input" 
									   id="edadMin" 
									   name="edadMin" 
									   min="0" 
									   max="150" 
									   placeholder="Ej: 5">
								<small class="input-help">
									<i class="fas fa-info-circle"></i>
									Edad en años (0-150)
								</small>
								<div class="input-feedback" id="edadMinFeedback"></div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-arrow-up label-icon"></i>
									Edad Máxima
								</label>
								<input type="number" 
									   class="form-control modern-input" 
									   id="edadMax" 
									   name="edadMax" 
									   min="0" 
									   max="150" 
									   placeholder="Ej: 12">
								<small class="input-help">
									<i class="fas fa-info-circle"></i>
									Edad en años (0-150)
								</small>
								<div class="input-feedback" id="edadMaxFeedback"></div>
							</div>

							<div id="validacionEdades" class="validation-alert" style="display:none;">
								<div class="alert-content">
									<i class="fas fa-exclamation-triangle alert-icon"></i>
									<span class="alert-message"></span>
								</div>
							</div>

							<div class="form-actions">
								<button type="button" id="btnAgregarEdad" class="btn btn-primary modern-btn">
									<i class="fas fa-plus btn-icon"></i>
									<span class="btn-text">Agregar Categoría</span>
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
							<h3 class="table-title">Categorías Registradas</h3>
						</div>
						<div class="table-responsive-custom">
							<div id="tablaEdadesLoad"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Button trigger modal -->


		<!-- Modal -->
		<div class="modal fade" id="abremodalEdadesUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualizar Edad</h4>
					</div>
					<div class="modal-body">
						<form id="frmEdadesU">
							<input type="text" hidden="" id="idedadU" name="idedadU">
							<label>Nombre</label>
							<input type="text" class="form-control input-sm" id="nombreU" name="nombreU">
							<label>Edad Mínima</label>
							<input type="number" class="form-control input-sm" id="edadMinU" name="edadMinU" min="0" max="150">
							<label>Edad Máxima</label>
							<input type="number" class="form-control input-sm" id="edadMaxU" name="edadMaxU" min="0" max="150">
							<div id="validacionEdadesU" class="alert" style="display:none; margin-top:10px;"></div>
						</form>
					</div>
					<div class="modal-footer">
						<button id="btnAgregarEdadU" type="button" class="btn btn-primary" data-dismiss="modal">Actualizar</button>

					</div>
				</div>
			</div>
		</div>

	</body>
	</html>

	<script type="text/javascript">
		// Función para validar rangos de edad
		function validarRangoEdad(edadMin, edadMax, esActualizacion = false, idActual = null) {
			var alertDiv = esActualizacion ? '#validacionEdadesU' : '#validacionEdades';
			
			// Limpiar alertas previas
			$(alertDiv).hide().removeClass('alert-danger alert-warning alert-success');
			
			// Convertir a números
			edadMin = parseInt(edadMin);
			edadMax = parseInt(edadMax);
			
			// Validaciones básicas
			if (isNaN(edadMin) || isNaN(edadMax)) {
				mostrarAlerta(alertDiv, 'Las edades deben ser números válidos', 'danger');
				return false;
			}
			
			if (edadMin < 0 || edadMax < 0) {
				mostrarAlerta(alertDiv, 'Las edades no pueden ser números negativos', 'danger');
				return false;
			}
			
			if (edadMin > 150 || edadMax > 150) {
				mostrarAlerta(alertDiv, 'Las edades no pueden ser mayores a 150 años', 'danger');
				return false;
			}
			
			if (edadMin >= edadMax) {
				mostrarAlerta(alertDiv, 'La edad mínima debe ser menor que la edad máxima', 'danger');
				return false;
			}
			
			// Validar solapamiento con rangos existentes (esta validación se hará en el servidor)
			return true;
		}
		
		function mostrarAlerta(selector, mensaje, tipo) {
			$(selector).removeClass('alert-danger alert-warning alert-success')
						.addClass('alert-' + tipo)
						.html('<i class="fas fa-exclamation-triangle"></i> ' + mensaje)
						.show();
		}
		
		// Validación en tiempo real para el formulario principal
		$('#edadMin, #edadMax').on('input', function() {
			var edadMin = $('#edadMin').val();
			var edadMax = $('#edadMax').val();
			
			if (edadMin && edadMax) {
				validarRangoEdad(edadMin, edadMax);
			}
		});
		
		// Validación en tiempo real para el formulario de actualización
		$('#edadMinU, #edadMaxU').on('input', function() {
			var edadMin = $('#edadMinU').val();
			var edadMax = $('#edadMaxU').val();
			
			if (edadMin && edadMax) {
				validarRangoEdad(edadMin, edadMax, true, $('#idedadU').val());
			}
		});

		function agregaDatosEdad(idedad){

			$.ajax({
				type:"POST",
				data:"idedad=" + idedad,
				url:"../procesos/edades/obtenDatosEdad.php",
				success:function(r){
					dato=jQuery.parseJSON(r);
					$('#idedadU').val(dato['id_edad']);
					$('#nombreU').val(dato['nombre']);
					$('#edadMinU').val(dato['edadMin']);
					$('#edadMax').val(dato['edadMax']);					

				}
			});
		}

		function eliminarEdad(idedad){
			alertify.confirm('¿Desea eliminar este Rango de Edad?', function(){ 
				$.ajax({
					type:"POST",
					data:"idedad=" + idedad,
					url:"../procesos/edades/eliminarEdad.php",
					success:function(r){
						if(r==1){
							$('#tablaEdadesLoad').load("edades/tablaEdades.php");
							alertify.success("Eliminado con exito!!");
						}else{
							alertify.error("No se pudo eliminar :(");
						}
					}
				});
			}, function(){ 
				alertify.error('Cancelo !')
			});
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function(){

			$('#tablaEdadesLoad').load("edades/tablaEdades.php");

			$('#btnAgregarEdad').click(function(){

				vacios=validarFormVacio('frmEdades');

				if(vacios > 0){
					alertify.alert("Debes llenar todos los campos!!");
					return false;
				}
				
				// Validaciones adicionales de edad
				var nombre = $('#nombre').val().trim();
				var edadMin = parseInt($('#edadMin').val());
				var edadMax = parseInt($('#edadMax').val());
				
				if (nombre.length < 2) {
					alertify.error("El nombre debe tener al menos 2 caracteres");
					return false;
				}
				
				if (!validarRangoEdad(edadMin, edadMax)) {
					return false;
				}

				datos=$('#frmEdades').serialize();

				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/edades/agregaEdad.php",
					success:function(r){
						if(r==1){
							$('#frmEdades')[0].reset();
							$('#validacionEdades').hide();
							$('#tablaEdadesLoad').load("edades/tablaEdades.php");
							alertify.success("Rango de edad agregado con éxito");
						}else if(r.indexOf('ERROR:') !== -1){
							alertify.error(r);
						}else{
							alertify.error("No se pudo agregar el rango de edad");
						}
					}
				});
			});
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#btnAgregarEdadU').click(function(){
				// Validaciones para actualización
				var nombre = $('#nombreU').val().trim();
				var edadMin = parseInt($('#edadMinU').val());
				var edadMax = parseInt($('#edadMaxU').val());
				
				if (nombre.length < 2) {
					alertify.error("El nombre debe tener al menos 2 caracteres");
					return false;
				}
				
				if (!validarRangoEdad(edadMin, edadMax, true, $('#idedadU').val())) {
					return false;
				}
				
				datos=$('#frmEdadesU').serialize();

				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/edades/actualizaEdad.php",
					success:function(r){
						if(r==1){
							$('#frmEdades')[0].reset();
							$('#validacionEdadesU').hide();
							$('#tablaEdadesLoad').load("edades/tablaEdades.php");
							alertify.success("Rango de edad actualizado con éxito");
						}else if(r.indexOf('ERROR:') !== -1){
							alertify.error(r);
						}else{
							alertify.error("No se pudo actualizar el rango de edad");
						}
					}
				});
			})
		})
	</script>


	<?php 
}else{
	header("location:../index.php");
}
?>