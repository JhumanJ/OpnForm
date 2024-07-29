#!/bin/bash -e

main() {
    generate_api_secrets
}

generate_api_secrets() {
	if ! is_configured; then
        echo "Generating shared secret..."
		SECRET="$(random_string)"
		add_secret_to_env_file /secrets/client.env NUXT_API_SECRET "$SECRET"
		add_secret_to_env_file /secrets/api.env FRONT_API_SECRET "$SECRET"
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

	grep -q "^$VAR=" "$FILE" 2>/dev/null || ( echo "$VAR=" >> "$FILE" )

	cp $FILE $TEMP_FILE
	sed "s/^$VAR=.*$/$VAR=$VAL/" -i $TEMP_FILE
	cat $TEMP_FILE > $FILE
}

is_configured() {
    grep -q "FRONT_API_SECRET=.\+" .env 2>/dev/null
}

main
