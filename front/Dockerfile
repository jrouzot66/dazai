FROM node:22-alpine

RUN corepack enable

WORKDIR /app

# On ne bascule pas sur l'utilisateur node tout de suite pour éviter les EACCES
# USER node

EXPOSE 5173

# On force l'installation au démarrage si node_modules est vide
CMD ["sh", "-c", "yarn install && yarn dev --host 0.0.0.0"]