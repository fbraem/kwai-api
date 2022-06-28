#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

KWAI_DATABASE_NAME=$1
KWAI_DATABASE_USER=$2
KWAI_DATABASE_PASSWORD=$3

apt-add-repository -y ppa:ondrej/php
add-apt-repository -y ppa:ondrej/apache2
apt-get update

# Apache
apt-get install -y apache2
a2enmod rewrite

mkdir -p -v /var/www/kwai_files
chown www-data:www-data /var/www/kwai_files

# Create a configuration that is used while developing kwai-api/vite
API_CONF=$(cat <<EOF
    <VirtualHost *:80>
      ServerName api.kwai.com

      ServerAdmin webmaster@localhost
      DocumentRoot /var/www/kwai_api/api
	    Alias "/files" "/var/www/kwai_files"

      <Directory /var/www/kwai_api/api>
        Options -Indexes
        AllowOverride All
        Require all granted
        RewriteEngine On
      </Directory>

      ErrorLog \${APACHE_LOG_DIR}/kwai_api_error.log
      CustomLog \${APACHE_LOG_DIR}/kwai_api_access.log combined
    </VirtualHost>
EOF
)
echo "${API_CONF}" > /etc/apache2/sites-available/api.conf

# Create a configuration that can be used to test the deployment of kwai-api
DEPLOY_CONF=$(cat <<EOF
    <VirtualHost *:82>
      ServerName api.kwai.com

      ServerAdmin webmaster@localhost
      DocumentRoot /var/www/kwai_dev_api/api
	    Alias "/files" "/var/www/kwai_files"

      <Directory /var/www/kwai_dev_api/api>
        Options -Indexes
        AllowOverride All
        Require all granted
        RewriteEngine On
      </Directory>

      ErrorLog \${APACHE_LOG_DIR}/kwai_dev_api_error.log
      CustomLog \${APACHE_LOG_DIR}/kwai_dev_api_access.log combined
    </VirtualHost>
EOF
)
echo "${DEPLOY_CONF}" > /etc/apache2/sites-available/kwai_dev_api.conf

# MySQL
debconf-set-selections <<< "mysql-server mysql-server/root_password password $KWAI_DATABASE_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $KWAI_DATABASE_PASSWORD"
apt-get install -y mysql-server
mysql -uroot -p"$KWAI_DATABASE_PASSWORD" -e "CREATE DATABASE $KWAI_DATABASE_NAME"
mysql -uroot -p"$KWAI_DATABASE_PASSWORD" -e "grant all privileges on $KWAI_DATABASE_NAME.* to '$KWAI_DATABASE_USER'@'%' identified by '$KWAI_DATABASE_PASSWORD'"

# update mysql conf file to allow remote access to the db
sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart

# Install PHP
PHP_VERSION=8.1
apt-get install -y php${PHP_VERSION}-common php${PHP_VERSION}-cli php${PHP_VERSION}-mysql libapache2-mod-php${PHP_VERSION} php${PHP_VERSION}-intl php${PHP_VERSION}-dom php${PHP_VERSION}-gd php${PHP_VERSION}-mbstring php${PHP_VERSION}-zip php${PHP_VERSION}-sqlite php${PHP_VERSION}-xml php${PHP_VERSION}-curl

php -v
a2enmod php${PHP_VERSION}

phpenmod intl
phpenmod curl

# Install unzip for Composer
apt-get install -y unzip

# Composer
EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi
php composer-setup.php --quiet
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer

# PHPMyAdmin
PHPMYADMIN_VERSION=5.1.1
wget https://files.phpmyadmin.net/phpMyAdmin/${PHPMYADMIN_VERSION}/phpMyAdmin-${PHPMYADMIN_VERSION}-all-languages.zip -nv -O /var/tmp/phpmyadmin.zip
unzip -q /var/tmp/phpmyadmin.zip -d /var/tmp/phpmyadmin
rm -Rf /var/www/phpmyadmin
mv /var/tmp/phpmyadmin/phpMyAdmin-${PHPMYADMIN_VERSION}-all-languages /var/www/phpmyadmin
mv /var/www/phpmyadmin/config.sample.inc.php /var/www/phpmyadmin/config.inc.php
chown -R www-data:www-data /var/www/phpmyadmin
rm /var/tmp/phpmyadmin.zip
rm -r /var/tmp/phpmyadmin

PHPMYADMIN_CONF=$(cat <<EOF
    <Directory /var/www/phpmyadmin>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>
    <VirtualHost *:81>
        ServerName api.kwai.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/phpmyadmin
        ErrorLog ${APACHE_LOG_DIR}/phpmyadmin_error.log
        CustomLog ${APACHE_LOG_DIR}/phpmyadmin_access.log combined
    </VirtualHost>
EOF
)
echo "${PHPMYADMIN_CONF}" > /etc/apache2/sites-available/phpmyadmin.conf

# Document root is a symlink to /vagrant
if ! [ -L /var/www/kwai_api ]; then
  rm -rf /var/www/kwai_api
  ln -fs /vagrant/ /var/www/kwai_api
fi

# Everything installed, enable sites, restart apache
sudo a2dissite 000-default
sudo a2ensite api
sudo a2ensite kwai_dev_api
sudo a2ensite phpmyadmin
service apache2 restart

cd /vagrant
composer update
# Run the database migration
/vagrant/vendor/bin/phinx migrate -c /vagrant/src/phinx.php
