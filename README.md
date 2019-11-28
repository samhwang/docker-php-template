# My Docker PHP Project Template

![GitHub](https://img.shields.io/github/license/samhwang/docker-php-template?style=for-the-badge)

## Requirements

- PHP >= 7.4
- Composer
- Docker

## Usage in future projects

- Clone the project.
- Find and replace all of the `docker-php-template`, `project_name` with
  your desired project name.
- Remove the `composer.lock` entry from `.gitignore` file.
- Remove the whole `.git` folder, and reinitialize the project.
- Copy the `.env.sample` file into `.env` and put in your db credentials.
- In `composer.json`, change the Project type from `library` to `project`,
  and change the version number to exact numbers.
- Install composer dependencies
- Build the local docker image (see instructions below).

```bash
git clone git@github.com:samhwang/docker-php-template.git [YOUR_PROJECT_DIRECTORY]
cd [YOUR_PROJECT_DIRECTORY]

rm -rf .git
git init .
cp .env.sample .env
composer install
```

## Building the development image

```bash
git clone git@github.com:samhwang/docker-php-template.git [YOUR_PROJECT_DIRECTORY]
cd [YOUR_PROJECT_DIRECTORY]
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
