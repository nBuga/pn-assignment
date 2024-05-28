<?php

namespace App\Service;

use App\Entity\Prize;
use App\Exception\CampaignNotValidException;
use App\Repository\PrizeRepository;
use App\Repository\UserPrizeRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class CampaignService
{
    /**
     *
     * We have a promotion for a client with prizes for users who play.
     *
     * You have two Excel tables with values for partners and prizes in a dual language system.
     * Your tasks are:
     *
     * DONE! - import the data into a storage system of your choosing (we recommend MySQL);
     * DONE! - create a user persistence system (and acouple of users 5-10) in the same storage; (E.g. MySQL table)
     * DONE!    - users should be authenticated before they can play;
     * (no registration required, you can fill the data of the user in the persistence module you've chosen directly
     * and use a secure authentication method of your choosing)
     * - the promotion will span 2 days and you will have to divide the prizes equally for the two days of the campaign; E.g.: if you have 100 prizes you have 50 prizes on the first day and 50 on the second.
     * - the game rules are the following:
     * -the user logs in;
     * -the user calls a service (a GET call to a url) to play the game and receives a random prize that is outputted;
     * (it is very important to deal with the concurrency issues so that the same prize is not awarded to two users and the exact amount of prizes is given and not more)
     * DONE -no prizes are to be given from 00:00:00 to 09:00:00 and 20:00:00 to 23:59:59;
     * DONE -the user can no longer play if he won a prize already that day;
     * DONE -if the user calls the play service again an error will be shown;
     * DONE -the user can call a service that tells him whether he played or not and if so then the prize will be outputted;
     * -when the prize is outputted to the user it will also contain the partner data;
     * -user will provide the language parameter to the server and server will choose appropriate language (for you to decide how to pass it);
     *
     * The server must always reply in JSON format and the services should obey the REST principles.
     *
     * We encourage you to use a framework and recommend the following which we are currently using: Symfony, Phalcon.
     * We look at database design, code design, OOP principles applied and code formatting.
     *
     * Authentication pe api (bearer, etc)
     *
     * !!!!
     * Daca nu mai exista premii disponibile, afisez mesaj eroare !!!
     *
     */

    public CONST CAMPAIGN_AVAILABILITY_DAYS = 2;
    public CONST CAMPAIGN_START_DATE = '2024-05-27';
    public CONST CAMPAIGN_START_TIME = '09:00:00';
    public CONST CAMPAIGN_END_TIME = '20:00:00';
    public CONST DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public CONST TIME_FORMAT = 'H:i:s';
    public CONST TIMEZONE = 'Europe/Bucharest';

    private int $totalNumberAvailablePrizes;

    public function __construct(
        private readonly PrizeRepository $prizeRepository,
        private readonly UserPrizeRepository $userPrizeRepository,
        private readonly PrizeService $prizeService
    ){
        $this->setTotalNumberOfPrizes();
    }

    private function setTotalNumberOfPrizes(): void
    {
        $this->totalNumberAvailablePrizes = $this->prizeRepository->countPrizes();
    }

    /**
     * @param UserInterface $user
     * @return Prize
     * @throws CampaignNotValidException
     * @throws Exception
     */
    public function play(UserInterface $user): Prize
    {
        $this->validateDateTime();
        $this->validateUserAlreadyPlayed($user);
        $this->validateTotalPrizeForToday();

        return $this->prizeService->saveUserPrize($user, $this->getRandomPrize());
    }

    /**
     * @return void
     * @throws CampaignNotValidException
     */
    private function validateDateTime(): void
    {
        $currentDateTime = $this->currentDateTime();
        $currentTime = $currentDateTime->format(self::TIME_FORMAT);
        $startDateTime = new \DateTime(self::CAMPAIGN_START_DATE .' '.self::CAMPAIGN_START_TIME, new \DateTimeZone(self::TIMEZONE));
        $endDateTime = new \DateTime(self::CAMPAIGN_START_DATE .' '.self::CAMPAIGN_END_TIME, new \DateTimeZone(self::TIMEZONE));
        $endDateTime->modify(sprintf('+%d days', self::CAMPAIGN_AVAILABILITY_DAYS - 1));
        $startTime = self::CAMPAIGN_START_TIME;
        $endTime = self::CAMPAIGN_END_TIME;

        if ($currentDateTime >= $startDateTime && $currentDateTime < $endDateTime) {
            if ($currentTime >= $startTime && $currentTime <= $endTime) {
                return;
            }
        }

        throw new CampaignNotValidException(
            vsprintf('Campaign date or interval hours not valid! The campaign is active from %s until %s between %s and %s', [
                $startDateTime->format(self::DATE_TIME_FORMAT),
                $endDateTime->format(self::DATE_TIME_FORMAT),
                $startTime,
                $endTime,
            ])
        );
    }

    /**
     * @return void
     * @throws CampaignNotValidException
     */
    private function validateUserAlreadyPlayed(UserInterface $user): void
    {
        $userPrize = $this->userPrizeRepository->findTodayPrize($user, $this->currentDateTime());

        if ($userPrize) {
            throw new CampaignNotValidException("You already played and won a prize for today!");
        }
    }

    /**
     * the promotion will span 2 days and you will have to divide the prizes equally for the two days of the campaign;
     * E.g.: if you have 100 prizes you have 50 prizes on the first day and 50 on the second.
     * @return void
     * @throws CampaignNotValidException
     */
    private function validateTotalPrizeForToday(): void
    {
        /**
         * check totalNumberOfPrizesForToday from Redis
         *
         */

        /**
         * todo: cum stiu a 2-a zis sa numar?!? sau sa nu mai numar daca este a 2-a zi de premii
         */

        $numberOfPrizesWonToday = $this->userPrizeRepository->countTodayPrizes($this->currentDateTime());
        $totalNumberOfPrizesPossible = round(num: $this->totalNumberAvailablePrizes / self::CAMPAIGN_AVAILABILITY_DAYS, mode: PHP_ROUND_HALF_DOWN);
        if ($numberOfPrizesWonToday >= $totalNumberOfPrizesPossible) {
            throw new CampaignNotValidException(sprintf("All the %d prizes were played today!", $totalNumberOfPrizesPossible));
        }
    }

    private function getRandomPrize(): Prize
    {
        $prizes = $this->prizeRepository->getAvailablePrizes();
        shuffle($prizes);

        return $prizes[0];
    }

    public function redeemTodayPrize(UserInterface $user): ?Prize
    {
        $userPrize = $this->userPrizeRepository->findTodayPrize($user, $this->currentDateTime());

        return $userPrize?->getPrize() ?? null;
    }

    private function currentDateTime(): \DateTime
    {
        return new \DateTime('now', new \DateTimeZone(self::TIMEZONE));
    }

}