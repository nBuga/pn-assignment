<?php

namespace App\Controller;

use App\Exception\CampaignNotValidException;
use App\Service\CampaignService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PlayController extends AbstractController
{
    /**
     * @throws CampaignNotValidException
     */
    #[Route('/{_locale}/play', name: 'app_play', requirements: ['_locale' => 'en|de'], methods: ["GET"])]
    public function index(
        Request $request,
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        } catch (AccessDeniedException $exception) {
            return $this->getResponseAccessDenied($exception, 'You must be logged in to play.');
        }

        $campaignService->play($this->getUser());

        $response = [
            'total' => 121,
            'data' => 'companies',
        ];

        return $this->json($response);
    }

    /**
     * -the user can call a service that tells him whether he played or not and if so then the prize will be outputted;
     *
     * @return JsonResponse
     */
    #[Route('/redeem-prize', name: 'app_redeem_prize', methods: ["GET"])]
    public function redeemPrize(
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        } catch (AccessDeniedException $exception) {
            $customMessage = 'You must be logged in order to redeem your prize.';
            return $this->getResponseAccessDenied($exception, $customMessage);
        }


        $response = [
            'total' => 121,
            'data' => 'companies',
        ];

        return $this->json($response);
    }

    private function getResponseAccessDenied(AccessDeniedException $exception, string $customMessage): JsonResponse
    {
        $response = ['error' => [
            'message' => vsprintf('%s %s', [$exception->getMessage(), $customMessage])
        ]];

        return $this->json(data: $response, status: $exception->getCode());
    }
}
