<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Task;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    { 
        $tasks = [
            (new Task())
                ->setTitle('Tâche 1')
                ->setContent('Contenu de la tâche')
                ->setIsDone(0)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUser($this->getReference('utilisateur@todo-and-co.com')),

            (new Task())
                ->setTitle('Tâche 2')
                ->setContent('Contenu de la tâche')
                ->setIsDone(1)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUser($this->getReference('administrateur@todo-and-co.com')),
        ];

        foreach ($tasks as $task) {
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
