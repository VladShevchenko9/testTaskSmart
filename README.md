Run with Docker

- Install make: `sudo apt install make`
- Create root `.env` (or copy from `.env.example`)
- Start containers: `sudo make boot`
- Create symlink for storage: `docker compose exec app php artisan storage:link` (once)
- Stop and remove containers + volumes: `sudo make down`
- Open the app in browser: `http://localhost:8000/`
