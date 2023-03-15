#!/usr/bin/env bash
flask db upgrade
flask --debug run --host=0.0.0.0


# Define variables
APP_NAME="__main__"
APP_PATH="/var/www/sample/$APP_NAME"
APACHE_CONF_FILE="/etc/apache2/sites-available/$APP_NAME.conf"
WSGI_FILE="$APP_PATH/$APP_NAME.wsgi"
LOCAL_APP_PATH="/git/IT490-Spring2023/frontend/FlaskFrontend"



# Copy application files from local folder to server
cp -r "$LOCAL_APP_PATH"/* "$APP_PATH/"

# Configure Apache virtual host
echo "<VirtualHost *:80>" > "$APACHE_CONF_FILE"
echo "    ServerName example.com" >> "$APACHE_CONF_FILE" # replace with your domain name
echo "    ServerAlias www.example.com" >> "$APACHE_CONF_FILE" # replace with your domain name
echo "    WSGIScriptAlias / $WSGI_FILE" >> "$APACHE_CONF_FILE"
echo "    <Directory $APP_PATH>" >> "$APACHE_CONF_FILE"
echo "        Order allow,deny" >> "$APACHE_CONF_FILE"
echo "        Allow from all" >> "$APACHE_CONF_FILE"
echo "    </Directory>" >> "$APACHE_CONF_FILE"
echo "</VirtualHost>" >> "$APACHE_CONF_FILE"
sudo a2ensite "$APP_NAME"
sudo service apache2 restart

# Create WSGI file
echo "#!/usr/bin/env python" > "$WSGI_FILE"
echo "import sys" >> "$WSGI_FILE"
echo "import logging" >> "$WSGI_FILE"
echo "logging.basicConfig(stream=sys.stderr)" >> "$WSGI_FILE"
echo "sys.path.insert(0,\"$APP_PATH\")" >> "$WSGI_FILE"
echo "from application.app import app as application" >> "$WSGI_FILE"
echo "application.secret_key = 'your_secret_key'" >> "$WSGI_FILE" # replace with your secret key

# Set permissions
sudo chown -R www-data:www-data "$APP_PATH"
sudo chmod -R 775 "$APP_PATH"

# Done!
echo "Deployment complete!"
