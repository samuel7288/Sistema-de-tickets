<?php 
try {
    require_once "clases/Conexion.php";
    $obj = new conectar();
    $conexion = $obj->conexion();
    
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $sql = "SELECT * from usuarios where email='admin'";
    $result = mysqli_query($conexion, $sql);
    $validar = 0;
    
    if ($result && mysqli_num_rows($result) > 0) {
        $validar = 1;
    }
    
    $db_connected = true;
    $error_message = null;
    
} catch (Exception $e) {
    $db_connected = false;
    $error_message = $e->getMessage();
    $validar = 0;
    
    // Log el error para debugging
    error_log("Error de base de datos en index.php: " . $error_message);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Ventas de Tickets</title>
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="vistas/css/style.css">
    <script src="librerias/jquery-3.2.1.min.js"></script>
    <script src="js/funciones.js"></script>
    <style>
        .error-container {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }
        .success-container {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="login-body">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            
            <?php if (!$db_connected): ?>
                <!-- Mostrar información de configuración si no hay BD -->
                <div class="col-12">
                    <div class="error-container">
                        <h3>🔧 Configuración de Base de Datos Requerida</h3>
                        <p><strong>La aplicación se ha desplegado correctamente, pero necesita configuración de base de datos.</strong></p>
                        
                        <h4>📋 Pasos para completar la configuración:</h4>
                        <ol>
                            <li><strong>Agregar servicio MySQL:</strong>
                                <ul>
                                    <li>En Railway, haz clic en "+ New"</li>
                                    <li>Selecciona "Database" → "Add MySQL"</li>
                                    <li>Espera 2-3 minutos a que se provisione</li>
                                </ul>
                            </li>
                            <li><strong>Importar esquema:</strong>
                                <ul>
                                    <li>Conecta a MySQL usando Railway CLI o phpMyAdmin</li>
                                    <li>Ejecuta el archivo <code>bd/tiquetera2.sql</code></li>
                                </ul>
                            </li>
                            <li><strong>Variables configuradas automáticamente:</strong>
                                <ul>
                                    <li>MYSQLHOST: <?php echo $_SERVER['MYSQLHOST'] ?? 'No configurada'; ?></li>
                                    <li>MYSQLUSER: <?php echo $_SERVER['MYSQLUSER'] ?? 'No configurada'; ?></li>
                                    <li>MYSQLDATABASE: <?php echo $_SERVER['MYSQLDATABASE'] ?? 'No configurada'; ?></li>
                                </ul>
                            </li>
                        </ol>
                        
                        <div style="margin-top: 20px;">
                            <a href="health-check.php" class="btn btn-info">🔍 Ver Diagnóstico Completo</a>
                            <a href="javascript:location.reload()" class="btn btn-success">🔄 Verificar Conexión</a>
                        </div>
                        
                        <p style="margin-top: 15px;">
                            <small>Error técnico: <?php echo htmlspecialchars($error_message); ?></small>
                        </p>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Mostrar login normal si la BD está conectada -->
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <div class="success-container text-center" style="margin-bottom: 20px;">
                        <h4>✅ Sistema Funcionando Correctamente</h4>
                        <p>Base de datos conectada y lista para usar</p>
                    </div>
                    
                    <div class="panel panel-primary login-panel">
                        <div class="panel-heading login-header">
                            <h3 class="text-center">Sistema de Ventas de Tickets</h3>
                        </div>
                        <div class="panel-body login-body-content">
                            <form id="frmLogin" class="login-form">
                                <div class="form-group">
                                    <label for="usuario">Usuario</label>
                                    <input type="text" class="form-control input-sm" name="usuario" id="usuario" placeholder="admin">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control input-sm" placeholder="hello">
                                </div>
                                <div class="form-group text-center mt-4">
                                    <button type="button" class="btn btn-primary btn-sm btn-login" id="entrarSistema">Entrar</button>
                                    <?php if(!$validar): ?>
                                        <a href="registro.php" class="btn btn-danger btn-sm btn-register">Registrar</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="text-center" style="margin-top: 20px;">
                        <small style="color: #666;">
                            Credenciales por defecto: admin / hello<br>
                            <a href="health-check.php" style="color: #007bff;">Ver diagnóstico del sistema</a>
                        </small>
                    </div>
                </div>
                <div class="col-sm-4"></div>
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>