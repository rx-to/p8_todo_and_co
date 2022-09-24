<?php

namespace App\Test\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private User $user;
    private Task $task;
    private DateTimeImmutable $date;

    public function setUp(): void
    {
        $this->date = new DateTimeImmutable('2022-09-24 04:00:00');
        $this->task = (new Task())
            ->setId(1)
            ->setContent('Contenu de la tâche n°1')
            ->setCreatedAt($this->date)
            ->setIsDone(false)
            ->setTitle('Tâche n°1');

        $this->user = (new User())
            ->setId(1)
            ->setUsername('Utilisateur')
            ->setEmail('utilisateur@todo-and-co.fr')
            ->setRoles(['ROLE_USER'])
            ->setPassword('mdp')
            ->addTask($this->task);
    }

    public function tearDown(): void
    {
        $this->task = new task();
    }

    public function testSetId(int $id = 2): void
    {
        $this->task->setId($id);
        $this->assertSame(2, $this->task->getId());
    }

    public function testGetId(): void
    {
        $this->assertSame(1, $this->task->getId());
    }
    public function testGetCreatedAt(): void
    {
        $this->assertSame($this->date, $this->task->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $date = new DateTimeImmutable('2022-09-24 04:10:00');
        $this->task->setCreatedAt($date);
        $this->assertSame($date, $this->task->getCreatedAt());
    }

    public function testGetTitle(): void
    {
        $this->assertSame('Tâche n°1', $this->task->getTitle());
    }

    public function testSetTitle(string $title = 'Nouveau titre de la tâche n°1'): void
    {
        $this->task->setTitle($title);
        $this->assertSame($title, $this->task->getTitle());
    }

    public function testGetContent(): void
    {
        $this->assertSame('Contenu de la tâche n°1', $this->task->getContent());
    }

    public function testSetContent(string $content = 'Nouveau contenu de la tâche n°1'): void
    {
        $this->task->setContent($content);
        $this->assertSame($content, $this->task->getContent());
    }

    public function testIsDone(): void
    {
        $this->assertSame(false, $this->task->isDone());
    }

    public function testSetIsDone(bool $isDone = true): void
    {
        $this->task->setIsDone($isDone);
        $this->assertSame($isDone, $this->task->isDone());
    }

    public function testGetUser(): void
    {
        $this->assertSame($this->user, $this->task->getUser());
    }

    public function testSetUser(): void
    {
        $user = (new User())
            ->setId(2)
            ->setEmail('administrateur@todo-and-co.fr')
            ->setPassword('mdp')
            ->setRoles(['ROLE_ADMIN'])
            ->setUsername('Administrateur');

        $this->task->setUser($user);
        $this->assertSame($user, $this->task->getUser());
    }

    public function testToggle($flag = true): void
    {
        $this->task->toggle($flag);
        $this->assertSame($flag, $this->task->isDone());
    }
}
