#!/bin/bash


source .env

OPERATION=$1

if [ "$OPERATION" == "dump" ]; then
    mysqldump -u$DB_USER -p$DB_PASSWORD -h$DB_HOST $DB_NAME > $DUMP_FILE_NAME.sql
    echo "Database dumped to $DUMP_FILE_NAME.sql"
elif [ "$OPERATION" == "import" ]; then
    mysql -u$DB_USER -p$DB_PASSWORD -h$DB_HOST -e "CREATE DATABASE IF NOT EXISTS $DB_NAME"

    mysql -u$DB_USER -p$DB_PASSWORD -h$DB_HOST $DB_NAME < $DUMP_FILE_NAME.sql
    echo "Database imported to $DB_NAME"
else
    echo "Invalid operation. Please use 'dump' or 'import'."
fi