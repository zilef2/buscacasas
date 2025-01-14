@echo on
set "folder=C:\laragon\www\buscacasas"

cd %folder%
start /b php artisan serve
start /b npm run dev
