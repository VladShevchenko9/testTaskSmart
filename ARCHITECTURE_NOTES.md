# Architecture Notes

This file is a short record of technical decisions made during implementation.

## Overview

The app uses a simple layered structure on top of Laravel MVC:

- HTTP layer: routes, controllers, form requests, API resources
- application layer: services
- persistence layer: repositories
- UI: Blade + Bootstrap + small page-level JS modules

Main flows:

- public ticket creation (`/widget` + `POST /api/tickets`)
- admin ticket management (`/admin/tickets-page` + `/admin/api/*`)

## Why service + repository

For this project we intentionally avoided putting business logic in controllers.

- controllers coordinate requests/responses
- services contain use-case logic
- repositories contain query/data access logic

This adds a bit more structure than a minimal Laravel app, but keeps the codebase easier to test and refactor.

## Repository base class

`AbstractEloquentRepository` contains common CRUD helpers:

- `getAll`
- `find`
- `findBy`
- `create`
- `update`
- `load`
- `delete`

Concrete repositories only keep domain-specific methods (for example ticket filters or statistics).

## API route split

Ticket API endpoints use explicit prefixes:

- public: `/api/tickets`
- admin-only: `/admin/api/tickets/*`

This keeps page routes and API routes clearly separated and simplifies frontend integrations.

## Ticket statistics

Statistics endpoint:

- `GET /admin/api/tickets/statistics`

Returns counts for:

- last day
- last week
- last month

Implementation uses:

- Carbon for time windows
- Eloquent scopes on `Ticket` (`createdFrom`, `createdBetween`)

## Auth & roles

Auth is standard Laravel session auth.
Authorization is role-based with Spatie Permission.

Notes:

- admin routes are protected with `auth` + `role:admin`
- middleware aliases are registered in `bootstrap/app.php`
- `/login` exists as a compatibility alias and redirects to `/admin/login`

## Attachments

Attachments are stored with Spatie Media Library.

Why:

- stable API for file handling
- simple attachment retrieval
- easy extension later (conversions, alternative disks)

Important operational details:

- files are served via `/storage` symlink
- `FILESYSTEM_DISK=public`
- `APP_URL` must include `:8000` in local docker setup
- upload limits are set in both PHP and nginx config

## Frontend approach

UI is server-rendered with Blade + Bootstrap.
Interactive behavior uses axios and small page modules imported by one Vite entry (`resources/js/app.js`).

Reason:

- fewer moving parts
- no SPA complexity
- easy to keep widget/admin scripts isolated

SweetAlert2 is used for user-facing success/error notifications.

## Testing

Current baseline coverage is feature-level (PHPUnit):

- create ticket (including attachment)
- admin list/show/update ticket
- admin statistics
- guest access restrictions on admin API

This gives quick regression coverage for critical flows without introducing heavy test infrastructure.

## Developer ergonomics

The root `Makefile` is the main entry point (`make boot`).

It performs:

- container startup
- dependency install (composer + npm)
- frontend build
- migrations + seed
- storage link setup/check
- Laravel cache clear

This keeps setup consistent across environments.

## Follow-ups

If the project grows, next practical steps would be:

- move API routes into `routes/api.php`
- add request throttling for public ticket creation
- add unit tests for services with mocked repository contracts
- add queue-backed processing if attachment workflows become heavy

