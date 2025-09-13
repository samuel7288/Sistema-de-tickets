# FeriaPlazaMundo - Sistema de Tickets

Sistema integral de gestión de tickets para ferias y eventos, con funcionalidades avanzadas de administración de usuarios y anulación de tickets.

## Características Principales

### 🎫 Gestión de Tickets
- Venta de tickets por categorías y edades
- Generación automática de números de ticket únicos
- Reportes de ventas en PDF
- Historial completo de transacciones

### 👥 Sistema de Roles de Usuario
- **Administrador**: Acceso completo al sistema
- **Personal de Feria**: Acceso limitado a ventas y operaciones básicas
- Control de acceso granular por funcionalidad

### ❌ Sistema de Anulación de Tickets
- Anulación de tickets con motivo obligatorio
- Búsqueda por número de ticket, documento o fecha
- Auditoria completa de anulaciones
- Historial de tickets anulados

## Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache/Nginx
- Extensiones PHP: mysqli, gd, mbstring

### Configuración de Base de Datos

1. Crear la base de datos:
```sql
CREATE DATABASE tiquetera2;
```

2. Importar la estructura completa:
```bash
mysql -u usuario -p tiquetera2 < bd/tiquetera2.sql
```

### Configuración de la Aplicación

1. Editar `config/conexion.php` con los datos de tu base de datos:
```php
$server = "localhost";
$user = "tu_usuario";
$pass = "tu_password";
$bd = "tiquetera2";
```

2. Configurar permisos de escritura en la carpeta del proyecto.

## Uso del Sistema

### Primer Acceso
1. Registrar el primer usuario como administrador
2. Configurar categorías y edades de tickets
3. Crear tickets disponibles para venta

### Gestión de Usuarios
- Los administradores pueden asignar roles durante el registro
- Solo administradores pueden acceder a:
  - Dashboard completo
  - Gestión de usuarios
  - Gestión de edades
  - Anulación de tickets

### Anulación de Tickets
1. Acceder a **Ventas > Anular Tickets**
2. Buscar ticket por:
   - Número de ticket
   - Documento del cliente
   - Fecha/hora de compra
3. Seleccionar ticket y especificar motivo
4. Confirmar anulación (acción irreversible)

## Estructura del Proyecto

```
FeriaPlazaMundo/
├── bd/                     # Base de datos
│   └── tiquetera2.sql     # Estructura completa
├── clases/                # Clases PHP
├── config/                # Configuración
├── procesos/              # Procesos AJAX
├── vistas/                # Interfaz de usuario
├── css/                   # Estilos
├── js/                    # JavaScript
└── librerias/             # Librerías externas
```

## Funcionalidades por Rol

### Administrador
- ✅ Dashboard completo
- ✅ Gestión de usuarios
- ✅ Gestión de categorías
- ✅ Gestión de edades
- ✅ Gestión de tickets
- ✅ Ventas y reportes
- ✅ Anulación de tickets
- ✅ Historial de anulaciones

### Personal de Feria
- ✅ Ventas de tickets
- ✅ Reportes básicos
- ❌ Gestión de usuarios
- ❌ Gestión de configuración
- ❌ Anulación de tickets

## Base de Datos

El archivo `bd/tiquetera2.sql` contiene la estructura completa incluyendo:

- Tabla `usuarios` con campo `rol`
- Tabla `ventas` con campos de anulación:
  - `horaCompra`
  - `documento_cliente`
  - `numero_ticket`
  - `estado`
  - `id_usuario_anulacion`
  - `fecha_anulacion`
  - `motivo_anulacion`
- Índices optimizados para búsquedas
- Datos de ejemplo

## Seguridad

- Validación de sesiones en todas las páginas
- Control de acceso basado en roles
- Sanitización de entradas
- Protección contra inyección SQL
- Auditoria de acciones críticas

## Soporte

Para reportar problemas o solicitar nuevas funcionalidades, contactar al administrador del sistema.

---

**Versión**: 2.0  
**Última actualización**: Junio 2025  
**Desarrollado para**: Feria Plaza Mundo