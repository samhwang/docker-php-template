# My Docker PHP Project Template

![GitHub](https://img.shields.io/github/license/samhwang/docker-php-template?style=for-the-badge)

## Requirements

- PHP >= 7.3
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
- Build the local docker image (see instructions below).

```bash
git clone git@github.com:samhwang/docker-php-template.git [YOUR_PROJECT_DIRECTORY]
cd [YOUR_PROJECT_DIRECTORY]

rm -rf .git
git init .
cp .env.sample .env
```

## Building the project image

```bash
git clone git@github.com:samhwang/docker-php-template.git [YOUR_PROJECT_DIRECTORY]
cd [YOUR_PROJECT_DIRECTORY]

# Either run the Docker build command
docker build -f .docker/Dockerfile -t [YOUR_PROJECT_NAME] .

# Or run the docker-compose build command
docker-compose build
```

You can also add build arguments for environment such as
`staging` and `production`, or you can edit the arguments in
the `docker-compose.yml` file.

```bash
docker build -f .docker/Dockerfile -t [YOUR_PROJECT_NAME]:latest \
    --build-arg ENVIRONMENT=[development,staging,production] \
    --build-arg XDEBUG_ENABLE=[true,false] \
    .
```

By default upon building the image, it will check if there
already exists a pair of SSL key files and certificate. If not,
it will generate one. To generate your own pair of self-signed
SSL keys, you can run:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout .docker/ssl/server.key -out .docker/ssl/server.crt
```

It also integrates mailhog for development environment so it
does not spam people emails excessively.

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
