<?php

declare(strict_types=1);

namespace App\Service;

use App\Cache\CampaignCache;
use App\Entity\Prize;
use App\Exception\CampaignNotValidException;
use App\Repository\UserPrizeRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CampaignService
{
    public const CAMPAIGN_AVAILABILITY_DAYS = 2;
    public const CAMPAIGN_START_DATE = '2024-05-30';
    public const CAMPAIGN_START_TIME = '08:00:00';
    public const CAMPAIGN_END_TIME = '20:00:00';
    public const DATE_FORMAT = 'Y-m-d';
    public const TIME_FORMAT = 'H:i:s';
    public const TIMEZONE = 'Europe/Bucharest';

    public function __construct(
        private UserPrizeRepository $userPrizeRepository,
        private PrizeService $prizeService,
        private CampaignCache $campaignCache,
        private TranslatorInterface $translator
    ) {
        $this->campaignCache->setCampaignPrizesPerDay();
    }

    /**
     * @throws CampaignNotValidException
     * @throws Exception
     */
    public function play(UserInterface $user): Prize
    {
        $this->validateDateTime();
        $this->validateTotalPrizeForToday();
        $this->validateUserAlreadyPlayed($user);

        // todo: traducere erori
        return $this->prizeService->saveUserPrize($user);
    }

    private function validateDateTime(): void
    {
        $currentDateTime = self::currentDateTime();
        $currentTime = $currentDateTime->format(self::TIME_FORMAT);
        $startDateTime = self::startDateTime();
        $endDateTime = self::endDateTime();
        $startTime = self::CAMPAIGN_START_TIME;
        $endTime = self::CAMPAIGN_END_TIME;

        if ($currentDateTime >= $startDateTime && $currentDateTime < $endDateTime) {
            if ($currentTime >= $startTime && $currentTime <= $endTime) {
                return;
            }
        }

        throw new CampaignNotValidException(
            $this->translator->trans('campaign.date_or_hours_not_valid', [
                "startDate" => $startDateTime->format(self::DATE_FORMAT),
                "endDate" => $endDateTime->format(self::DATE_FORMAT),
                "startTime" => $startTime,
                "endTime" => $endTime,
            ])
        );
    }

    private function validateUserAlreadyPlayed(UserInterface $user): void
    {
        $userPrize = $this->userPrizeRepository->findTodayPrize($user, self::currentDateTime());

        if ($userPrize) {
            throw new CampaignNotValidException($this->translator->trans("user.already_played"));
        }
    }

    private function validateTotalPrizeForToday(): void
    {
        $prizeStockForToday = $this->campaignCache->getPrizesPerDay();
        $countTodayPrizes = $this->userPrizeRepository->countTodayPrizes(self::currentDateTime());

        if ($countTodayPrizes >= $prizeStockForToday) {
            throw new CampaignNotValidException($this->translator->trans("prize.played_today", ['countPrizes' => $prizeStockForToday]));
        }
    }

    public static function currentDateTime(): \DateTime
    {
        return new \DateTime('now', new \DateTimeZone(self::TIMEZONE));
    }

    public static function startDateTime(): \DateTime
    {
        return new \DateTime(self::CAMPAIGN_START_DATE .' '.self::CAMPAIGN_START_TIME, new \DateTimeZone(self::TIMEZONE));
    }

    public static function endDateTime(): \DateTime
    {
        $endDateTime = new \DateTime(self::CAMPAIGN_START_DATE .' '.self::CAMPAIGN_END_TIME, new \DateTimeZone(self::TIMEZONE));
        $endDateTime->modify(sprintf('+%d days', self::CAMPAIGN_AVAILABILITY_DAYS - 1));

        return $endDateTime;
    }
}
