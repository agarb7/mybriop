#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/
BOOTSTRAP_DIR=vagrant/guest/
WRITABLE_DIRS=( 'runtime' 'web/assets' 'web/data' )

cd $APP_ROOT

# Enable russian locale
sed -i '/^# ru_RU\.UTF-8 UTF-8/s/^#\s*//' /etc/locale.gen
locale-gen

#Update apt
apt-get update

# Install Apache, Php, PostgreSQL, Git, phppgadmin
apt-get install -y apache2 postgresql postgresql-contrib php5 php5-intl php5-pgsql php5-gd git phppgadmin

# Configure Apache
## Setup rewrite module
APACHE_DIR=/etc/apache2/
ln -sf $APACHE_DIR/mods-available/rewrite.load $APACHE_DIR/mods-enabled/
## Setup site
rm -f $APACHE_DIR/sites-enabled/000-default
cp -Rf $BOOTSTRAP_DIR/apache2.conf/* $APACHE_DIR
## Restart
apache2ctl restart

# Configure Php
sed -i \
    '/^;default_charset = "UTF-8"/s/^;//;s/;mbstring\.internal_encoding = EUC-JP/mbstring.internal_encoding = UTF-8/' \
    /etc/php5/cli/php.ini /etc/php5/apache2/php.ini
##Restart Apache
apache2ctl restart

# Install Composer
## Download and install
php -r "readfile('https://getcomposer.org/installer');" | php -- --filename=composer --install-dir=/usr/local/bin
## Setup OAuth token
composer config -g github-oauth.github.com `cat $BOOTSTRAP_DIR/composer.conf/oauthtoken`
## Install asset plugin
composer global require "fxp/composer-asset-plugin:~1.1.0"

# Install vendors
composer install --dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction

# Create writable directories if not exists
mkdir -p "${WRITABLE_DIRS[@]}"

# Bootstrap DB: create (or restore from dump) and apply migrations. Previously drop if exists.
$BOOTSTRAP_DIR/resetdb.sh

cd $OLDPWD
