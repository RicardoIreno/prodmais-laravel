## Prodmais - Laravel

### Permissões

    sudo chown -R www-data:www-data /path/to/your/project/vendor
    sudo chown -R www-data:www-data /path/to/your/project/storage

    sudo usermod -a -G www-data userName

### Rodar migration

    php artisan migrate:fresh

### Criar registros de teste

    php artisan db:seed --class=PersonSeeder
    php artisan db:seed --class=WorkSeeder
