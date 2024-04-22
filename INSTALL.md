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
    GRANT ALL PRIVILEGES ON DATABASE laravel TO laravel;
    \q

### Permissões

    sudo chown -R www-data:www-data /path/to/your/project/vendor
    sudo chown -R www-data:www-data /path/to/your/project/storage

    sudo usermod -a -G www-data userName

### Rodar migration ou resetar a base

    php artisan migrate:fresh

### Criar registros de teste

    php artisan db:seed --class=PersonSeeder
    php artisan db:seed --class=WorkSeeder
