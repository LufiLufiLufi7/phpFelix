#!/bin/sh
set -eu

# Configuration is generated at container startup, never committed with secrets.
umask 027
cat > /var/www/html/includes/config.php <<'PHP'
<?php
define('DB_HOST', getenv('DB_HOST') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: '');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', (int) (getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_FROM_NAME', 'Felix Construction');

define('APP_URL', rtrim(getenv('APP_URL') ?: '', '/'));
PHP

exec apache2-foreground
