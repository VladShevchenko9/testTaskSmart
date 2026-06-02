Run with Docker

- Create root `.env` (or copy from `.env.example`)
- Build containers: `docker compose build`
- Start containers: `docker compose up -d`
- Create symlink for storage: `docker compose exec app php artisan storage:link`
- Stop and remove containers + volumes: `docker compose down -v`
- Open the app in browser

