# sudo rsync -avr /home/bitnami/staging/changed-files/ /opt/bitnami/apache/htdocs/

# # Navigate to the Laravel application directory
# cd /opt/bitnami/apache/htdocs

# # Install composer dependencies
# #echo "Installing Composer dependencies..."
# #composer install --no-dev --optimize-autoloader

# # Run migrations (optional, only if you want to run migrations after each deployment)
# #echo "Running migrations..."
# #php artisan migrate --force

# # Clear the Laravel cache (optional, if you want to refresh the cache after each deploy)
# echo "Clearing Laravel cache..."
# sudo php artisan optimize:clear
# sudo php artisan config:clear
# sudo php artisan cache:clear
# sudo php artisan route:clear
# sudo php artisan view:clear
# sudo chown -R daemon:daemon /opt/bitnami/apache/htdocs/storage
# sudo chmod -R 777 /opt/bitnami/apache/htdocs/storage

# echo "After install tasks completed."

#!/bin/bash
set -euo pipefail

STAGING=/opt/bitnami/apache/staging
HTDOCS=/opt/bitnami/apache/htdocs
BACKUP=/opt/bitnami/apache/htdocs_backup
SHARED=/opt/bitnami/apache/shared

log() {
	echo "[$(date -u +%Y-%m-%dT%H:%M:%SZ)] $*"
}

php_bin() {
	if [ -x /opt/bitnami/php/bin/php ]; then
		echo "/opt/bitnami/php/bin/php"
		return
	fi
	command -v php
}

composer_cmd() {
	if command -v composer >/dev/null 2>&1; then
		echo "composer"
		return
	fi
	if [ -x /opt/bitnami/php/bin/composer ]; then
		echo "/opt/bitnami/php/bin/composer"
		return
	fi
	if [ -x /opt/bitnami/php/bin/php ] && [ -f /opt/bitnami/composer/bin/composer ]; then
		echo "/opt/bitnami/php/bin/php /opt/bitnami/composer/bin/composer"
		return
	fi
	return 1
}

run_composer() {
	local cmd
	cmd="$(composer_cmd)"
	# shellcheck disable=SC2086
	$cmd "$@"
}

run_artisan() {
	local php
	php="$(php_bin)"
	(cd "$HTDOCS" && "$php" artisan "$@")
}

owner_group() {
	if id -u bitnami >/dev/null 2>&1 && getent group daemon >/dev/null 2>&1; then
		echo "bitnami:daemon"
		return
	fi
	echo "root:root"
}

ensure_shared() {
	mkdir -p "$SHARED"
	# Do not create subdirectories here; keep shared storage empty enough
	# for migrate_from_backup_once() to detect and copy an existing storage/.
	mkdir -p "$SHARED/storage"
}

ensure_storage_layout() {
	# Laravel expects these directories to exist; otherwise config/view.php can
	# evaluate realpath(storage/framework/views) to false and artisan will error.
	mkdir -p "$SHARED/storage/app/public"
	mkdir -p "$SHARED/storage/framework/cache/data"
	mkdir -p "$SHARED/storage/framework/sessions"
	mkdir -p "$SHARED/storage/framework/views"
	mkdir -p "$SHARED/storage/logs"
}

ensure_public_storage_link() {
	# Avoid relying on artisan for the symlink creation (artisan may need vendor/ + cache dirs).
	rm -rf "$HTDOCS/public/storage"
	ln -sfn "../storage/app/public" "$HTDOCS/public/storage"
}

prepare_runtime_dirs_and_permissions() {
	local owner
	owner="$(owner_group)"

	ensure_storage_layout
	mkdir -p "$HTDOCS/bootstrap/cache"

	# Permissions must be in place before any artisan commands run.
	chown -R "$owner" "$SHARED/storage" || true
	chown -R "$owner" "$HTDOCS/bootstrap/cache" || true

	chmod -R 775 "$SHARED/storage" || true
	chmod -R 775 "$HTDOCS/bootstrap/cache" || true
}

