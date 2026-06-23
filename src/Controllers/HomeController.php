<?php
declare(strict_types=1);

final class HomeController
{
    public function index(): void
    {
        View::render('home', [
            'title' => 'Bomb Defusal CTF',
            'styles' => ['assets/css/home.css'],
            'scripts' => [],
            'bodyAttributes' => '',
        ]);
    }
}
