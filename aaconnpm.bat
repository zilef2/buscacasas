@echo on
set destination=C:\laragon\www\buscacasas\buscacasas.zip
del %destination%
cd C:\laragon\www\buscacasas
del /F /S hot
npm run build && "C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\buscacasas\vendor" -x!"C:\laragon\www\buscacasas\storage" -x!"C:\laragon\www\buscacasas\node_modules" -x!"C:\laragon\www\buscacasas\public\hot.*"  %destination% "C:\laragon\www\buscacasas\app" "C:\laragon\www\buscacasas\resources" "C:\laragon\www\buscacasas\routes" "C:\laragon\www\buscacasas\database" "C:\laragon\www\buscacasas\public" "C:\laragon\www\buscacasas\lang"
pause
