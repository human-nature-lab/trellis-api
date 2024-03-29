#!/bin/sh

if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
fi

docker-compose exec trellis_db mysql -u$DB_USERNAME -p$DB_PASSWORD -h$DB_HOST $DB_DATABASE "$@"