<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;

class PageController
{
    public function __construct(protected Engine $view) {}

    public function about(Request $request): Response
    {
        $html = $this->view->render('pages.about');
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function privacy(Request $request): Response
    {
        $html = $this->view->render('pages.privacy');
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function terms(Request $request): Response
    {
        $html = $this->view->render('pages.terms');
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function license(Request $request): Response
    {
        $html = $this->view->render('pages.license');
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
