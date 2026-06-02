up:
	docker compose up -d --build

down:
	docker compose down -v

mysql-wait:
	@echo "Waiting for MySQL..."
	@until docker compose exec -T mysql mysqladmin ping -h mysql --silent; do \
		sleep 2; \
	done
	@echo "MySQL is ready!"

composer-install:
	docker compose exec app composer install

migrate:
	docker compose exec app php artisan migrate:fresh --seed

bootstrap: up mysql-wait composer-install migrate

reset: down bootstrap
