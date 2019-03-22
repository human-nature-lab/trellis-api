- [Trellis Server](#trellis-server)
  * [Prerequisites](#prerequisites)
    + [Domains and sub-domains](#domains-and-sub-domains)
  * [Installing Nginx](#prerequisites)
  * [Installation](#installation)
    + [Install trellis-api](#install-trellis-api)
      - [Add trellis-api to Homestead.yaml](#add-trellis-api-to-homesteadyaml)
      - [Add trellis-api to your hosts file](#add-trellis-api-to-your-hosts-file)
      - [Provision Homestead](#provision-homestead-1)
      - [Open the ~/Code/trellis-api directory in your local editor (Atom, PHPStorm, etc)](#open-the-codetrellis-api-directory-in-your-local-editor-atom-phpstorm-etc)
      - [Create database within Homestead](#create-database-within-homestead)
      - [Install trellis-api](#install-trellis-api-1)
      - [Visit trellis-api in the browser: http://api.trellislocaldev.net/](#visit-trellis-api-in-the-browser-httpapitrellislocaldevnet)
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
$ sudo apt install php-fpm php-mysql
$ sudo apt install php-gmp
$ sudo apt install php-mbstring
$ sudo apt install php-dom
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



