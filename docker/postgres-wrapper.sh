#!/bin/bash

DATA_DIR=/persist/pgsql/data
CONFIG_FILE=/etc/postgresql/postgresql.conf
PG_BASE=/usr/lib/postgresql/15/

touch $CONFIG_FILE

mkdir -p $DATA_DIR
chown postgres -R $DATA_DIR
chmod 0700 $DATA_DIR

. /app/.env

test -f $DATA_DIR/postgresql.conf || NEW_DB=true

if [ "x$NEW_DB" != "x" ]; then
    echo "No database files found.  Initialising blank database"
    sudo -u postgres $PG_BASE/bin/initdb -D $DATA_DIR
fi
sudo -u postgres $PG_BASE/bin/postgres -D $DATA_DIR -c config_file=$CONFIG_FILE &

wait_for_database_to_be_ready() {
    while ! (echo "select version()" | psql -U $DB_USERNAME); do
        echo "Waiting 5 seconds for the database to come up"
        sleep 5;
    done
}

if [ "x$NEW_DB" != "x" ]; then
    echo "Creating database users"
    wait_for_database_to_be_ready
    psql -U postgres <<EOF
CREATE ROLE $DB_USERNAME LOGIN PASSWORD '$DB_PASSWORD';
CREATE DATABASE $DB_DATABASE;
\c $DB_DATABASE;
GRANT ALL ON DATABASE $DB_DATABASE TO $DB_USERNAME;
GRANT ALL ON SCHEMA public TO $DB_USERNAME;
EOF

fi

wait_for_database_to_be_ready
artisan migrate

wait
