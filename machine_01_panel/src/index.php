<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Core/View.php';
require_once __DIR__ . '/Models/Bomb.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/BombController.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'bomb':
        (new BombController(new Bomb()))->show();
        break;

    default:
        http_response_code(404);
        echo 'Página não encontrada.';
        break;
}
