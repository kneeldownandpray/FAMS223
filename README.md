for sub-sections, as you requested:

```
# FAMS223 Project Environment Setup

This documentation provides a step-by-step guide for setting up the environment for the **FAMS223** project. It includes setting up **Nginx**, **PHP 8.2**, **MySQL/MariaDB**, **Composer**, **Node.js (NVM 20)**, **npm**, and other necessary tools.

---

### Table of Contents

1. [System Preparation](#system-preparation)
   - [Update System Packages](#update-system-packages)
   - [Install Nginx](#install-nginx)
   - [Install PHP 8.2 and FPM](#install-php-82-and-fpm)
   - [Install MySQL (MariaDB)](#install-mysql-mariadb)
     - [Secure MySQL Installation](#secure-mysql-installation)
     - [Bind MySQL to 0.0.0.0 to Allow Remote Connections](#bind-mysql-to-000-to-allow-remote-connections)
     - [Create and Grant Privileges to a New MySQL User](#create-and-grant-privileges-to-a-new-mysql-user)
   - [Install Node.js and NVM (Node Version Manager)](#install-nodejs-and-nvm-node-version-manager)
     - [Install NVM](#install-nvm)
     - [Install Node.js 20.x](#install-nodejs-20x)
   - [Install Composer](#install-composer)
   - [Configure Nginx for the Project](#configure-nginx-for-the-project)
     - [Nginx Configuration](#nginx-configuration)
     - [Enable the Site](#enable-the-site)
     - [Test Nginx Configuration](#test-nginx-configuration)
     - [Restart Nginx](#restart-nginx)
   - [Install NPM Dependencies](#install-npm-dependencies)
   - [Run Laravel Migrations](#run-laravel-migrations)
   - [Access the Project](#access-the-project)
   - [Troubleshooting](#troubleshooting)
2. [Conclusion](#conclusion)

---

### System Preparation

#### Update System Packages
Ensure your system is up-to-date and install necessary packages:

```bash
sudo apt update
sudo apt upgrade -y
```

Install Nginx to serve your web application:

```
sudo apt install nginx -y
```

After installing, start and enable Nginx:

```
sudo systemctl start nginx
sudo systemctl enable nginx
```

Check if Nginx is working by navigating to ```
http://your_server_ip
```

 in a browser.

Install PHP 8.2 along with necessary extensions:

```
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-cli php8.2-curl php8.2-xml php8.2-mbstring php8.2-bcmath php8.2-zip php8.2-gd php8.2-intl php8.2-json -y
```

Start and enable PHP-FPM:

```
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

Ensure that PHP-FPM is running:

```
sudo systemctl status php8.2-fpm
```

Install MySQL or MariaDB server:

```
sudo apt install mariadb-server mariadb-client -y
```

Run the security script to improve the security of your MySQL/MariaDB installation:

```
sudo mysql_secure_installation
```

This will guide you through a series of prompts to configure your installation securely.

Edit the MySQL/MariaDB configuration file to bind MySQL to all IP addresses:

```
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```

Find the line with ```
bind-address
```

 and change it to:

```
bind-address = 0.0.0.0
```

Save the file and restart MySQL/MariaDB to apply the changes:

```
sudo systemctl restart mariadb
```

Log into MySQL as the root user:

```
sudo mysql -u root -p
```

Create a new user and grant privileges:

```
CREATE USER 'fams'@'%' IDENTIFIED BY 'fams';
GRANT ALL PRIVILEGES ON *.* TO 'fams'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

This will allow the ```
fams
```

 user to connect from any IP address.

Install NVM to manage Node.js versions:

```
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
```

After installation, restart your terminal or run:

```
source ~/.bashrc
```

Install the latest version of Node.js (v20.x) via NVM:

```
nvm install 20
nvm use 20
```

Install Composer, the PHP dependency manager:

```
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify the installation:

```
composer --version
```

Create a new Nginx server block (virtual host):

```
sudo nano /etc/nginx/sites-available/focusbackend
```

Paste the following configuration:

```
server {
    listen 8090;
    server_name your_domain_or_ip;  # Replace with your domain or server IP address

    root /var/www/FAMS223/focusbackend/public;  # Path to your Laravel project's public directory
    index index.php index.html index.htm;

    # Ensure all traffic goes to the public directory for Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP Processing via FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # Change PHP version if necessary
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Disable .htaccess files (Laravel does not use them)
    location ~ /\.ht {
        deny all;
    }

    # Prevent access to sensitive files
    location ~* /(?:composer|phpunit|\.env|\.git) {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/focusbackend_access.log;
    error_log /var/log/nginx/focusbackend_error.log;
}
```

Create a symlink to enable the site:

```
sudo ln -s /etc/nginx/sites-available/focusbackend /etc/nginx/sites-enabled/
```

Test the configuration for errors:

```
sudo nginx -t
```

Restart Nginx to apply the changes:

```
sudo systemctl restart nginx
```

Navigate to the Laravel project directory and install the necessary npm dependencies:

```
cd /var/www/FAMS223/focusfrontend
npm install
```

Make sure the ```
.env
```

 file is properly configured with your database credentials. Then run the migrations:

```
cd /var/www/FAMS223/focusbackend
php artisan migrate
```

Your project should now be accessible at ```
http://your_server_ip:8090
```

. Replace ```
your_server_ip
```

 with your actual server's IP address or domain name.

- **Nginx not starting**: Check the error logs located in ```
  /var/log/nginx/
  ```

   for any issues.
- **Permission issues**: Make sure the correct file and folder permissions are set on your project directory.
- **Database connection issues**: Verify that your MySQL server is running and that your ```
  .env
  ```

   file is correctly configured.

---

### Conclusion

You’ve successfully set up the development environment for the FAMS223 project, including Nginx, PHP 8.2, MySQL, Composer, Node.js, and other essential tools. Happy coding!

```

Feel free to copy and paste this entire content into your `README.md` file. Let me know if you need any further modifications!
```
