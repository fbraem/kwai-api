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

VIRTUAL_HOST=$(cat <<EOF
    <VirtualHost *:80>
      ServerName api.kwai.com

      ServerAdmin webmaster@localhost
      DocumentRoot /var/www/kwai_api
	    Alias "/files" "/var/www/kwai_files"

      <Directory /var/www/kwai_api>
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
echo "${VIRTUAL_HOST}" > /etc/apache2/sites-enabled/000-default.conf

# MySQL
debconf-set-selections <<< "mysql-server mysql-server/root_password password $KWAI_DATABASE_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $KWAI_DATABASE_PASSWORD"
apt-get install -y mysql-server
mysql -uroot -p$KWAI_DATABASE_PASSWORD -e "CREATE DATABASE $KWAI_DATABASE_NAME"
mysql -uroot -p$KWAI_DATABASE_PASSWORD -e "grant all privileges on $KWAI_DATABASE_NAME.* to '$KWAI_DATABASE_USER'@'%' identified by '$KWAI_DATABASE_PASSWORD'"

# update mysql conf file to allow remote access to the db
sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart

# Install PHP
apt-get install -y php8.0-common php8.0-cli php8.0-mysql libapache2-mod-php8.0 php8.0-intl php8.0-dom php8.0-gd php8.0-mbstring php8.0-zip php8.0-sqlite php8.0-xml

php -v
a2enmod php8.0

phpenmod intl

# Install unzip for Composer
apt-get install -y unzip

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c31c1e292ad7be5f49291169c0ac8f683499edddcfd4e42232982d0fd193004208a58ff6f353fde0012d35fdd72bc394') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer

# PHPMyAdmin
add-apt-repository -y ppa:phpmyadmin/ppa
apt-get update
apt-get upgrade
debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install no"
apt-get -yq install phpmyadmin

# Document root is a symlink to /vagrant
if ! [ -L /var/www/kwai_api ]; then
  rm -rf /var/www/kwai_api
  ln -fs /vagrant/ /var/www/kwai_api
fi

# Everything installed, restart apache
service apache2 restart

# Run the database migration
/vagrant/src/vendor/bin/phinx migrate -c /vagrant/src/phinx.php
