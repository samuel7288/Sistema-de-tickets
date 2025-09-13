# Dockerfile ultra-simple y confiable para Railway
FROM php:8.2-cli

# Instalar solo extensiones MySQL esenciales
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Configurar permisos
RUN chmod -R 755 .

# Crear archivo de prueba simple
RUN echo '<?php echo "OK - Sistema funcionando"; ?>' > /app/health.php

# Usar servidor PHP built-in (más simple y confiable)
CMD php -S 0.0.0.0:$PORT -t .