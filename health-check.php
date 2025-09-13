<?php
// Test de conectividad básica
echo "<h1>🎯 Test del Sistema de Tickets</h1>";

// Test 1: PHP funcionando
echo "<h2>✅ PHP Status:</h2>";
echo "PHP Version: " . phpinfo(INFO_GENERAL) ? "PHP " . phpversion() : "Error";
echo "<br>";

// Test 2: Extensiones necesarias
echo "<h2>📦 Extensiones PHP:</h2>";
$extensions = ['mysqli', 'pdo', 'pdo_mysql'];
foreach($extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "✅ Cargada" : "❌ No disponible") . "<br>";
}

// Test 3: Variables de entorno
echo "<h2>🔧 Variables de Railway:</h2>";
echo "RAILWAY_ENVIRONMENT: " . ($_SERVER['RAILWAY_ENVIRONMENT'] ?? 'No configurada') . "<br>";
echo "MYSQLHOST: " . ($_SERVER['MYSQLHOST'] ?? 'No configurada') . "<br>";
echo "MYSQLUSER: " . ($_SERVER['MYSQLUSER'] ?? 'No configurada') . "<br>";
echo "MYSQLDATABASE: " . ($_SERVER['MYSQLDATABASE'] ?? 'No configurada') . "<br>";

// Test 4: Archivos del proyecto
echo "<h2>📁 Archivos del Sistema:</h2>";
$files = ['index.php', 'clases/Conexion.php', 'config/conexion.php'];
foreach($files as $file) {
    echo $file . ": " . (file_exists($file) ? "✅ Existe" : "❌ No encontrado") . "<br>";
}

// Test 5: Conectividad a base de datos (si está configurada)
echo "<h2>🗄️ Conexión a Base de Datos:</h2>";
if (isset($_SERVER['MYSQLHOST']) && isset($_SERVER['MYSQLUSER'])) {
    try {
        require_once 'clases/Conexion.php';
        $obj = new conectar();
        $conexion = $obj->conexion();
        if ($conexion) {
            echo "✅ Conexión exitosa a MySQL<br>";
            // Test básico de tabla
            $result = mysqli_query($conexion, "SHOW TABLES");
            if ($result) {
                echo "✅ Base de datos accesible - Tablas: " . mysqli_num_rows($result) . "<br>";
            }
            mysqli_close($conexion);
        }
    } catch (Exception $e) {
        echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⏳ Variables de BD no configuradas aún<br>";
}

echo "<h2>🚀 Próximos Pasos:</h2>";
echo "<ol>";
echo "<li>Si ves este mensaje, el contenedor PHP funciona correctamente</li>";
echo "<li>Configura el servicio MySQL en Railway</li>";
echo "<li>Importa el schema de BD (bd/tiquetera2.sql)</li>";
echo "<li>Accede a index.php para usar el sistema</li>";
echo "</ol>";

echo "<br><a href='index.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🎫 Ir al Sistema de Tickets</a>";
?>