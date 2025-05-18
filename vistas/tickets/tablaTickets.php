<?php 
	require_once "../../clases/Conexion.php";
	$c= new conectar();
	$conexion=$c->conexion();
	$sql = "SELECT 
            tic.nombre,
            tic.descripcion,
            tic.cantidad,
            tic.precio,
            img.ruta,
            cat.nombreCategoria,
            tic.id_ticket
        FROM tickets AS tic 
        INNER JOIN imagenes AS img ON tic.id_imagen = img.id_imagen
        INNER JOIN categorias AS cat ON tic.id_categoria = cat.id_categoria
        WHERE tic.cantidad > 0";
	$result=mysqli_query($conexion,$sql);

 ?>

<table class="table table-hover table-condensed table-bordered" style="text-align: center;">
	<caption><label>Tipos Tickets</label></caption>
	<tr>
		<td>Nombre</td>
		<td>Descripcion</td>
		<td>Cantidad</td>
		<td>Precio</td>
		<td>Imagen</td>
		<td>Categoria</td>
		<td>Editar</td>
		<td>Eliminar</td>
	</tr>

	<?php while($ver=mysqli_fetch_row($result)): ?>

	<tr>
		<td><?php echo $ver[0]; ?></td>
		<td><?php echo $ver[1]; ?></td>
		<td><?php echo $ver[2]; ?></td>
		<td><?php echo $ver[3]; ?></td>
		<td>
			<?php 
			$imgVer=explode("/", $ver[4]);
			$imgruta=implode("/", array_slice($imgVer, -3));
			?>
			<img width="80" height="80" src="<?php echo $imgruta ?>">
		</td>
		<td><?php echo $ver[5]; ?></td>
		<td>
			<span  data-toggle="modal" data-target="#abremodalUpdateTicket" class="btn btn-warning btn-xs" onclick="agregaDatosTicket('<?php echo $ver[6] ?>')">
				<span class="glyphicon glyphicon-pencil"></span>
			</span>
		</td>
		<td>
			<span class="btn btn-danger btn-xs" onclick="eliminaTicket('<?php echo $ver[6] ?>')">
				<span class="glyphicon glyphicon-remove"></span>
			</span>
		</td>
	</tr>
<?php endwhile; ?>
</table>