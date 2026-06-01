#!/bin/sh
set -e

echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."
until php -r '
$host = getenv("DB_HOST") ?: "mysql";
$port = (int) (getenv("DB_PORT") ?: 3306);
$db = getenv("DB_DATABASE") ?: "";
$user = getenv("DB_USERNAME") ?: "";
$pass = getenv("DB_PASSWORD") ?: "";
try {
    new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
    exit(0);
} catch (Throwable $e) {
    exit(1);
}
'; do
    sleep 2
done

composer install

if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

exec "$@"
