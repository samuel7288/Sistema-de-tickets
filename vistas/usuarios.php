<?php 
session_start();
if(isset($_SESSION['usuario']) and isset($_SESSION['rol']) and $_SESSION['rol']=='administrador'){
	?>
	<!DOCTYPE html>
	<html>	<head>
		<title>Usuarios</title>
		<?php require_once "menu.php"; ?>
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">		<style>
			.form-group {
				margin-bottom: 15px;
			}
			.form-group label {
				display: block;
				margin-bottom: 5px;
				font-weight: bold;
			}
			.form-group .form-control {
				width: 100%;
			}
			/* Arreglar altura del select */
			select.form-control.input-sm {
				height: 30px !important;
				line-height: 1.5 !important;
				padding: 5px 10px !important;
			}
			/* Asegurar que el texto no se corte */
			select.form-control option {
				padding: 5px;
				line-height: normal;
			}
		</style>
	</head>
	<body>		<div class="container">
			<h1>Administrar usuarios</h1>
			<div class="row">				<div class="col-sm-4">
					<form id="frmRegistro">
						<div class="form-group">
							<label>Nombre</label>
							<input type="text" class="form-control input-sm" name="nombre" id="nombre">
						</div>
						<div class="form-group">
							<label>Apellido</label>
							<input type="text" class="form-control input-sm" name="apellido" id="apellido">
						</div>
						<div class="form-group">
							<label>Usuario</label>
							<input type="text" class="form-control input-sm" name="usuario" id="usuario">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="text" class="form-control input-sm" name="password" id="password">
						</div>
						<div class="form-group">
							<label>Rol</label>
							<select class="form-control input-sm" name="rol" id="rol">
								<option value="personal">Personal de la Feria</option>
								<option value="administrador">Administrador</option>
							</select>
						</div>
						<div class="form-group">
							<span class="btn btn-primary" id="registro">Registrar</span>
						</div>
					</form>
				</div>
				<div class="col-sm-7">
					<div id="tablaUsuariosLoad"></div>
				</div>
			</div>
		</div>


		<!-- Button trigger modal -->


		<!-- Modal -->
		<div class="modal fade" id="actualizaUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualiza Usuario</h4>
					</div>					<div class="modal-body">
						<form id="frmRegistroU">
							<input type="text" hidden="" id="idUsuario" name="idUsuario">
							<div class="form-group">
								<label>Nombre</label>
								<input type="text" class="form-control input-sm" name="nombreU" id="nombreU">
							</div>
							<div class="form-group">
								<label>Apellido</label>
								<input type="text" class="form-control input-sm" name="apellidoU" id="apellidoU">
							</div>
							<div class="form-group">
								<label>Usuario</label>
								<input type="text" class="form-control input-sm" name="usuarioU" id="usuarioU">
							</div>
							<div class="form-group">
								<label>Rol</label>
								<select class="form-control input-sm" name="rolU" id="rolU">
									<option value="personal">Personal de la Feria</option>
									<option value="administrador">Administrador</option>
								</select>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button id="btnActualizaUsuario" type="button" class="btn btn-warning" data-dismiss="modal">Actualiza Usuario</button>

					</div>
				</div>
			</div>
		</div>

	</body>
	</html>

	<script type="text/javascript">		function agregaDatosUsuario(idusuario){

			$.ajax({
				type:"POST",
				data:"idusuario=" + idusuario,
				url:"../procesos/usuarios/obtenDatosUsuario.php",
				success:function(r){
					dato=jQuery.parseJSON(r);

					$('#idUsuario').val(dato['id_usuario']);
					$('#nombreU').val(dato['nombre']);
					$('#apellidoU').val(dato['apellido']);
					$('#usuarioU').val(dato['email']);
					$('#rolU').val(dato['rol']);
				}
			});
		}

		function eliminarUsuario(idusuario){
			alertify.confirm('¿Desea eliminar este usuario?', function(){ 
				$.ajax({
					type:"POST",
					data:"idusuario=" + idusuario,
					url:"../procesos/usuarios/eliminarUsuario.php",
					success:function(r){
						if(r==1){
							$('#tablaUsuariosLoad').load('usuarios/tablaUsuarios.php');
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
			$('#btnActualizaUsuario').click(function(){

				datos=$('#frmRegistroU').serialize();
				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/usuarios/actualizaUsuario.php",
					success:function(r){

						if(r==1){
							$('#tablaUsuariosLoad').load('usuarios/tablaUsuarios.php');
							alertify.success("Actualizado con exito :D");
						}else{
							alertify.error("No se pudo actualizar :(");
						}
					}
				});
			});
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){

			$('#tablaUsuariosLoad').load('usuarios/tablaUsuarios.php');

			$('#registro').click(function(){

				vacios=validarFormVacio('frmRegistro');

				if(vacios > 0){
					alertify.alert("Debes llenar todos los campos!!");
					return false;
				}

				datos=$('#frmRegistro').serialize();
				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/regLogin/registrarUsuario.php",
					success:function(r){
						//alert(r);

						if(r==1){
							$('#frmRegistro')[0].reset();
							$('#tablaUsuariosLoad').load('usuarios/tablaUsuarios.php');
							alertify.success("Agregado con exito");
						}else{
							alertify.error("Fallo al agregar :(");
						}
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