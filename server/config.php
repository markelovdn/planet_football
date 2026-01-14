<?php

$dotenv = parse_ini_file(__DIR__ . '/../.env');

define('ADMIN_EMAIL', $dotenv['ADMIN_EMAIL']);
define('FROM_EMAIL', $dotenv['FROM_EMAIL']);
define('FROM_NAME', $dotenv['FROM_NAME']);
define('SMTP_HOST', $dotenv['SMTP_HOST']);
define('SMTP_PORT', $dotenv['SMTP_PORT']);
define('SMTP_USER', $dotenv['SMTP_USER']);
define('SMTP_PASS', $dotenv['SMTP_PASS']);
