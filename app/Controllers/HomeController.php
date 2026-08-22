<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;
use App\Helpers\DocsParser;

class HomeController
{
    public function __construct(protected Engine $view) {}

    public function index(Request $request): Response
    {
        $parser = new DocsParser();
        $nav    = $parser->getNav();

        $html = $this->view->render('pages.home', [
            'nav'          => $nav,
            'sectionCount' => count(array_filter($nav, fn($n) => $n['level'] === 2)),
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}