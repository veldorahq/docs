<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;

class ExtensionController
{
    public function __construct(protected Engine $view) {}

    public function index(Request $request): Response
    {
        $html = $this->view->render('pages.extension', [
            'version'     => '0.5.3',
            'downloads'   => '1,200+',
            'marketplace' => 'https://marketplace.visualstudio.com/items?itemName=veldora.veldora-vscode',
            'github'      => 'https://github.com/veldorahq/veldora-vscode',
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
