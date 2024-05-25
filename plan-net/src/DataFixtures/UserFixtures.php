<?php
namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\Prize;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->addUser($manager);
        $manager->flush();
    }

    private function addUser(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'demo'.$i
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }
    }
}
