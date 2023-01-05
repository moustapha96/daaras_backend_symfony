# API

The API will be here.

Refer to the [Getting Started Guide](https://api-platform.com/docs/distribution) for more information.

Pour demarer l'application: symfony serve

les configuration de la base de données dans le fichier .env

remplacer par le votre configuration pour comminuquer avec la base de données local

#run server send mail

# Run mailHog

docker pull mailhog/mailhog

docker run -d --name mailhog -p 1025:1025 -p 8025:8025 mailhog/mailhog
