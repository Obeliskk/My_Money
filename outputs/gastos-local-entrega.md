# GastosLocal — entrega inicial

El proyecto Laravel está en la carpeta raíz de este espacio de trabajo.

Para ejecutarlo en Windows:

    composer install
    Copy-Item .env.example .env
    New-Item -ItemType File -Force database/database.sqlite
    php artisan key:generate
    php artisan migrate
    php artisan serve

Después abre http://127.0.0.1:8000.

La guía completa está en README.md.
