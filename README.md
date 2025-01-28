# FAMS223 Project Environment Setup

This documentation provides a step-by-step guide for setting up the environment for the **FAMS223** project. It includes setting up **Nginx**, **PHP 8.2**, **MySQL/MariaDB**, **Composer**, **Node.js (NVM 20)**, **npm**, and other necessary tools.

---

## 1. **System Preparation**

### 1.1 Update System Packages
Ensure your system is up-to-date and install necessary packages:

```bash
sudo apt update
sudo apt upgrade -y
2. Install Nginx
Install Nginx to serve your web application:

bash
Copy
Edit
sudo apt install nginx -y
After installing, start and enable Nginx:

bash
Copy
Edit
sudo systemctl start nginx
sudo systemctl enable nginx
Check if Nginx is working by navigating to http://your_server_ip in a browser.

3. Install PHP 8.2 and FPM
Install PHP 8.2 along with necessary extensions:

bash
Copy
Edit
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-cli php8.2-curl php8.2-xml php8.2-mbstring php8.2-bcmath php8.2-zip php8.2-gd php8.2-intl php8.2-json -y
Start and enable PHP-FPM:

bash
Copy
Edit
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
Ensure that PHP-FPM is running:

bash
Copy
Edit
sudo systemctl status php8.2-fpm
4. Install MySQL (MariaDB)
Install MySQL or MariaDB server:

bash
Copy
Edit
sudo apt install mariadb-server mariadb-client -y
4.1 Secure MySQL Installation
Run the security script to improve the security of your MySQL/MariaDB installation:

bash
Copy
Edit
sudo mysql_secure_installation
This will guide you through a series of prompts to configure your installation securely.

4.2 Bind MySQL to 0.0.0.0 to Allow Remote Connections
Edit the MySQL/MariaDB configuration file to bind MySQL to all IP addresses:

bash
Copy
Edit
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
Find the line with bind-address and change it to:

css
Copy
Edit
bind-address = 0.0.0.0
Save the file and restart MySQL/MariaDB to apply the changes:

bash
Copy
Edit
sudo systemctl restart mariadb
4.3 Create and Grant Privileges to a New MySQL User
Log into MySQL as the root user:

bash
Copy
Edit
sudo mysql -u root -p
Create a new user and grant privileges:

sql
Copy
Edit
CREATE USER 'fams'@'%' IDENTIFIED BY 'fams';
GRANT ALL PRIVILEGES ON *.* TO 'fams'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
This will allow the fams user to connect from any IP address.

5. Install Node.js and NVM (Node Version Manager)
5.1 Install NVM
Install NVM to manage Node.js versions:

bash
Copy
Edit
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
After installation, restart your terminal or run:

bash
Copy
Edit
source ~/.bashrc
5.2 Install Node.js 20.x
Install the latest version of Node.js (v20.x) via NVM:

bash
Copy
Edit
nvm install 20
nvm use 20
6. Install Composer
Install Composer, the PHP dependency manager:

bash
Copy
Edit
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
Verify the installation:

bash
Copy
Edit
composer --version
7. Configure Nginx for the Project
Now configure Nginx to serve your Laravel application.

7.1 Nginx Configuration
Create a new Nginx server block (virtual host):

bash
Copy
Edit
sudo nano /etc/nginx/sites-available/focusbackend
Paste the following configuration:

nginx
Copy
Edit
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
7.2 Enable the Site
Create a symlink to enable the site:

bash
Copy
Edit
sudo ln -s /etc/nginx/sites-available/focusbackend /etc/nginx/sites-enabled/
7.3 Test Nginx Configuration
Test the configuration for errors:

bash
Copy
Edit
sudo nginx -t
7.4 Restart Nginx
Restart Nginx to apply the changes:

bash
Copy
Edit
sudo systemctl restart nginx
8. Install NPM Dependencies
Navigate to the Laravel project directory and install the necessary npm dependencies:

bash
Copy
Edit
cd /var/www/FAMS223/focusfrontend
npm install
9. Run Laravel Migrations
Make sure the .env file is properly configured with your database credentials. Then run the migrations:

bash
Copy
Edit
cd /var/www/FAMS223/focusbackend
php artisan migrate
10. Access the Project
Your project should now be accessible at http://your_server_ip:8090. Replace your_server_ip with your actual server's IP address or domain name.

11. Troubleshooting
Nginx not starting: Check the error logs located in /var/log/nginx/ for any issues.
Permission issues: Make sure the correct file and folder permissions are set on your project directory.
Database connection issues: Verify that your MySQL server is running and that your .env file is correctly configured.
Conclusion
You’ve successfully set up the development environment for the FAMS223 project, including Nginx, PHP 8.2, MySQL, Composer, Node.js, and other essential tools. Happy coding!

markdown
Copy
Edit

---

### What This Includes:

- **System setup**: Update your system and install the required packages.
- **Nginx** setup: Configuration and setup for Nginx to serve the Laravel project.
- **PHP 8.2 & MySQL** installation and configuration: Includes binding MySQL to `0.0.0.0` to allow remote connections and setting up user privileges.
- **Composer, NVM, and Node.js** installation.
- **Laravel migration**: Run migrations and setup the application.
- **Troubleshooting** steps for common issues like permissions or Nginx errors.

You can now include this in your project’s `README.md` file for easy reference!
