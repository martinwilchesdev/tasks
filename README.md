```bash
# Instalar nginx, php, mysql
sudo apt install nginx -y
sudo apt install php8.3-cli
sudo apt install mysql-server

# Crear proyecto
mkdir -p /var/www/tareas
cd tareas

# Crear archivo .env y cambiar propietario
nano .env
sudo chown martin:martin .env

# Cambiar permisos
sudo chmod 600 .env

# Crear usuario en mysql y darle permisos
sudo mysql
CREATE USER 'tareas_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES on tareas_db.* TO 'tareas_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Verificar conexion a mysql
mysql -u tareas_user -p tareas_db

# Crear archivo de scripts SQL
nano /var/www/tareas/database.sql

# Ejecutar los scripts en MySQL
msql -u tareas_user -p < /var/www/tareas/database.sql 
```

## Configurar NGINX

Nginx necesita saber 3 cosas: en que puerto escuchar, donde estan los archivos de la app y como pasarle las peticiones PHP a PHP-FPM.

```bash
# Crear el archivo de configuracion
sudo nano /etc/nginx/sites-available/tareas

# Activar el sitio
sudo -ln /etc/nginx/sites-available/tareas /etc/nginx/sites-enabled/

# Validar sintaxis de Nginx
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx

# Levantar PHP-FPM - el proceso que ejecuta codigo PHP
sudo systemctl start php8.3-fpm
sudo systemctl status php8.3-fpm

# Verificar que el socket que se configuro en Nginx existe
ls -la /var/run/php

# Nginx solo recibe archivos estaticos, no ejecuta PHP
# Navegador -> Nginx -> PHP-FPM -> Ejecutar PHP -> Regresar HTML -> Nginx -> Navegador 

# En produccion nunca dejar el sitio default de Nginx activo
sudo rm /etc/nginx/sites-enabled/default
sudo systemctl reload nginx
```
