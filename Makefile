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

npm-install:
	docker compose exec app npm install

npm-build:
	docker compose exec app npm run build
	docker compose exec app rm -f public/hot

migrate:
	docker compose exec app php artisan migrate:fresh --seed

storage-link:
	@docker compose exec -T app test -L public/storage && echo "storage link already exists" || docker compose exec app php artisan storage:link

optimize-clear:
	docker compose exec app php artisan optimize:clear

check-storage-link:
	@docker compose exec -T app test -L public/storage && echo "storage link is OK" || (echo "storage link is missing"; exit 1)

boot: up mysql-wait composer-install npm-install npm-build migrate storage-link optimize-clear check-storage-link

reset: down boot
