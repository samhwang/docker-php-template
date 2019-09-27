# My Docker PHP Project Template

## Building the project image

```bash
git clone git@github.com:samhwang/docker-php-template.git
cd docker-php-template

# Either run the Docker build command
docker build -f .docker/Dockerfile -t project_name .

# Or run the docker-compose build command
docker-compose build
```

You can also add build arguments for environment such as
`staging` and `production`, or you can edit the arguments in
the `docker-compose.yml` file.

```bash
docker build -f .docker/Dockerfile -t samhwang/php:latest \
    --build-arg ENVIRONMENT=[development,staging,production] \
    --build-arg XDEBUG_ENABLE=[true,false] \
    .
```

By default in development environment, upon buildin the image,
it will check if there already exists a pair of SSL key files
and certificate. If not, it will generate one. But if you want
to generate your own, you can run:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout .docker/apache/ssl/server.key -out .docker/apache2/ssl/server.crt
```

It also integrates mailhog for
development environment so it does not spam people emails
excessively.

## Composing the network

To run the image and connect it with the rest of the network
(including Composer, MySQL and Adminer), you will need to
create your own .env file. A sample is provided as .env.sample.
And then you can run these commands:

```bash
# Add -d to run detached
# Add --build to rebuild the image
docker-compose [-d --build] up

# These two ports are set to bind to port 40 and 443 in the docker-compose.yml file.
curl http://localhost:8000
curl https://localhost:8001
```
