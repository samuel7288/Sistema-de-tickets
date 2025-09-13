<?php
/**
 * Configuración de rutas y utilidades para el sistema
 * Detecta automáticamente si está en Railway o entorno local
 */

// Detectar si está en Railway
define('IS_RAILWAY', isset($_SERVER['RAILWAY_ENVIRONMENT']));

// Base URL del sistema
if (IS_RAILWAY) {
    // En Railway, usar la URL pública
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'https';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . '://' . $host . '/');
} else {
    // En desarrollo local
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . '://' . $host . $path);
}

// Función para generar rutas de assets
function asset_url($path) {
    // Remover slash inicial si existe
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}

// Función para generar rutas de imágenes
function image_url($imageName) {
    return asset_url('img/' . $imageName);
}

// Función para manejo de errores en producción
function handleError($message) {
    if (IS_RAILWAY) {
        error_log($message);
        return "Ha ocurrido un error. Por favor, inténtalo de nuevo.";
    } else {
        return $message;
    }
}

// Configurar manejo de errores para Railway
if (IS_RAILWAY) {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
?>