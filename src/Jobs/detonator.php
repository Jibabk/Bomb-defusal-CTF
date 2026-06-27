<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/ChallengeTimer.php';

set_time_limit(0);

$databasePath = getenv('BOMB_DB_PATH') ?: __DIR__ . '/../storage/bomb.sqlite';
$timer = new ChallengeTimer(Database::connection());
$timer->initialize();
seedSecretDirectory();

foreach (array_merge([dirname($databasePath)], glob($databasePath . '*') ?: []) as $path) {
    @chown($path, 'www-data');
    @chgrp($path, 'www-data');
    @chmod($path, is_dir($path) ? 0775 : 0664);
}

$shouldDetonate = false;

while (true) {
    $timerStatus = $timer->snapshot();

    if ($timerStatus['is_challenge_defused']) {
        break;
    }

    if ($timerStatus['is_time_expired']) {
        $shouldDetonate = true;
        break;
    }

    if ($timerStatus['is_challenge_started'] && !$timerStatus['is_time_expired']) {
        if ($timerStatus['remaining_seconds'] > 0) {
            $timer->decrementOneSecond();
        } else {
            $timer->markExpired();
            $shouldDetonate = true;
            break;
        }
    }
    sleep(1);
}

if ($shouldDetonate) {
    sleep(5);
    exec('/usr/local/bin/detonate');
}

function seedSecretDirectory(): void
{
    if (!is_dir('/root/secret_seed') || !is_dir('/root/secret')) {
        return;
    }

    exec('find ' . escapeshellarg('/root/secret') . ' -mindepth 1 -maxdepth 1 -exec rm -rf {} +');
    exec('cp -a ' . escapeshellarg('/root/secret_seed/.') . ' ' . escapeshellarg('/root/secret'));
    file_put_contents('/root/secret/.seeded', date(DATE_ATOM) . "\n");
}
