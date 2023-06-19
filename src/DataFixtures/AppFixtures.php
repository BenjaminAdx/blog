<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{


    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setFullName('Andrieux Benjamin')
            ->setPseudo('Bibi')
            ->setEmail('andrieux.benjamin1@gmail.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('Camels01*');

        $users[] = $user;
        $manager->persist($user);


        $manager->flush();
    }
}
