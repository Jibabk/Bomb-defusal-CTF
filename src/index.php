<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Core/View.php';
require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/Models/ChallengeTimer.php';
require_once __DIR__ . '/Models/Bomb.php';
require_once __DIR__ . '/Models/GreenWireLogin.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/BombController.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'start':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(404);
            echo 'Page not found.';
            break;
        }

        $bomb = new Bomb();
        $bomb->start();
        header('Location: index.php?route=bomb', true, 303);
        break;

    case 'bomb':
        (new BombController(new Bomb()))->show();
        break;

    case 'timer':
        (new BombController(new Bomb()))->timer();
        break;

    case 'wire':
        (new BombController(new Bomb()))->wire((string) ($_GET['id'] ?? ''));
        break;

    case 'hermanos':
        (new BombController(new Bomb()))->secret();
        break;

    case 'defuse_code':
        (new BombController(new Bomb()))->defuseCode();
        break;

    default:
        http_response_code(404);
        echo 'Page not found.';
        break;
}
