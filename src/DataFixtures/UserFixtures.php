<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $users = [
            (new User())
                ->setId(1)
                ->setEmail('administrateur@todo-and-co.com')
                ->setUsername('Administrateur')
                ->setPassword('Administrateur1*')
                ->setRoles(['ROLE_ADMIN']),

            (new User())
                ->setId(2)
                ->setEmail('utilisateur@todo-and-co.com')
                ->setUsername('Utilisateur')
                ->setPassword('Utilisateur1*')
                ->setRoles(['ROLE_USER']),
        ];

        foreach ($users as $user) {
            $manager->persist($user);
            $this->addReference($user->getEmail(), $user);
        }

        $manager->flush();
    }
}
