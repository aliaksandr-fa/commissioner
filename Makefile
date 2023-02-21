test:
	docker-compose run --rm commissioner-php-cli php ./vendor/bin/phpunit

run:
	docker-compose run --rm commissioner-php-cli php public/app.php public/data/input.txt

init:
	docker-compose build
