<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $content = $this->twig->render('security/access_denied.html.twig', [
            'message' => 'Vous n\'avez pas la permission d\'accéder à cette page.',
            'back_url' => $request->headers->get('referer') ?? '/'
        ]);

        return new Response($content, 403);
    }
}
