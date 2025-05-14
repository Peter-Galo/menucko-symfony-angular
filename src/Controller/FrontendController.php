<?php

// src/Controller/FrontendController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController
{
    #[Route('/', name: 'frontend_index')]
    public function index(): Response
    {
        return new Response(file_get_contents(__DIR__.'/../../public/index.html'));
    }

    #[Route('/{route}', name: 'frontend_spa', requirements: [
        'route' => '^(?!api|_wdt|_profiler|browser|build|assets|favicon\.ico|.*\.(js|css|ico|png|jpg|svg|map|json)$).*'
    ])]
    public function spa(): Response
    {
        return new Response(file_get_contents(__DIR__.'/../../public/index.html'));
    }
}