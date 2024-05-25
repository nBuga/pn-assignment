<?php
namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\Prize;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class bkp
{
    public const LOCALE_EN = 'en';
    public const LOCALE_DE = 'de';

    private array $partners;

    public function load(ObjectManager $manager): void
    {
        $this->addPartners($manager);
        $this->addPrizes($manager);

        $manager->flush();
    }

    private function addPartners(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $partner = new Partner();
            $partner->setUrl(sprintf('www.partner%d.com', $i));
            $partner->setCode(sprintf('pt%d', $i));
            $partner->translate(self::LOCALE_EN)->setName(vsprintf('Partner name %d %s', [$i, self::LOCALE_EN]));
            $partner->translate(self::LOCALE_DE)->setName(vsprintf('Partner name %d %s', [$i, self::LOCALE_DE]));
            $manager->persist($partner);
            $partner->mergeNewTranslations();

            $this->partners[] = $partner;
        }
    }

    private function addPrizes(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 1; $i <= 1000; $i++) {
            $prizeNumberGenerated = $faker->numberBetween(1, 50);

            $prize = new Prize();
            $prize->setPartnerCode($this->partners[rand(0, count($this->partners) - 1)]);
            $prize->setCode(sprintf('pr%d', $prizeNumberGenerated));
            $prize->translate(self::LOCALE_EN)->setName(vsprintf('Prize name %d %s', [$prizeNumberGenerated, self::LOCALE_EN]));
            $prize->translate(self::LOCALE_DE)->setName(vsprintf('Prize name %d %s', [$prizeNumberGenerated, self::LOCALE_DE]));
            $prize->translate(self::LOCALE_EN)->setDescription(vsprintf('Prize description %d %s', [$prizeNumberGenerated, self::LOCALE_EN]));
            $prize->translate(self::LOCALE_DE)->setDescription(vsprintf('Prize description %d %s', [$prizeNumberGenerated, self::LOCALE_DE]));

            $manager->persist($prize);
            $prize->mergeNewTranslations();
        }
    }
}
