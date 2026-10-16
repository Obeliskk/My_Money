# GastosLocal

Mini aplicación local para registrar ingresos y gastos manualmente y visualizar el ahorro mensual.

## Requisitos

- PHP 8.2 o superior con las extensiones `pdo_sqlite`, `mbstring`, `openssl` y `fileinfo`.
- Composer.

## Instalación en Windows

Desde esta carpeta, ejecuta:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File -Force database/database.sqlite
php artisan key:generate
php artisan migrate
php artisan serve
```

Luego abre http://127.0.0.1:8000.

Con el entorno ya instalado, puedes iniciar la aplicación en adelante con:

```powershell
.\iniciar.ps1
```

La base de datos queda en `database/database.sqlite`. Para hacer un respaldo, copia ese archivo junto con el proyecto.

## Alcance actual

- Registro de ingresos y gastos con fecha, monto, categoría y descripción.
- Filtro por mes.
- KPI de ingresos, gastos, saldo disponible y cantidad de movimientos.
- Meta de ahorro mensual con porcentaje de cumplimiento.
- Distribución de gastos por categoría.
- Etiquetas predeterminadas y etiquetas personalizadas, incluyendo Regalos.
- Registro de deudas y cobros con persona, monto, vencimiento, dirección y estado de pago.
- Al marcar una deuda como pagada o un cobro como cobrado, se crea automáticamente el gasto o ingreso correspondiente en el historial; al reabrirlo, ese movimiento se retira para evitar duplicados.
- Modo noche con preferencia guardada en el navegador.
- Navegación por módulos: resumen semanal, nuevo movimiento (con administración de etiquetas), deudas y cobros, meta e historial mensual.
- Selector de KPI del resumen para alternar entre semana y mes.
- Edición de movimientos, deudas y etiquetas.
- Eliminación de movimientos.
