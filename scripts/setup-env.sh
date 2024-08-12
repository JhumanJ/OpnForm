#!/bin/bash

set -e

# Welcome to the OpnForm environment setup script!

# Paths to the environment files
ENV_FILE=".env"
CLIENT_ENV_FILE="client/.env"

# Paths to the environment templates
ENV_EXAMPLE=".env.example"
CLIENT_ENV_EXAMPLE="client/.env.example"

# Check for the --docker flag to use Docker-specific environment settings
USE_DOCKER_ENV=false
for arg in "$@"; do
  if [ "$arg" == "--docker" ]; then
    USE_DOCKER_ENV=true
    ENV_EXAMPLE=".env.docker"
    CLIENT_ENV_EXAMPLE="client/.env.docker"
    echo "OpnForm setup detected the --docker flag. Preparing Docker-specific environment..."
    break
  fi
done

# Function to generate a random string for secrets
generate_secret() {
  LC_ALL=C tr -dc A-Za-z0-9 </dev/urandom | head -c 40 ; echo ''
}

# Function to generate a base64-encoded 32-byte string for keys
generate_base64_key() {
  openssl rand -base64 32
}

# Function to set or update an environment variable within a file
set_env_value() {
  local file=$1
  local key=$2
  local value=$3
  local delimiter="|"

  if grep -q "^$key=" "$file"; then
    # Use different sed syntax based on the operating system
    if [[ "$OSTYPE" == "darwin"* ]]; then
      # macOS uses BSD sed, which requires an argument for -i
      sed -i '' "s${delimiter}^$key=.*${delimiter}$key=$value${delimiter}" "$file"
    else
      # Linux uses GNU sed, which does not require an argument for -i
      sed -i "s${delimiter}^$key=.*${delimiter}$key=$value${delimiter}" "$file"
    fi
  else
    # Append a newline and the new key-value pair
    echo -e "\n$key=$value" >> "$file"
  fi
}

# Check if the main .env file exists
if [ -f "$ENV_FILE" ]; then
  echo "OpnForm's main .env file is already in place. No further action is needed."
else
  echo "Creating OpnForm's main .env file from the template..."
  cp "$ENV_EXAMPLE" "$ENV_FILE"

  # Secure your OpnForm instance with a unique APP_KEY
  APP_KEY=$(generate_base64_key)
  set_env_value "$ENV_FILE" "APP_KEY" "base64:$APP_KEY"

  # Generate a JWT_SECRET to sign your tokens
  JWT_SECRET=$(generate_secret)
  set_env_value "$ENV_FILE" "JWT_SECRET" "$JWT_SECRET"

  # Generate a shared secret for the client
  SHARED_SECRET=$(generate_secret)
  set_env_value "$ENV_FILE" "FRONT_API_SECRET" "$SHARED_SECRET"
fi

# Check if the client .env file exists
if [ -f "$CLIENT_ENV_FILE" ]; then
  echo "OpnForm's client .env file is already configured. Moving on..."
else
  echo "Creating OpnForm's client .env file from the template..."
  cp "$CLIENT_ENV_EXAMPLE" "$CLIENT_ENV_FILE"
  set_env_value "$CLIENT_ENV_FILE" "NUXT_API_SECRET" "$SHARED_SECRET"
fi

echo "✅ OpnForm environment setup is now complete. Enjoy building your forms!"