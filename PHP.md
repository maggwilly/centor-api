https://gist.github.com/hawkapparel/92dfbe2d68eee003e3e1c22391bf97cb

Install PHP 5.6 On linux (Debian, Ubuntu, Mint ...)

sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php5.6

heck Installed PHP Version

php -v

 
Install PHP 5.6 Modules

Show available PHP Modules list
sudo apt-cache search php5.6-* 
Install modules which is required for you.
Example PHP 5.6:
```sudo apt-get install libapache2-mod-php5.6 php5.6-cgi php5.6-cli php5.6-curl php5.6-imap php5.6-gd php5.6-mysql php5.6-pgsql php5.6-sqlite3 php5.6-mbstring php5.6-json php5.6-bz2 php5.6-mcrypt php5.6-xmlrpc php5.6-gmp php5.6-xsl php5.6-soap php5.6-xml php5.6-zip php5.6-dba```

Example PHP 7:
```sudo apt-get install php7.0-bcmath libapache2-mod-php7.0 php7.0-common php7.0-pgsql  php7.0-curl php7.0-json php7.0-cgi php7.0-gd```


force composer 
```composer install --ignore-platform-reqs```