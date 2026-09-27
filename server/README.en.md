# server: Backend API

The Webman backend lives in this directory. It requires PHP 8.2+. Use composer.json and composer.lock for dependency requirements. Configure the database, Redis, and listen address using the local .env and config files.

## Install and run

Install dependencies:

    composer install

Run in the foreground for development:

    php start.php start

Run in the background for production:

    php start.php start -d

Check status, stop, or restart:

    php start.php status
    php start.php stop
    php start.php restart

On Windows, run .\windows.bat. It checks config/install.lock: when present, it runs windows.php; otherwise it runs install.php.

## Backend layout

- app/adminapi: admin APIs organized under controller, logic, validate, lists, and related directories.
- app/api: client APIs.
- app/common: shared models, services, enums, and validation.
- config/route.php: explicit route definitions.
- public: static files served by Webman.

Admin APIs use the /adminapi prefix; client APIs use /api. When adding an endpoint, check the route, controller, validation, business logic, permissions, and client API definition.

## Docker Compose

The Compose file is docker-compose.yaml in this directory. From server, run:

    docker compose up -d
    docker compose ps
    docker compose logs -f server
    docker compose down

Before starting, confirm the environment variables, ports, and persistence settings match the target environment.

## Deployment routes

- /adminapi/ and /api/: reverse proxy to Webman.
- /resource/: configure according to the project's static resource layout.
- /admin: serve the admin files from server/public/admin and configure an index.html fallback for client-side routes.
- /pc: serve the PC files from server/public/pc and configure an index.html fallback for client-side routes.
- UniApp H5 build output is in server/public/mobile; configure its public path for the deployment.

The admin, pc, and UniApp H5 release scripts remove their target directories before copying new files. Confirm those directories can be replaced before deployment. Configure Nginx host, port, static paths, and cache policy for the deployment environment; do not use placeholder values as-is.
