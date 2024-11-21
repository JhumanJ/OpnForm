#!/bin/bash

# Generate .env file for Coolify deployment
cat <<EOL > .env
DB_HOST=db
REDIS_HOST=redis
DB_DATABASE=${DB_DATABASE:-forge}
DB_USERNAME=${DB_USERNAME:-forge}
DB_PASSWORD=${DB_PASSWORD:-forge}
DB_CONNECTION=${DB_CONNECTION:-pgsql}
EOL

echo ".env file generated successfully for Coolify."