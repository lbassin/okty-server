up:
	docker-compose up -d

behat: up
	docker-compose exec php ./vendor/bin/behat --strict
