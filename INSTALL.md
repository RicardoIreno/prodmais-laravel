# Guia de instalação passo a passo do Prodmais UNIFESP

## Linux

### Instalação do PostgreSQL

    sudo apt update
    sudo apt install postgresql postgresql-contrib

Acesse o PostgreSQL Shell

    sudo -u postgres psql

Criar a base de dados:

    CREATE DATABASE prodmais;
    CREATE USER prodmais WITH ENCRYPTED PASSWORD 'prodmais';
    GRANT ALL PRIVILEGES ON DATABASE prodmais TO prodmais;
    \q

### Instalação do PHP 8.2

    sudo apt -y install lsb-release apt-transport-https ca-certificates
    sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
    sudo apt update
    sudo apt -y install php8.2
    sudo apt-get install php8.2-{cgi,curl,mbstring,zip,xml,pgsql}

### Instalação do Apache2

    sudo apt update
    sudo apt install apache2

### Configurar o Apache

    cd /etc/apache2/sites-available
    sudo cp 000-default.conf prodmais.conf
    sudo nano prodmais.conf

Editar desta maneira:

    <VirtualHost *:80>
        ServerAdmin admin@example.com
        ServerName prodmais.example.com
        DocumentRoot /var/www/html/prodmais/public/

        <Directory /var/www/html/prodmais/public/>
                AllowOverride All
                Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>

Desabilitar o Default:

    sudo a2dissite 000-default.conf

### Clonagem do repositório do Prodmais

Você pode clonar em qualquer pasta, mas é recomendável clonar na pasta pública do apache (ex. /var/www/html):

    git clone https://github.com/trmurakami/prodmais-laravel.git prodmais

Na pasta do repositório, rodar:

    curl -s https://getcomposer.org/installer | php
    php composer.phar install --no-dev

Copiar o arquivo .env

    cp .env.example .env

Criar a chave .env

    php artisan key:generate

Editar o arquivo .env com as informações do PostgreSQL

    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=prodmais
    DB_USERNAME=prodmais
    DB_PASSWORD=prodmais

### Permissões

    sudo chown -R www-data:www-data /path/to/your/project/vendor
    sudo chown -R www-data:www-data /path/to/your/project/storage

    sudo usermod -a -G www-data userName

### Rodar migration ou resetar a base

    php artisan migrate:fresh

### Criar registros de teste

    php artisan db:seed --class=PersonSeeder
    php artisan db:seed --class=WorkSeeder
