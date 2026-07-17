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
mysql -u tareas_user -p < /var/www/tareas/database.sql 
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

# Host Virtual

C:\Windows\System32\crivers\etc\hosts

```
127.0.0.1    tareas.local	
```

## HTTPS con certificado local

```bash
# Generar certificado
sudo mkdir /etc/nginx/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/tareas.key -out /etc/nginx/ssl/tareas.crt -subj "/CN=tareas.local"

# Configurar Nginx para usar el certificado
sudo nano /etc/nginx/sites-available/tareas
```

### Ejemplo configuracipión Nginx

```nginx
server {
    listen 80;
    server_name tareas.local;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name tareas.local;
    root /var/www/tareas;
    index index.php;

    ssl_certificate /etc/nginx/ssl/tareas.crt;
    ssl_certificate_key /etc/nginx/ssl/tareas.key;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## SSH y administración remota

```bash
# Verificar el estado de SSH
sudo systemctl status ssh 
sudo systemctl start ssh

# Generar llave publica
ssh-keygen -t ed25519 -C "martin@local"

# Copiar la llave publica al servidor
type $env:USERPROFILE\.ssh\id_ed25519.pub | ssh martin@172.28.118.50 "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"

# Asegurar los permisos del archivo authorized_keys
sudo chmod 700 ~/.ssh
sudo chmod 600 ~/.ssh/authorized_keys

# Deshabilitar autenticacion contraseña
sudo nano /etc/ssh/sshd_config
# PasswordAuthentication no
# PubkeyAuthentication yes
```

## Config file SSH

```bash
# Crear archivo de configuración
nano ~/.ssh/config

# Crear alias de conexión
Host tareas-local
	HostName 172.28.118.50
	User martin
	IdentityFile ~/.ssh/id_ed25519

# Conectarse mediante alias
ssh tareas-local
```

## Copiar archivos a un servidor remoto

```bash
# Copiar archivos de una maquina local a un servidor remoto
scp archivo.txt tareas-local:/tmp/

# Copiar archivos del servidor remoto a la maquina local
scp tareas-local:/tmp/archivo.txt ~/Downloads
```

## Ejecutar comandos remotos sin entrar al servidor

```bash
ssh tareas-local systemctl status nginx
```

## Hardening final del servidor SSH

```bash
sudo nano /etc/ssh/sshd_config

# Cambiar el puerto por defecto 
Port 2222

# Solo permitir usuarios especificos
AllowUsers martin

# Desactivar reenvio de X11
X11Forwarding no

# Tiempo maximo para autenticarse
LoginGraceTime 20

# Maximo de intentos de autenticacion
MaxAuthTries 3

# Desactivar conexiones sin actividad despues de 5 minutos
ClientAliveInterval 300
ClientAliveCountMax 2
```
