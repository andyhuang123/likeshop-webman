# likeadmin-webman

A multi-client application repository with a Webman PHP backend, a Vue admin console, a Nuxt PC site, and a UniApp client.

## Projects

- server: Webman API service. Admin APIs use the /adminapi prefix; client APIs use /api.
- admin: Vue 3, TypeScript, and Vite admin console.
- pc: Nuxt 3 PC site.
- uniapp: UniApp client.

See AGENTS.md for repository development rules and each module README for commands and build behavior.

## Requirements

- Backend: PHP 8.2+, Composer, and database/Redis services configured for server.
- Frontend: Node.js and the package manager selected for the target module. The interactive UniApp development and publish scripts require Node.js 16.16.0+.
- Docker Compose configuration is under server; deployment notes are in server/README.en.md.

The admin and pc directories each contain multiple JavaScript lockfiles, and their package.json files do not declare a packageManager. The repository has no single package manager standard. Follow the team's choice and update only its lockfile. The commands below use npm to run existing scripts.

## Local development

Backend (run from the repository root):

    cd server
    composer install
    php start.php start

On Windows, run .\windows.bat from server. It invokes windows.php when the install-state file exists, or install.php otherwise.

Admin console:

    cd admin
    npm run dev
    npm run type-check
    npm run build

npm run lint runs ESLint with automatic fixes; review the working tree before running it.

PC site:

    cd pc
    npm run dev
    npm run build

npm run build generates the static site. Use npm run build:ssr for the Nuxt SSR build.

UniApp:

    cd uniapp
    npm run dev
    npm run dev:h5
    npm run dev:mp-weixin

The interactive npm run dev entry lets you choose WeChat Mini Program or H5. See uniapp/package.json for scripts targeting other platforms.

## Build outputs

Some build scripts replace release directories under server/public:

- The admin build replaces server/public/admin.
- The pc build script replaces server/public/pc.
- The UniApp H5 build replaces server/public/mobile.

Confirm that each destination may be replaced before running those builds. See the module READMEs for details.

## Deployment

The Docker Compose file is server/docker-compose.yaml; run Compose commands from server. Configure Nginx to route /adminapi, /api, and /resource to the backend and serve the admin, PC, and H5 files from their release directories. Adjust hostnames and paths for the deployment. See server/README.en.md.
