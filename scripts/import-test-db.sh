#!/bin/bash
set -e
set -x

db_file="$(dirname "$0")/../test-db.sql"
mysql="docker-compose exec -T trellis_db mysql -uroot -ptestpass"
echo "Importing database from $db_file"
$mysql -e "drop database if exists test; CREATE DATABASE test CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
$mysql test < "$db_file"
$mysql -e "create user if not exists 'testuser'@'%' identified by 'testpass';"
$mysql -e "grant all privileges on test.* to 'testuser'@'%';flush privileges;"
$mysql test -e "show tables;"

