# Recipe Sharing Portal

A PHP and MySQL recipe sharing app. The included `schema.sql` creates the database, tables, and starter recipes.

## Run locally with XAMPP

1. Copy this folder into XAMPP's `htdocs` directory.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Import `schema.sql` using phpMyAdmin.
4. Open `http://localhost/recipe-portal/`.

The local database defaults are `localhost`, database `recipe_portal`, user `root`, and an empty password. Hosted environments can set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` instead. See `.env.example` for the variable names; do not commit real credentials.

## Deploy to Vercel

`Dockerfile.vercel` packages PHP with FrankenPHP for Vercel's container runtime. A hosted MySQL server is still required: set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in the Vercel project's environment variables, then import `schema.sql` into that database. The schema also creates the `app_sessions` table used to share login sessions across instances.

Vercel container instances are stateless, so the app stores PHP sessions in the configured MySQL database rather than on local disk.

See [Deploy PHP on Vercel with Docker](https://vercel.com/kb/guide/deploy-php-on-vercel-with-docker) for the current deployment flow.
