<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\CampaignNotValidException;
use App\Service\CampaignService;
use App\Service\PrizeService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlayController extends AbstractController
{
    #[Route('/{_locale}/play', name: 'app_play', requirements: ['_locale' => 'en|de'], methods: ["GET"])]
    public function index(
        Request         $request,
        CampaignService $campaignService,
        TranslatorInterface $translator
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

            $prize = $campaignService->play($this->getUser());

            $response = [
                'data' => [
                    'prizeName' => $prize->translate()->getName(),
                    'prizeDescription' => $prize->translate()->getDescription(),
                    'partnerName' => $prize->getPartner()->getNameTranslated($request->getLocale()),
                    'partnerUrl' => $prize->getPartner()->getUrl(),
                    'partnerCode' => $prize->getPartner()->getCode(),
                ],
            ];

            return $this->json($response);

        } catch (AccessDeniedException $exception) {
            return $this->getResponseException($exception, $translator->trans('user.not_logged_in'));
        } catch (CampaignNotValidException $exception) {
            return $this->getResponseException($exception);
        } catch (Exception $exception) {
            return $this->getResponseException($exception, '');
        }
    }

    #[Route('/{_locale}/redeem-prize', name: 'app_redeem_prize', requirements: ['_locale' => 'en|de'], methods: ["GET"])]
    public function redeemPrize(
        Request         $request,
        PrizeService $prizeService,
        TranslatorInterface $translator
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

            $prize = $prizeService->redeemTodayPrize($this->getUser());

            if (!$prize) {
                $response = [
                    'data' => [
                        'message' => $translator->trans('prize.user_not_played'),
                    ]
                ];
            } else {
                $response = [
                    'data' => [
                        'prizeName' => $prize->getNameTranslated($request->getLocale()),
                        'prizeDescription' => $prize->translate()->getDescription(),
                        'partnerName' => $prize->getPartner()->getNameTranslated($request->getLocale()),
                        'partnerUrl' => $prize->getPartner()->getUrl(),
                        'partnerCode' => $prize->getPartner()->getCode(),
                    ],
                ];
            }

            return $this->json($response);

        } catch (AccessDeniedException $exception) {
            return $this->getResponseException($exception, $translator->trans('prize.user_not_logged'));
        }
    }

    private function getResponseException(
        AccessDeniedException|CampaignNotValidException|Exception $exception,
        string                                                    $customMessage = ''
    ): JsonResponse {
        $response = ['error' => [
            'message' => vsprintf('%s %s', [$exception->getMessage(), $customMessage])
        ]];

        return $this->json(data: $response, status: $exception->getCode());
    }
}
