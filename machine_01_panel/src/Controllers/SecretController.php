<?php
declare(strict_types=1);

final class SecretController
{
    public function __construct(private Bomb $bomb)
    {
    }

    public function index(): void
    {
        if (!$this->bomb->isStarted() || $this->bomb->isDefused() || $this->bomb->isExpired()) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        // O Controller solicita a informação ao Model
        $blueWire = $this->bomb->findWire('blue');

        if ($blueWire === null) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        // O Controller envia os dados estritamente necessários para a View
        View::render('secret', [
            'title' => 'secret page',
            'styles' => [],
            'scripts' => [],
            'bodyAttributes' => '',
            'codigo_fio' => $blueWire['code']
        ]);
    }
}