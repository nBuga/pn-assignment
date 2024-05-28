<?php

namespace App\Controller;

use App\Exception\CampaignNotValidException;
use App\Service\CampaignService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PlayController extends AbstractController
{
    /**
     */
    #[Route('/{_locale}/play', name: 'app_play', requirements: ['_locale' => 'en|de'], methods: ["GET"])]
    public function index(
        Request $request,
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

            $prize = $campaignService->play($this->getUser());
        } catch (AccessDeniedException $exception) {
            return $this->getResponseException($exception, 'You must be logged in to play.');
        } catch (CampaignNotValidException $exception) {
            return $this->getResponseException($exception);
        } catch (Exception $exception) {
            return $this->getResponseException($exception, 'Something was wrong.');
        }

        $response = [
            'data' => [
                'prizeName' => $prize->translate()->getName(),
                'prizeDescription' => $prize->translate()->getDescription(),
                'partnerName' => $prize->getPartner()->getNameTranslated($request->getLocale()),
                'partnerUrl' => $prize->getPartner()->getUrl(),
            ],
        ];

        return $this->json($response);
    }

    /**
     * -the user can call a service that tells him whether he played or not and if so then the prize will be outputted;
     *
     * @return JsonResponse
     */
    #[Route('/{_locale}/redeem-prize', name: 'app_redeem_prize', requirements: ['_locale' => 'en|de'], methods: ["GET"])]
    public function redeemPrize(
        Request $request,
        CampaignService $campaignService
    ): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
            $prize = $campaignService->redeemTodayPrize($this->getUser());
        } catch (AccessDeniedException $exception) {
            return $this->getResponseException($exception, 'You must be logged in order to redeem your prize.');
        }

        //dd($request->getLocale(), $prize->getPartner()->getNameTranslated($request->getLocale()));

        if (!$prize) {
            $response = [
                'data' => [
                    'message' => 'You didn\'t play today!',
                ]
            ];
        } else {
            $response = [
                'data' => [
                    'prizeName' => $prize->getNameTranslated($request->getLocale()),
                    'prizeDescription' => $prize->translate()->getDescription(),
                    'partnerName' => $prize->getPartner()->getNameTranslated($request->getLocale()),
                    'partnerUrl' => $prize->getPartner()->getUrl(),
                ],
            ];
        }

        return $this->json($response);
    }

    private function getResponseException(
        AccessDeniedException|CampaignNotValidException|Exception $exception,
        string $customMessage = ''
    ): JsonResponse
    {
        $response = ['error' => [
            'message' => vsprintf('%s %s', [$exception->getMessage(), $customMessage])
        ]];

        return $this->json(data: $response, status: $exception->getCode());
    }
}
