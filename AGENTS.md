# AGENTS.md

## Cursor Cloud specific instructions

This repo is a Docker Compose based PHP monorepo (a "before/after" payment app).
Services (see `docker-compose.yml`, commands in `Makefile`):

| Service | Purpose | Port / usage |
|---|---|---|
| `db` | MySQL 8.0, shared schema; auto-seeded from `docker/mysql/init.sql` on first start | host `3307` -> `3306` |
| `legacy` | Before app (PHP 7.4 + mysqli) | http://localhost:8080/login.php |
| `modern` | After app (PHP 8.3 + PDO), the primary product | http://localhost:8081/login.php |
| `cli` | Task runner for Composer / PHPUnit / PHPStan / Rector (not a web server) | `docker compose run --rm cli ...` |

Test users (seeded, password `password123`): `test@example.com` (user), `admin@example.com` (admin).

### Startup (non-obvious)

- The update script only refreshes host `vendor/` via `composer install`; it does NOT start anything. You must start services yourself.
- The Docker daemon is not running on a fresh VM. Start it once in the background before any `docker`/`make` command: `sudo dockerd` (the `ubuntu` user is already in the `docker` group, so no `sudo` is needed for `docker`/`docker compose` afterwards). If you hit a socket permission error, run `sudo chmod 666 /var/run/docker.sock`.
- Bring the stack up with `make up` (build + `docker compose up -d`). `make setup` additionally runs `composer install` inside the `cli` container, which is redundant once the host `vendor/` exists.
- `vendor/` lives on the host and is bind-mounted into both `modern` and `cli`. It must exist before `modern` can serve pages (its front controller autoloads from the mounted vendor). If you change Composer deps, re-run `composer install` on the host so the mount updates.

### Test / lint (all require `db` healthy; run via `cli`)

- Tests: `make test` (PHPUnit characterization tests, `modern/tests`)
- Static analysis: `make phpstan` (PHPStan L6 on `modern/`)
- Refactor check: `make rector` (Rector dry-run)

### Notes

- Re-seed the DB from scratch with `docker compose down -v` then `make up` (drops the `db_data` volume).
- The PSR-4 autoloader is generated at the repo root and resolves `App\` relative to the parent of `vendor/`. In the `modern` container `vendor/` is mounted at `/var/www/vendor`, so the Dockerfile creates `/var/www/modern -> /var/www/html` so autoloading resolves; do not remove that symlink.
