# Trellis API
The Trellis API survey is built on an API optimized version of Laravel called Lumen.

------

# Trellis Server

------

## Prerequisites

Installing Trellis Server requires root access to a Linux server. At the time of writing these instructions I'm using a
t2.medium Amazon EC2 server initialized with a Ubuntu Server 18.04 LTS AMI. The steps for setting up Trellis API on 
other distributions of Linux should be similar. The steps for launching an EC2 server is out of the scope of this 
documentation.

### Domain and sub-domain

You will need to register a domain name and point both `yourdomainname.com` and `api.yourdomainname.com` to the IP
address of your server (optionally also `www.yourdomainname.com`).

------

## MySQL

We'll be using the MySQL database to store the Trellis data, other relational databases such as Postgres, Oracle, and 
Amazon RDS can also be used.

### Install MySQL

```
$ sudo apt install mysql-server
```

### Secure the MySQL installation

```
$ sudo mysql_secure_installation
```

Follow the step-by-step instructions to set the password for root, remove anonymous users, disallow root login remotely,
remove the test database, and reload the privilege tables.

### Create the trellis database

```
$ sudo mysql
mysql> create database trellis CHARACTER SET utf8 COLLATE utf8_general_ci;
mysql> create user 'trellis'@'localhost' identified by '[enter your desired password here]';
mysql> grant all privileges on trellis.* to 'trellis'@'localhost';
mysql> flush privileges;
mysql> exit
```

*Note: in MySQL 5.7+ the **root** user is authenticated automatically using the logged in user's credentials. If you get
an error when starting mysql in without credentials, try `mysql -u root -p` and enter the password provided in the 
previous step.*

------

## PHP

### Install PHP

```
$ sudo apt install php-fpm php-mysql php-gmp php-mbstring php-dom php-cli
$ sudo apt install php-dev php-xml php-zip php-json php-curl php-pear
```

------

## Zip

### Install Zip

```
$ sudo apt install zip unzip
```

------

## Trellis API

### Change the permissions of the www directory
```
$ sudo chown -R "$USER":www-data /var/www
$ sudo chmod -R 755 /var/www
```

### Clone the trellis API repository
```
$ cd /var/www
$ git clone https://github.com/human-nature-lab/trellis-api.git trellis-api
```
*Note: provide your github Username and Password if prompted.*
*Note: if Git has not already been installed on your server, run `sudo apt install git` to install git.*

### Install Composer

```
$ cd /var/www/trellis-api 
$ curl https://getcomposer.org/installer | php 
$ php composer.phar install
```

### Configure Trellis
```
$ cd /var/www/trellis-api
$ cp .env.example .env 
$ nano .env
```
Modify the `DB_USERNAME` and `DB_PASSWORD` lines to match the database user you created above then press CTRL-X and Y to
save and exit.

### Change the permissions of the storage directory
```
$ sudo chown -R "$USER":www-data /var/www
$ sudo chmod -R 755 /var/www
```

### Run the database migrations
```
$ cd /var/www/trellis-api
$ php artisan migrate --seed
```
Enter and confirm a password for the admin user when prompted.

### Setup the scheduler
Add the following line to crontab:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Trellis App

### Create the trellis-app directory and change its permissions
```
$ sudo mkdir /var/www/trellis-app
$ sudo chown "$USER":www-data /var/www/trellis-app
$ sudo chmod 775 /var/www/trellis-app
```

### Download the latest Trellis web app
```
$ cd /var/www/trellis-api
$ php artisan trellis:download-app
```
Select the desired version of the app from the menu and to download. 

Specify the directory you created in the previous step (e.g. `var/www/trellis-app`) when prompted.

------

## Nginx

We'll be using the Nginx web server to serve Trellis, other web servers such as Apache should work but the instructions
will be different.

### Install Nginx

```
$ sudo apt update
$ sudo apt install nginx
```

Now if you navigate to `yourdomainname.com` you should see a **Welcome to nginx!** message. 

*Note: if you can't access your server, make sure ports 80 and 443 are open in your server's firewall and check if Linux
has additional firewall software, such as `ufw`, that needs to be configured.*

