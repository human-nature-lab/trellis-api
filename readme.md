Installation
===

* [Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)](#install-virtualboxhttpswwwvirtualboxorgwikidownloads)
* [Install [Vagrant](https://www.vagrantup.com/downloads.html)](#install-vagranthttpswwwvagrantupcomdownloadshtml)
* [Install Homestead 5](#install-homestead-5)
* [(Optional) Install Homestead 7](#optional-install-homestead-7)
* [(Optional) Install the Laravel Homestead example](#optional-install-the-laravel-homestead-example)
* [Install trellis-api](#install-trellis-api)
* [Install trellis-app](#install-trellis-app)

**Trellis** currently requires php 5.6.  It's recommended that you run a legacy php 5.6 Homestead or Vagrant box in order to prevent introducing php 7 code which might not be compatible.  The following repositories are required:

* [https://github.com/human-nature-lab/trellis-api](https://github.com/human-nature-lab/trellis-api)
* [https://github.com/human-nature-lab/trellis-app](https://github.com/human-nature-lab/trellis-app)
* [https://github.com/human-nature-lab/trellis-vagrant](https://github.com/human-nature-lab/trellis-vagrant)

You may either use the following instructions to add **trellis-app** and **trellis-api** to Laravel Homestead (which allows running multiple projects within one virtual machine), or use the trellis-vagrant instructions above to set up a standalone Vagrant box.

* Homestead with php 5.6 instructions: [https://laravel.com/docs/5.0/homestead](https://laravel.com/docs/5.0/homestead)
* Homestead with php 7+ instructions: [https://laravel.com/docs/5.4/homestead](https://laravel.com/docs/5.4/homestead)
* Dual php 5.6 and php 7+ boxes: [https://medium.com/@mikeeeeeeey/multiple-laravel-homestead-boxes-side-by-side-487c4caeb29d](https://medium.com/@mikeeeeeeey/multiple-laravel-homestead-boxes-side-by-side-487c4caeb29d)

## Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)

## Install [Vagrant](https://www.vagrantup.com/downloads.html)

***

## Install Homestead 5
```
cd ~
git clone -b 2.0 https://github.com/laravel/homestead.git Homestead-5
cd Homestead-5
bash init.sh
```

#### (Optional) Create a symlink from ~/.homestead/Homestead.yaml to ~/Homestead-5/Homestead.yaml for convenience
```
ln -s ~/.homestead/Homestead.yaml ~/Homestead-5/Homestead.yaml
```

#### Start Homestead
```
cd Homestead-5
vagrant up --provision
```
*Enter password if requested, to run NFS for faster networking*

#### Log in to Homestead
```
cd Homestead-5
vagrant ssh
```

#### Verify php version
```
php -v
```
*PHP 5.6.15-1+deb.sury.org~trusty+1 (cli)*

#### Stop Homestead
```
exit
vagrant halt
```

***

## (Optional) Install Homestead 7

```
cd ~
git clone https://github.com/laravel/homestead.git Homestead-7
cd Homestead-7
bash init.sh
```

#### (Optional) Create a symlink so that Homestead-5 uses the same Homestead.yaml as Homestead-7

*Newer Homestead no longer has the hidden .homestead directory.*

```
mv ~/.homestead/Homestead.yaml ~/.Trash
ln -s ~/Homestead-7/Homestead.yaml ~/.homestead/Homestead.yaml
mv ~/Homestead-5/Homestead.yaml ~/.Trash
ln -s ~/Homestead-7/Homestead.yaml ~/Homestead-5/Homestead.yaml
```

#### Start Homestead (enter password if requested, to run NFS for faster networking)
```
cd Homestead-7
vagrant up --provision
```

#### Log in to Homestead
```
cd Homestead-7
vagrant ssh
```

#### Verify php version
```
php -v
```
*PHP 7.1.3-3+deb.sury.org~xenial+1 (cli) (built: Mar 25 2017 14:00:03) ( NTS )*

#### Stop Homestead
```
exit
vagrant halt
```

***

## (Optional) Install the Laravel Homestead example

#### Verify that the Homestead example is listed in Homestead.yaml
```
cat ~/.homestead/Homestead.yaml
```

*or for php 7:*

```
cat ~/Homestead-7/Homestead.yaml
```

*you should see:*

```
sites:
    - map: homestead.app
      to: /home/vagrant/Code/Laravel/public
```

#### Add the Homestead example to your hosts file
```
sudo nano /etc/hosts
192.168.10.10  homestead.app
```
*Press control-x and press y to save file and exit nano*

#### Provision Homestead
Run one of the following for Homestead with php 5.6 or Homestead with php 7:

```
cd Homestead-5
```

*or:*

```
cd Homestead-7
```

*then:*

```
vagrant halt
vagrant up --provision
```

#### Log in to Homestead
```
vagrant ssh
```

#### Install the Laravel Homestead example within Homestead
```
cd Code
git clone https://github.com/laravel/laravel.git
cd laravel
cp .env.example .env
composer install
php artisan key:generate
```
*Note that ~/Code on the host machine corresponds to /home/vagrant/Code inside the Homestead client*

#### Visit the Laravel Homestead example in the browser: [http://homestead.app](http://homestead.app)
*You should see "Laravel" in large letters*

## Install trellis-api

#### Add trellis-api to Homestead.yaml

```
sites:
    - map: api.trellislocaldev.net
      to: /home/vagrant/Code/trellis-api/public
```

#### Add trellis-api to your hosts file
```
sudo nano /etc/hosts
192.168.10.10   api.trellislocaldev.net
```
*Press control-x and press y to save file and exit nano*

#### Provision Homestead
```
cd Homestead-5
vagrant halt
vagrant up --provision
```

#### Open the ~/Code/trellis-api directory in your local editor (Atom, PHPStorm, etc)

Create a file called `.env` in ~/Code/trellis-api with the following and save it:

```
APP_ENV=dev
APP_KEY=R645f6hxomGkDBJez0nM4uB8Zhl6BDSm
APP_DEBUG=true
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=trellis
DB_USERNAME=homestead
DB_PASSWORD=secret
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=database
TOKEN_EXPIRE=60
```

#### Download initial database seed data

Save [https://github.com/human-nature-lab/trellis-vagrant/blob/master/trellis2.sql](https://github.com/human-nature-lab/trellis-vagrant/blob/master/trellis2.sql) to ~/Code/trellis-api/trellis2.sql

#### Create database within Homestead and seed it
```
mysql -hlocalhost -uhomestead -psecret -e "drop database trellis;"
mysql -hlocalhost -uhomestead -psecret -e "create database trellis;"
mysql -hlocalhost -uhomestead -psecret trellis < trellis2.sql
```

#### Set "admin" user password to "trellispass" (without quotes), to allow login at [http://trellislocaldev.net/#/login](http://trellislocaldev.net/#/login)
```
CRYPT_PASSWORD=$(php -r'echo(password_hash("trellispass",CRYPT_BLOWFISH));')
mysql -hlocalhost -uhomestead -psecret -e'update user set password="'"$CRYPT_PASSWORD"'" where 1;' trellis
```

#### Install trellis-api

**Trellis-api** is served from within Homestead.

See [https://github.com/human-nature-lab/trellis-vagrant/blob/master/bootstrap.sh](https://github.com/human-nature-lab/trellis-vagrant/blob/master/bootstrap.sh) for more configuration details.

```
cd ~/Code
git clone https://github.com/human-nature-lab/trellis-api.git
cd Homestead-5
vagrant ssh
cd Code/trellis-api
mkdir storage/framework
chmod -R 775 storage/framework
mkdir storage/framework/sessions
chmod -R 775 storage/framework/sessions
composer install
```

#### Visit trellis-api in the browser: [http://api.trellislocaldev.net/](http://api.trellislocaldev.net/)
*You should see {"msg":"Unauthorized"}*

## Install trellis-app

#### Add trellis-app to Homestead.yaml

```
sites:
    - map: trellislocaldev.net
      to: /home/vagrant/Code/trellis-app/compiled
```

#### Add trellis-app to your hosts file
```
sudo nano /etc/hosts
192.168.10.10   trellislocaldev.net
```
*Press control-x and press y to save file and exit nano*

#### Provision Homestead
```
cd Homestead-5
vagrant halt
vagrant up --provision
```

#### Download app config

Save [https://github.com/human-nature-lab/trellis-vagrant/blob/master/trellis-app-config.js](https://github.com/human-nature-lab/trellis-vagrant/blob/master/trellis-app-config.js) to ~/Code/trellis-app/application/config.js

#### Install trellis-app

**Trellis-app** is served from within Homestead but can be built from either the host machine or within the Homestead client.  The following tools should be installed on the host, within the client, or both:

* [NPM](https://www.npmjs.com/get-npm)
* [Bower](https://bower.io/#install-bower)
* [Gulp](http://gulpjs.com/)

See [https://github.com/human-nature-lab/trellis-vagrant/blob/master/bootstrap.sh](https://github.com/human-nature-lab/trellis-vagrant/blob/master/bootstrap.sh) for more configuration details.

```
cd ~/Code
git clone https://github.com/human-nature-lab/trellis-app.git
cd trellis-app
npm install
bower install
gulp
```
#### Visit trellis-api in the browser: [http://trellislocaldev.net/](http://trellislocaldev.net/)
*You should see the Trellis login screen*

Enter:

`admin`

`trellispass`

Press the `Login` button to log into Trellis.
