<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LanguageController extends AbstractController
{
    #[Route('/switch-language/{locale}', name: 'switch_language')]
    public function switchLanguage(Request $request, string $locale): RedirectResponse
    {
        $request->getSession()->set('_locale', $locale);

        // Redirect to the referer URL or home if no referer
        $referer = $request->headers->get('referer') ?: $this->generateUrl('home');
        $urlParts = parse_url($referer);
        $query = isset($urlParts['query']) ? parse_str($urlParts['query'], $queryArr) : [];
        $query['_locale'] = $locale;
        $queryStr = http_build_query($query);

        return $this->redirect($urlParts['path'] . '?' . $queryStr);
    }
}
