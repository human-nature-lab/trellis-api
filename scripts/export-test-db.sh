#!/bin/bash
set -e
set -x

db_file="$(dirname "$0")/../test-db.sql"
delete_file="$(dirname "$0")/delete_data.sql"
mysql="docker-compose exec -T trellis_db mysql -uroot -ptestpass"
mysqldump="docker-compose exec -T trellis_db mysqldump -uroot -ptestpass"

# $mysql test < $delete_file
$mysqldump --default-character-set=utf8mb4 --skip-set-charset test > $db_file