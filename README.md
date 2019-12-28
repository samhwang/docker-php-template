# My Docker PHP Project Template

![GitHub](https://img.shields.io/github/license/samhwang/docker-php-template?style=for-the-badge)

Basically a boilerplate to setup a PHP7 project with common tools such as Composer and PHPUnit and
a Docker container environment setup. From that point, it can be a project on its own or running
as a backend service in a microservices configuration.

## Requirements

- PHP >= 7.4
- Composer >= 1.9
- Docker & docker-compose

## Usage in future projects

- Clone the project.
- Run the composer setup command.

```bash
git clone git@github.com:samhwang/docker-php-template.git [YOUR_PROJECT_DIRECTORY]
cd [YOUR_PROJECT_DIRECTORY]
composer run setup
```

## Building the development image

```bash
docker-compose build
```

Upon building the image, it will check if there
already exists a pair of SSL key files and certificate. If not,
it will generate one. To generate your own pair of self-signed
SSL keys, you can run:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout .docker/ssl/server.key -out .docker/ssl/server.crt
```

### Building the production image

```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up --remove-orphans
```

## Composing the network

To run the image and connect it with the rest of the network
(including Database, Mailhog and Adminer), you will need to
create your own .env file. A sample is provided as .env.sample.
And then you can run these commands:

```bash
# Add -d to run detached
# Add --build to rebuild the image
docker-compose [-d --build] up

# These two ports are set to bind to port 40 and 443 in the docker-compose.yml file.
curl http://localhost:80
curl https://localhost:443
```
