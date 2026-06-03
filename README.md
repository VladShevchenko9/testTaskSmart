# Ticket Support Portal (Laravel + Docker)

Simple support system with:
- customer ticket creation via embeddable iframe widget
- admin login
- admin ticket list with filters, status updates, statistics, and ticket details modal

## Requirements

- Docker + Docker Compose
- `make`

Install `make` on Ubuntu:

```bash
sudo apt install make
```

## Project Structure

- Root infra: `docker-compose.yml`, `Dockerfile`, `nginx.conf`, `Makefile`
- Laravel app: `src/`

## Quick Start

From project root:

```bash
sudo make boot
```

`make boot` does all required steps:
- starts containers
- waits for MySQL
- installs Composer dependencies
- installs npm dependencies
- builds frontend assets
- runs fresh migrations + seed
- creates `storage` symlink (only if missing)
- clears Laravel caches
- checks symlink health

Open in browser:

- App: [http://localhost:8000](http://localhost:8000)
- phpMyAdmin: [http://localhost:8080](http://localhost:8080)

Stop and remove containers/volumes:

```bash
sudo make down
```

Reset full environment:

```bash
sudo make reset
```

## Test Data (Seeder)

After `make boot`, seeded users:

- Admin
  - Email: `admin@example.com`
  - Password: `password`
- Customer
  - Email: `customer@example.com`
  - Password: `password`

Also seeded:
- roles (`admin`, `customer`)
- sample tickets
- sample ticket attachments

## Main Pages

- Home: `GET /`
- Admin login: `GET /admin/login`
- Admin tickets page: `GET /admin/tickets-page`
- Widget page (iframe-ready): `GET /widget`
- Swagger UI: `GET /swagger/index.html`

## Widget Embed (iframe)

Use this on any page:

```html
<iframe
  src="http://localhost:8000/widget"
  title="Support Ticket Widget"
  style="width: 100%; min-height: 680px; border: 0;"
></iframe>
```

## API Endpoints

### Public API

- `POST /api/tickets` — create ticket (supports attachments)

Example `curl` (without files):

```bash
curl -X POST "http://localhost:8000/api/tickets" \
  -H "Accept: application/json" \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "subject=Payment issue" \
  -F "message=I have a payment problem"
```

Example with files:

```bash
curl -X POST "http://localhost:8000/api/tickets" \
  -H "Accept: application/json" \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "subject=Attachment test" \
  -F "message=Please check attached files" \
  -F "attachments[]=@/absolute/path/to/file1.png" \
  -F "attachments[]=@/absolute/path/to/file2.pdf"
```

### Admin API (requires authenticated admin session)

- `GET /admin/api/tickets/statistics` — ticket stats for:
  - `day`
  - `week`
  - `month`
- `GET /admin/api/tickets` — list with pagination + filters
- `GET /admin/api/tickets/{ticket}` — ticket details
- `PATCH /admin/api/tickets/{ticket}` — update status

Supported list filters:
- `id`
- `customer_name`
- `customer_email`
- `subject`
- `status`
- `created_at` (date)
- `per_page`
- `page`

Example list request:

```bash
GET /admin/api/tickets?status=new&customer_email=example.com&per_page=25&page=1
```

Example update payload:

```json
{
  "status": "in_progress"
}
```

Allowed statuses:
- `new`
- `in_progress`
- `processed`

## Swagger Documentation

- OpenAPI spec file: `src/public/swagger/openapi.yaml`
- Swagger UI page: `http://localhost:8000/swagger/index.html`

The docs include:
- public ticket creation endpoint
- admin statistics endpoint
- admin list/details/update ticket endpoints
- schemas for ticket payloads and attachments

## Notes

- Attachment links are served from `/storage/...`
- App URL is configured as `http://localhost:8000`
- Frontend assets are built via Vite into `src/public/build`
