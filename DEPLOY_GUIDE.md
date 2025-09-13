# Guía de Despliegue en Railway - Sistema de Tickets

Esta guía te llevará paso a paso para desplegar tu sistema de gestión de tickets en Railway.

## 📋 Pre-requisitos

- ✅ Cuenta en [GitHub](https://github.com)
- ✅ Cuenta en [Railway](https://railway.app)
- ✅ Tu código debe estar subido a GitHub

## 🚀 Paso 1: Preparar el Repositorio

1. **Commitear todos los cambios:**
```bash
git add .
git commit -m "Preparar para despliegue en Railway"
git push origin main
```

2. **Verificar archivos necesarios están incluidos:**
- ✅ `railway.json` - Configuración de Railway
- ✅ `nixpacks.toml` - Configuración del build
- ✅ `.htaccess` - Configuración de Apache
- ✅ `config/config.php` - Configuración de rutas
- ✅ `bd/tiquetera2.sql` - Schema de base de datos

## 🎯 Paso 2: Crear Proyecto en Railway

### 2.1 Acceder a Railway
1. Ve a [railway.app](https://railway.app)
2. Inicia sesión con tu cuenta de GitHub
3. Haz clic en **"New Project"**

### 2.2 Conectar Repositorio
1. Selecciona **"Deploy from GitHub repo"**
2. Busca tu repositorio: `Sistema-de-gestion-de-tickets-para-un-parque-de-diversiones`
3. Haz clic en **"Deploy"**

### 2.3 Configuración inicial
- Railway detectará automáticamente que es un proyecto PHP
- Usará la configuración de `nixpacks.toml` para el build
- El despliegue inicial tomará unos minutos

## 🗄️ Paso 3: Configurar Base de Datos MySQL

### 3.1 Agregar servicio MySQL
1. En tu proyecto de Railway, haz clic en **"+ New"**
2. Selecciona **"Database"**
3. Elige **"Add MySQL"**
4. Espera a que se provisione (2-3 minutos)

### 3.2 Variables automáticas
Railway configurará automáticamente estas variables en tu aplicación:
- `MYSQLHOST` - Host del servidor MySQL
- `MYSQLUSER` - Usuario de la base de datos
- `MYSQLPASSWORD` - Contraseña de la base de datos  
- `MYSQLDATABASE` - Nombre de la base de datos
- `MYSQLPORT` - Puerto (3306)
- `RAILWAY_ENVIRONMENT` - Indicador de entorno Railway

## 📊 Paso 4: Importar Esquema de Base de Datos

### Opción A: Usando Railway CLI (Recomendado)
1. **Instalar Railway CLI:**
```bash
npm install -g @railway/cli
# o usando curl:
curl -fsSL https://railway.app/install.sh | sh
```

2. **Autenticarse:**
```bash
railway login
```

3. **Conectar al proyecto:**
```bash
railway link
```

4. **Conectar a MySQL e importar:**
```bash
railway connect MySQL
# En el prompt MySQL:
mysql> source bd/tiquetera2.sql;
```

### Opción B: Usando phpMyAdmin
1. **Agregar phpMyAdmin:**
   - En Railway, agregar nuevo servicio
   - Usar template "phpMyAdmin"
   - Configurar variables para conectar a tu MySQL

2. **Importar schema:**
   - Acceder a phpMyAdmin
   - Seleccionar tu base de datos
   - Ir a "Importar"
   - Subir archivo `bd/tiquetera2.sql`

### Opción C: Usando cliente MySQL externo
1. **Obtener credenciales:**
   - Ve a tu servicio MySQL en Railway
   - Copia las credenciales de conexión

2. **Conectar e importar:**
```bash
mysql -h [MYSQLHOST] -u [MYSQLUSER] -p[MYSQLPASSWORD] -P [MYSQLPORT] [MYSQLDATABASE] < bd/tiquetera2.sql
```

## 🔧 Solución Rápida - Errores de Build

### ❌ **Errores Comunes:**
```bash
# Error de Composer
composer: command not found

# Error de PHP/Nixpkgs
error: php80 has been dropped due to the lack of maintenance

# Error de extensiones PHP
Package requirements (zlib) were not met
```

### ✅ **Soluciones (En orden de prioridad):**

#### **Opción 1: Dockerfile Completo (Recomendado)**
```bash
# Ya actualizado con dependencias necesarias
git add .
git commit -m "Usar Dockerfile completo con dependencias"
git push
```
✅ Incluye: Apache, GD, ZIP, todas las extensiones PHP

#### **Opción 2: Dockerfile Simple**
```bash
# Si hay problemas con extensiones adicionales
cp Dockerfile.simple Dockerfile
git add .
git commit -m "Usar Dockerfile simple - solo extensiones esenciales"
git push
```
✅ Solo extensiones MySQL esenciales

#### **Opción 3: Dockerfile Mínimo**
```bash
# Configuración ultra-mínima
cp Dockerfile.minimal Dockerfile
git add .
git commit -m "Usar Dockerfile mínimo con PHP built-in server"
git push
```
✅ PHP CLI con servidor built-in

#### **Opción 4: Volver a Nixpacks**
```bash
# Si prefieres Nixpacks
rm Dockerfile
cp railway-nixpacks.json railway.json
cp nixpacks-ultra-simple.toml nixpacks.toml
git add .
git commit -m "Volver a Nixpacks ultra-simple"
git push
```
✅ Sin dependencias complejas

## 🔧 Paso 5: Verificar Configuración

### 5.1 Variables de entorno
En Railway, ve a tu servicio de la aplicación → **Variables** y verifica:
- ✅ `RAILWAY_ENVIRONMENT=production`
- ✅ Variables MySQL aparecen automáticamente

### 5.2 Build y despliegue
1. Ve a la pestaña **"Deployments"**
2. Verifica que el build se completó exitosamente
3. Copia la URL pública generada

### 5.3 Probar la aplicación
1. Abre la URL en tu navegador
2. Deberías ver la página de login
3. Credenciales por defecto:
   - **Usuario:** `admin`
   - **Contraseña:** `hello`

## 🎉 Paso 6: Configuración Post-Despliegue

### 6.1 Crear usuario administrador personalizado
1. Accede al sistema con las credenciales por defecto
2. Ve a **"Usuarios"** → **"Agregar Usuario"**
3. Crea tu usuario administrador personalizado
4. Opcional: Elimina o cambia las credenciales por defecto

### 6.2 Configurar datos iniciales
1. **Categorías:** Ya están pre-configuradas
2. **Grupos de edad:** Ya están pre-configurados
3. **Tickets:** Ya están pre-configurados con datos de ejemplo
4. **Personalizar:** Modifica según tus necesidades

## 🔍 Solución de Problemas

### Error de conexión a base de datos
```
Error: Could not connect to database
```
**Solución:**
1. Verifica que el servicio MySQL esté corriendo
2. Confirma que las variables de entorno están configuradas
3. Revisa los logs en Railway

### Error de build con Composer
```
/bin/bash: line 1: composer: command not found
ERROR: failed to build: failed to solve
```
**Solución:**
1. **El archivo `nixpacks.toml` ya está configurado correctamente** con `php81Packages.composer`
2. **Si persiste el problema, usa la configuración alternativa:**
   ```bash
   # Renombrar la configuración simple
   mv nixpacks-simple.toml nixpacks.toml
   git add .
   git commit -m "Usar configuración sin Composer"
   git push
   ```
3. **Alternativa manual**: Las librerías están incluidas en `librerias/` y `vendor/`, no requiere Composer en Railway

### Error 500 - Internal Server Error
**Solución:**
1. Revisa los logs de la aplicación en Railway
2. Verifica permisos de archivos
3. Confirma que el schema de BD fue importado correctamente

### Imágenes no se muestran
**Solución:**
1. Verifica que las imágenes están en la carpeta `img/`
2. Confirma que las rutas en la BD son correctas (sin `../`)
3. Revisa permisos de la carpeta de imágenes

## 📈 Monitoreo y Mantenimiento

### Logs
- Ve a tu proyecto en Railway → **"Observability"** → **"Logs"**
- Filtrar por servicio (App o MySQL)
- Revisar errores y advertencias

### Métricas
- CPU y memoria usage
- Requests por minuto
- Tiempo de respuesta

### Backups
- Railway hace backups automáticos de MySQL
- Configura backups adicionales si es necesario

## 🔒 Seguridad en Producción

### Variables sensibles
- Nunca hardcodear credenciales en el código
- Usar variables de entorno de Railway
- Cambiar credenciales por defecto

### HTTPS
- Railway proporciona SSL automático
- Todas las conexiones son cifradas

### Actualizaciones
- Mantener PHP y dependencias actualizadas
- Revisar logs de seguridad regularmente

## 📞 Soporte

Si encuentras problemas:
1. **Railway Docs:** [docs.railway.app](https://docs.railway.app)
2. **Railway Discord:** [railway.app/discord](https://railway.app/discord)
3. **Logs de la aplicación** en Railway Dashboard

---

## ✅ Checklist Final

- [ ] Código subido a GitHub
- [ ] Proyecto creado en Railway
- [ ] MySQL configurado
- [ ] Schema importado exitosamente  
- [ ] Variables de entorno configuradas
- [ ] Aplicación accesible vía URL pública
- [ ] Login funcionando correctamente
- [ ] Usuario administrador personalizado creado
- [ ] Datos iniciales verificados

¡Tu sistema de gestión de tickets ya está funcionando en Railway! 🎉