migrate_from_backup_once() {
	if [ ! -d "$BACKUP" ]; then
		return
	fi

	if [ ! -f "$SHARED/.env" ] && [ -f "$BACKUP/.env" ]; then
		log "Migrating .env from previous release into shared"
		cp -f "$BACKUP/.env" "$SHARED/.env"
	fi

	if [ -d "$BACKUP/storage" ]; then
		if [ -d "$SHARED/storage" ] && [ -z "$(ls -A "$SHARED/storage" 2>/dev/null || true)" ]; then
			log "Migrating storage/ from previous release into shared"
			cp -a "$BACKUP/storage/." "$SHARED/storage/"
		fi
	fi
}

restore_env_or_fail() {
	if [ -f "$SHARED/.env" ]; then
		cp -f "$SHARED/.env" "$HTDOCS/.env"
		return
	fi

	log "ERROR: Missing $SHARED/.env and could not migrate from previous release."
	log "Create it once (copy from an existing server .env), then redeploy."
	exit 1
}

ensure_storage_symlink() {
	rm -rf "$HTDOCS/storage"
	ln -sfn "$SHARED/storage" "$HTDOCS/storage"
	ensure_public_storage_link
}

ensure_app_key_persisted() {
	# If APP_KEY is missing, generate once and persist back into shared/.env
	if ! grep -qE '^APP_KEY=base64:' "$HTDOCS/.env" 2>/dev/null; then
		log "APP_KEY missing; generating and persisting to shared/.env"
		run_artisan key:generate --force
		cp -f "$HTDOCS/.env" "$SHARED/.env"
	fi
}

install_backend_deps() {
	if [ -f "$HTDOCS/composer.json" ]; then
		log "Installing PHP dependencies (composer)"
		(cd "$HTDOCS" && run_composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist)
	fi
}

build_frontend_assets_if_possible() {
	if [ -f "$HTDOCS/package.json" ] && command -v npm >/dev/null 2>&1; then
		log "Installing Node dependencies and building assets (Laravel Mix)"
		(cd "$HTDOCS" && {
			if [ -f package-lock.json ]; then
				npm ci --no-audit --no-fund || npm install --no-audit --no-fund
			else
				npm install --no-audit --no-fund
			fi
			npm run production
		})
	else
		log "Skipping asset build (npm not available or package.json missing)"
	fi
}

clear_caches() {
	log "Clearing Laravel caches"
	run_artisan optimize:clear
}

fix_permissions() {
	local owner
	owner="$(owner_group)"

	log "Setting permissions for runtime directories"
	mkdir -p "$HTDOCS/bootstrap/cache"
	mkdir -p "$SHARED/storage"

	chown -R "$owner" "$SHARED/storage" || true
	chown -R "$owner" "$HTDOCS/bootstrap/cache" || true

	chmod -R 775 "$SHARED/storage" || true
	chmod -R 775 "$HTDOCS/bootstrap/cache" || true
}

log "Preparing shared directories"
ensure_shared

if [ -d "$HTDOCS" ]; then
	log "Backing up current htdocs"
	rm -rf "$BACKUP"
	mv "$HTDOCS" "$BACKUP"
else
	log "No existing htdocs found (first deploy)"
	rm -rf "$BACKUP"
fi

log "Attempting one-time migration of .env/storage into shared (if needed)"
migrate_from_backup_once

log "Promoting staging to htdocs"
mv "$STAGING" "$HTDOCS"

log "Restoring environment"
restore_env_or_fail

install_backend_deps

log "Preparing runtime directories and permissions"
prepare_runtime_dirs_and_permissions

log "Linking shared storage and public storage"
ensure_storage_symlink

ensure_app_key_persisted

build_frontend_assets_if_possible

clear_caches
fix_permissions

log "AfterInstall complete"