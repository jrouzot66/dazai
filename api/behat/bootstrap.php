<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

// Charge .env / .env.test si pas déjà défini par le process (Docker, CI, etc.)
if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test';
}

if (class_exists(Dotenv::class)) {
    $projectDir = dirname(__DIR__);

    // Charge .env, puis .env.test (et .env.test.local si présent)
    (new Dotenv())->bootEnv($projectDir . '/.env');
}