# Sistema de Gestión de Tickets - Parque de Diversiones

Sistema completo de venta y gestión de tickets para parque de diversiones con PHP y MySQL.

## 🚀 Despliegue en Railway

### Requisitos
- Cuenta en [Railway](https://railway.app/)
- Repositorio en GitHub

### Pasos para desplegar:

#### 1. Preparar el repositorio
```bash
git add .
git commit -m "Preparar para despliegue en Railway"
git push origin main
```

#### 2. Crear proyecto en Railway
1. Ve a [Railway](https://railway.app/) e inicia sesión
2. Haz clic en "New Project"
3. Selecciona "Deploy from GitHub repo"
4. Conecta tu repositorio de GitHub
5. Selecciona este repositorio

#### 3. Agregar base de datos MySQL
1. En tu proyecto de Railway, haz clic en "New Service"
2. Selecciona "Database" → "Add MySQL"
3. Railway generará automáticamente las credenciales de la base de datos

#### 4. Configurar variables de entorno
Railway configurará automáticamente estas variables:
- `MYSQLHOST` - Host de la base de datos
- `MYSQLUSER` - Usuario de la base de datos  
- `MYSQLPASSWORD` - Contraseña de la base de datos
- `MYSQLDATABASE` - Nombre de la base de datos
- `MYSQLPORT` - Puerto de la base de datos (3306)
- `RAILWAY_ENVIRONMENT` - Marcador de entorno Railway

#### 5. Importar esquema de base de datos
1. Espera a que se despliegue la aplicación
2. Ve a la pestaña "Data" del servicio MySQL en Railway
3. Usa el cliente MySQL para conectarte a la base de datos
4. Ejecuta el script SQL que se encuentra en `bd/tiquetera2.sql`

O usa phpMyAdmin/Adminer desde Railway:
1. Agrega un nuevo servicio usando la imagen de phpMyAdmin
2. Configura las variables de entorno para conectar a tu MySQL
3. Importa el archivo `bd/tiquetera2.sql`

#### 6. Verificar despliegue
- La aplicación estará disponible en la URL que Railway proporcione
- Usuario por defecto: `admin`
- Contraseña por defecto: `hello` (SHA1: `d033e22ae348aeb5660fc2140aec35850c4da997`)

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

## Instalación Local (XAMPP)

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