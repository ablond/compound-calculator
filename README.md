# Requirements
1. Docker
2. Docker compose

# Install
Run `docker-compose run --rm php-cli composer install`

# How to use
1. Update `\App\Service\NodeCompound::process` to fit your situation
2. Run `docker-compose run --rm php-cli php src/run.php`
