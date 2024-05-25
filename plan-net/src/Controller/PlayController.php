<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlayController extends AbstractController
{
    #[Route('/play', name: 'app_play')]
    public function index(
        TranslatorInterface $translator,
        LocaleSwitcher $localeSwitcher
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return new Response();

        /*return $this->render('home.html.twig', [
            'messages' => $messages,
        ]);*/
    }
}
