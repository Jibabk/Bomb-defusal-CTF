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

$timer = new ChallengeTimer(Database::connection());
$timer->initialize();

while (true) {
    $timerStatus = $timer->snapshot();

    if ($timerStatus['is_challenge_started'] && !$timerStatus['is_time_expired']) {
        if ($timerStatus['remaining_seconds'] > 0) {
            $timer->decrementOneSecond();
        } else {
            $timer->markExpired();
        }
    }
    sleep(1);
}