### Configuring Nginx
Trellis uses two server blocks to configure REST API server and the web server.

Create an nginx configuration file for each server and copy the contents of the each into these files. Replace the `{yourdomainname.com}` with your domain name in the code below.
- `sudo touch /etc/nginx/sites-available/trellis-app.conf`
- `sudo touch /etc/nginx/sites-available/trellis-api.conf`
- `sudo touch /etc/nginx/sites-available/http-to-https.conf`

Web Server - trellis-app.conf
```
# Redirect all www requests to www-less
server {
  listen 443 ssl http2;
  server_name {yourdomainname.com};

  root        /var/www/trellis-app;
  access_log  /var/log/nginx/app-access.log;
  error_log   /var/log/nginx/app-error.log;

  charset        utf-8;
  source_charset utf-8;

  # Block requests for all . directories/files in general (including .htaccess, etc)
  location ~ /\. {
    deny all;
  }
}
```

API Server - trellis-api.conf
```
server {
  listen 443 ssl http2;
  server_name api.{yourdomainname.com};

  root        /var/www/trellis-api/public;
  access_log  /var/log/nginx/api-access.log;
  error_log   /var/log/nginx/api-error.log;

  charset        utf-8;
  source_charset utf-8;

  location / {
    index     index.html index.htm index.php;
    try_files $uri $uri/ /index.php$is_args$args;
  }
  location ~ \.php$ {
      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      rewrite ^(?!/index\.php)(.*)\.php(.*)$ $1$2 permanent;
      fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
      fastcgi_index index.php;
      include fastcgi_params;
      fastcgi_split_path_info ^(.+\.php)(/.+)$;
    }

  # Block requests for all . directories/files in general (including .htaccess, etc)
  location ~ /\. {
    deny all;
  }
}
```

Http to Https - http-to-https.conf
```
server {

  listen 80 default_server;
  return 301 https://$host$request_uri;
  server_name _;
  root /var/www/html;
  access_log /var/log/nginx/http_to_https-access.log;
  error_log /var/log/nginx/http_to_https-error.log debug;
  ancient_browser Links Lynx netscape4;
  if ($ancient_browser) { rewrite ^ /unsupported.html break; }
  modern_browser_value "modern.";
  modern_browser msie 8.0;
  modern_browser gecko 1.0.0;
  modern_browser opera 9.0;
  modern_browser safari 413;
  modern_browser konqueror 3.0;
  large_client_header_buffers 4 32k;

}
```

Enable the servers that were just created using the following commands. After running these commands you will no longer see a response from your server.
- `sudo rm /etc/nginx/sites-enabled/default`
- `sudo ln -s /etc/nginx/sites-available/trellis-app.conf /etc/nginx/sites-enabled`
- `sudo ln -s /etc/nginx/sites-available/trellis-api.conf /etc/nginx/sites-enabled`
- `sudo ln -s /etc/nginx/sites-available/http-to-https.conf /etc/nginx/sites-enabled`
- To reload the nginx configuration use `sudo nginx -s reload`

## LetsEncrypt
LetsEncrypt is an free, easy and automated tool for configuring and renewing a TLS Certificate for your server. We recommend using [certbot](https://certbot.eff.org/) to configure your TLS certificates.
Follow along with the nginx documentation for your OS. LetsEncrypt can automatically make changes to your nginx configuration so it is recommended to configure nginx first. Be sure to select the option to redirect to HTTPS.

## Development

### Debugging

#### Command line
Use the `-dxdebug.remote_autostart` option from the command line to start the debugger.

Ex.

    php7.1 -dxdebug.remote_autostart trellis:make:reports {study_id}

### Profiling
From the command line you can start the profiling using the `-dxdebug.profiler_enable` option. 

Ex

    php7.1 -dxdebug.profiler_enable -dxdebug.extended_info=0 {study_id
       
The `-dxdebug.extended_info=0` command limits the amount of data written to the profile dump and reduces the performance overhead caused by profiling.