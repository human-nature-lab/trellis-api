[TOC]

------

# Trellis API

------

## Installation

**Trellis** currently requires php 5.6.  It's recommended that you run a legacy php 5.6 Homestead or Vagrant box in order to prevent introducing php 7 code which might not be compatible.  The following repositories are required:

- [https://github.com/human-nature-lab/trellis-api](https://github.com/human-nature-lab/trellis-api)
- [https://github.com/human-nature-lab/trellis-app](https://github.com/human-nature-lab/trellis-app)
- [https://github.com/human-nature-lab/trellis-vagrant](https://github.com/human-nature-lab/trellis-vagrant)

You may either use the following instructions to add **trellis-app** and **trellis-api** to Laravel Homestead (which allows running multiple projects within one virtual machine), or use the trellis-vagrant instructions above to set up a standalone Vagrant box.

- Homestead with php 5.6 instructions: [https://laravel.com/docs/5.0/homestead](https://laravel.com/docs/5.0/homestead)
- Homestead with php 7+ instructions: [https://laravel.com/docs/5.4/homestead](https://laravel.com/docs/5.4/homestead)
- Dual php 5.6 and php 7+ boxes: [https://medium.com/@mikeeeeeeey/multiple-laravel-homestead-boxes-side-by-side-487c4caeb29d](https://medium.com/@mikeeeeeeey/multiple-laravel-homestead-boxes-side-by-side-487c4caeb29d)

------

### Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)

------

### Install [Vagrant](https://www.vagrantup.com/downloads.html)

***

### Install Homestead 5
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

### (Optional) Install Homestead 7

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

### (Optional) Install the Laravel Homestead example

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

------

### Install trellis-api

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
chmod -R 775 storage
mkdir storage/framework
chmod -R 775 storage/framework
mkdir storage/framework/sessions
chmod -R 775 storage/framework/sessions
composer install
```

#### Visit trellis-api in the browser: [http://api.trellislocaldev.net/](http://api.trellislocaldev.net/)
*You should see {"msg":"Unauthorized"}*

------

### Install trellis-app

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

------

## REST API

**Trellis-api** is powered by [Lumen](https://lumen.laravel.com/) 5.1 and [MySQL](https://www.mysql.com/).  Request and response bodies are usually JSON.  The following HTTP request headers are required:

- `X-Key` (currently `rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95`) must be included by all clients
- `X-Token` a temporary token provided to the user after successful login
  - The token lifetime is set by the TOKEN_EXPIRE variable in the .env file
- `Content-Type` the request body content type (typically `application/json;charset=UTF-8`
- `Accept` the desired response body content type (typically `application/json, text/plain, */*`)

------

### (Optional) Install [Postman](https://chrome.google.com/webstore/detail/postman/fhbjgbiflinjbdggehcddcbncdddomop?hl=en)

------

### Perform HTTP Request

- #### Via Postman

  1. Enter the URL http://api.trellislocaldev.net/heartbeat

  2. Click on the Headers tab and enter the following headers:

     -  `X-Key` `rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95`

  3. Click `Send`

     *You should see a JSON response containing `[]` indicating that the API is running*

- #### Via CURL

  1. ```
     curl --request GET \
       --url http://api.trellislocaldev.net/heartbeat \
       --header 'x-key: rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95'
     ```

     *You should see a JSON response containing `[]` indicating that the API is running*

### Perform Log in

- #### Via Postman

  1. Enter the URL http://api.trellislocaldev.net/token

  2. Click on the `Headers` tab and enter the following headers:

     -  `X-Key` `rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95`
     - `Content-Type` `application/json;charset=UTF-8`
     - `Accept` `application/json, text/plain, */*`

  3. Click on the `Body` tab and enter the following text:

     -  `{"username":"admin","pass":"trellispass"}`

  4. Click `Send`

     *You should see a JSON response containing the `X-Token` for subsequent requests in the `token.hash` field*:

     ```
     {
         "user": {
             "id": "c1f277ab-e181-11e5-84c9-a45e60f0e921",
             "name": "Test Admin"
         },
         "token": {
             "hash": "{x-token}",
             "exp": "60",
             "id": "0dd6d391-0f2b-49cb-b40d-7c50bd8c040f"
         }
     }
     ```

- #### Via CURL

  1. ```
     curl --request POST \
       --url 'http://api.trellislocaldev.net/token' \
       --header 'X-Key: rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95' \
       --header 'Content-Type: application/json;charset=UTF-8' \
       --header 'Accept: application/json, text/plain, */*' \
       --data-binary '{"username":"admin","pass":"trellispass"}' \
       --silent 2>&1 | python -c "import json,sys;obj=json.load(sys.stdin);print obj['token']['hash'];"
     ```

     *You should see a 128 character response representing the `X-Token` for subsequent requests (note that Python was used in the last line to extract the `token.hash` field for convenience)*

------

### Perform Synchronization

Synchronization is performed by client apps uploading and downloading gzipped SQLite dumps to and from the server.  The `{device-id}` of the device running the app and the `{x-token}` obtained from login are required.

#### Upload

As the user works, rows on the device are inserted, updated and deleted.  All newly created table row ids should be `UUID` to prevent conflicts.  In the case of upload sync merge conflicts, the row with the newest `created_at`, `updated_at` or `deleted_at` timestamp is favored.  The old row is appended to the `log` table if it has not already been logged in a previous merge.  Any rows that were merged in a previous upload sync are ignored.

The client app should export any SQLite database rows that have been modified since the last sync as a series of SQL INSERT statements of the form:

```
INSERT INTO `table` (`field1`, `field2`, ...) VALUES ('value1', 'value2', ...);
INSERT INTO `table` (`field1`, `field2`, ...) VALUES ('value1', 'value2', ...);
...
```

Note that the server accepts most time formats such as UTC strings like `2000-12-28T23:59:59.123456Z` (the fractional portion is optional), UNIX timestamps like `1500000000`, SQLite millisecond timestamps like `1500000000123` and microsecond timestamps like `1500000000123456` for DATETIME, TIMESTAMP and other time fields.

The resulting dump should be gzipped and the raw bytes should be sent as the body of the HTTP request.  Here is a `curl` example for uploading data to the server (in this case a gzipped SQL dump named `trellis.sqlite.sql.gz`):

```
curl --request POST \
  --url http://api.trellislocaldev.net/device/{device-id}/upload \
  --header 'x-key: rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95' \
  --header 'x-token: {x-token}' \
  --data-binary '@trellis.sqlite.sql.gz'
```

*You should see a JSON response containing `[]` indicating that the API request was successful*

After the client app has performed an upload sync, it should set a flag of some kind in its local storage indicating that the data has been uploaded.  **If the user performs any changes, the flag should be cleared to indicate that another upload is necessary.**

#### Download

The client app should check the status of its local storage upload flag to ensure that the user has not made any edits since the last upload (the download sync will overwrite any of these unsynced edits).

If no edits have been made since the last upload sync, the client app should periodically request a snapshot of the server database.  The snapshot is a gzipped SQLite dump containing a series of CREATE TABLE, INSERT and other statements that allow the client database to be created from scratch.  Here is a `curl` example for downloading data from the server:

```
curl --request GET \
  --url http://api.trellislocaldev.net/device/{device-id}/download \
  --header 'x-key: rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95' \
  --header 'x-token: {x-token}' \
  --remote-name --remote-header-name
```

*You should see a newly-created file like `0000000000000001.sqlite.sql.gz` indicating that the API request was successful*

The client app should unzip and read in bytes from the dump (either to a fresh database to replace the old database, or by dumping and recreating its existing database).  Each statement is separated by `";\n"` (`\n` is the linefeed character having ASCII code 10).  Any linefeeds in the fields of the dump are guaranteed to be escaped as the literal characters `\n`, so will never be mistaken for the ";\n" semicolon linefeed sequence.

