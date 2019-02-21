#!/usr/bin/env bash


echo "Update packages"
cp -r /vagrant/provision/linuxConfiguration/etc/apt/* /etc/apt
apt-get update -q
apt-get upgrade -y

echo "Installing packages"
apt install sudo wget curl git-core vim elinks apache2  php-common \
        libapache2-mod-php  php-cli php-curl php-intl php-zip php-ldap \
        php-gd php-mcrypt php-mysql php-xml php-json php-mbstring php-soap \
        php-bcmath php-ldap rabbitmq-server libevent-dev supervisor  \
        php-bcmath -y
rabbitmq-plugins enable rabbitmq_management rabbitmq_tracing  rabbitmq_federation
cp /vagrant/linuxConfiguration/etc/rabbitmq/rabbitmq.config /etc/rabbitmq/rabbitmq.config
service rabbitmq-server restart
rabbitmqctl add_vhost /dev/
rabbitmqctl set_permissions -p /dev/ guest ".*" ".*" ".*"

if [ ! -f  /usr/local/bin/composer ]; then
    echo "Get composer"
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin
    mv /usr/bin/composer.phar /usr/bin/composer
    chmod a+x /usr/bin/composer
fi
if ! [ -L /var/www ]; then
    echo "Check apache2"
    cp -r /vagrant/provision/linuxConfiguration/etc/apache2/* /etc/apache2
    a2enmod rewrite
    service apache2 restart
fi
if [ ! -f /usr/sbin/mysqld ]; then
    echo "Installing packages - mysql"
    DEBIAN_FRONTEND=noninteractive apt-get  -y install mysql-server
    mysqladmin -u root password root
fi
if ls -c1 /var/lib/mysql | grep igroove; then
     echo "Database exists nothing to do"
else
     echo "Create database"
     mysqladmin -u root -proot create igroove
fi
if [ ! -f /vagrant/vendor ]; then
    echo "Get vendors via composer"
    cd /vagrant
    composer install 
fi
if [ ! `pecl list | grep libevent|cut -d" " -f1` ]; then
    echo "PECL libevent"
    pecl install libevent channel://pecl.php.net/libevent-0.1.0
fi

if [ ! -f  /etc/supervisor/conf.d/ldap_service.conf ]; then
    mkdir /var/log/supervisor
    cp -r /vagrant/provision/linuxConfiguration/etc/supervisor/conf.d/* /etc/supervisor/conf.d/
    service supervisor restart
    service supervisor status
    ln -s /vagrant /var/www/igroove
fi

if [ ! -f  /etc/ldap/certs/server.test.network.cer ]; then
    mkdir -p /etc/ldap/certs/
    cp /vagrant/provision/linuxConfiguration/etc/ldap/ldap.conf /etc/ldap/ldap.conf
    cp /vagrant/provision/linuxConfiguration/etc/ldap/certs/server.test.network.cer /etc/ldap/certs/server.test.network.cer
fi

if [ ! -f  /vagrant/src/Zen/IgrooveBundle/Resources/config/version/versionAvailable.yml ]; then
    cp /vagrant/src/Zen/IgrooveBundle/Resources/config/version/versionAvailable.yml.dist /vagrant/src/Zen/IgrooveBundle/Resources/config/version/versionAvailable.yml
    cp /vagrant/src/Zen/IgrooveBundle/Resources/config/version/versionInstalled.yml.dist /vagrant/src/Zen/IgrooveBundle/Resources/config/version/versionInstalled.yml
fi