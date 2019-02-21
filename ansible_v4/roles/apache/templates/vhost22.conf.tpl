# Default Apache virtualhost template

<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /vagrant/web

    <Directory /vagrant/web>
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
