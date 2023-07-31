FROM node:latest

RUN apt-get update
RUN apt-get install npm -y
RUN npm i -g @vue/cli

WORKDIR /var/www/spa

CMD ["npm", "run", "dev", "--hot"]