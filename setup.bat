@echo off

IF NOT EXIST .\services\api\.env (
  copy .\services\api\.env.example .\services\api\.env
)

REM Setting up a project
cd .\docker
docker-compose -f docker-compose.local.yml -p weather-app up -d --force-recreate --remove-orphans --build
cd ..

REM Installing dependencies and restarting needed containers
docker exec weather-app-laravel composer i
docker exec weather-app-laravel php artisan key:generate
docker exec weather-app-node npm i
docker restart weather-app-vue

FOR /F "usebackq tokens=*" %%i IN (`docker inspect -f "{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}" weather-app-server`) DO SET "IP_ADDR=%%i"

echo Please, add to Your hosts file next lines, if doesn't exist yet

echo.

echo Linux or Mac OS X: /etc/hosts
echo Windows: C:\Windows\System32\drivers\etc\hosts

echo.

echo %IP_ADDR% weather.local
echo %IP_ADDR% api.weather.local

