sudo #!/bin/bash

# Define variables
APP_NAME="MyLiqourCabinet"
APP_PATH="/var/www/$APP_NAME"
APACHE_CONF_FILE="/etc/apache2/sites-available/$APP_NAME.conf"
WSGI_FILE="$APP_PATH/$APP_NAME.wsgi"
LOCAL_APP_PATH="/home/jonathan/git/IT490-Spring2023/FlaskFrontend"
# Create virtual environment and install dependencies


# Copy application files from local folder to server
cp -r "$LOCAL_APP_PATH"/* "$APP_PATH/"


# Done!
echo "Deployment complete!"
