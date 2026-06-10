<?php
declare(strict_types=1);

final class BombController
{
    public function __construct(private Bomb $bomb)
    {
    }

    public function show(): void
    {
        $remaining = $this->bomb->getRemainingSeconds();

        View::render('bomb', [
            'title' => 'Bomb Defusal',
            'styles' => ['assets/css/bomb.css'],
            'scripts' => ['assets/js/timer.js', 'assets/js/wires.js'],
            'bodyAttributes' => 'data-remaining="' . $remaining . '"',
            'remaining' => $remaining,
        ]);
    }
}
