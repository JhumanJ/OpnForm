#!/bin/bash

main() {
    ( flock -n 100 || wait_for_other_instance; generate_api_secrets) 100> /var/lock/api_secret.lock
}

generate_api_secrets() {
	if ! is_configured; then
		SECRET=$(random_string)
		add_secret_to_env_file /app/client/.env NUXT_API_SECRET $SECRET
		add_secret_to_env_file /app/.env FRONT_API_SECRET $SECRET
	fi
}

random_string() {
	array=()
	for i in {a..z} {A..Z} {0..9}; 
	   do
	   array[$RANDOM]=$i
	done
	printf %s ${array[@]::8} $'\n'
}

add_secret_to_env_file() {
	FILE=$1
	TEMP_FILE=/tmp/env.$$
	VAR=$2
	VAL=$3

	grep "^$VAR=" $FILE || ( echo $VAR= >> $FILE )

	cp $FILE $TEMP_FILE
	sed "s/^$VAR=.*$/$VAR=$VAL/" -i $TEMP_FILE
	cat $TEMP_FILE > $FILE
}

wait_for_other_instance() {
    while ! is_configured; do
        sleep 1;
    done
}

is_configured() {
    grep -q "FRONT_API_SECRET=.\+" /app/.env
}

main
