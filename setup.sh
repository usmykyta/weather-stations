#!/bin/sh

if [ ! -f ./services/api/.env ]; then
  cp ./services/api/.env.example ./services/api/.env
fi

# Setting up a project
cd ./docker
docker-compose -f docker-compose.local.yml -p weather-app up -d --force-recreate --remove-orphans --build
cd ..

# Installing dependencies and restarting needed containers
docker exec weather-app-laravel composer i
docker exec weather-app-laravel php artisan key:generate
docker exec weather-app-node npm i
docker restart weather-app-vue

IP_ADDR=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' weather-app-server)
NC='\033[0m'
YELLOW='\033[1;33m'
BLUE='\033[1;34m'

echo "Please, add to Your hosts file next lines, if doesn't exists yet"

printf "\r\n"

echo "Linux or Mac OS X: /etc/hosts"
echo "Windows: C:\Windows\System32\drivers\etc\hosts"

printf "\r\n"
echo -e ${BLUE}$IP_ADDR${NC} ${YELLOW}weather.local${NC}
echo -e ${BLUE}$IP_ADDR${NC} ${YELLOW}api.weather.local${NC}