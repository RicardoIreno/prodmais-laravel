## Prodmais - Laravel

### Permissões

    sudo chown -R www-data:www-data /path/to/your/project/vendor
    sudo chown -R www-data:www-data /path/to/your/project/storage

    sudo usermod -a -G www-data userName

### Rodar migration

    php artisan migrate:fresh
