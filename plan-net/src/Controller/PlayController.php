<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CampaignService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class PlayController extends AbstractController
{
    #[Route(
        '/{_locale}/play',
        name: 'app_play',
        requirements: [
            '_locale' => 'en|de',
        ],
        methods: ["GET"],
    )]
    public function index(
        Request $request,
        SerializerInterface $serializer,
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        } catch (AccessDeniedException $exception) {
            $this->addFlash(
                'error', sprintf('%s You must be logged in to play.', $exception->getMessage())
            );

            throw $exception;
        }

        $campaignService->play($this->getUser());

        $response = [
            'total' => 121,
            'data' => 'companies',
        ];

        $data = $serializer->serialize($response, 'json');

        return new JsonResponse(data: $data, json: true);
    }

    /**
     * -the user can call a service that tells him whether he played or not and if so then the prize will be outputted;
     *
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/redeem-prize', name: 'app_redeem_prize', methods: ["GET"])]
    public function redeemPrize(
        SerializerInterface $serializer,
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        } catch (AccessDeniedException $exception) {
            $this->addFlash(
                'error',
                sprintf('%s You must be logged in order to redeem your prize.', $exception->getMessage())
            );

            throw $exception;
        }


        $response = [
            'total' => 121,
            'data' => 'companies',
        ];

        $data = $serializer->serialize($response, 'json');


        return new JsonResponse(data: $data, json: true);
    }
}
