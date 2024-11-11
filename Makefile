up:
	docker compose up -d

down:
	docker compose down

migrate:
	docker compose exec app php artisan migrate

rollback:
	docker compose exec app php artisan migrate:rollback

seed:
	docker compose exec app php artisan db:seed

test:
	php artisan test
