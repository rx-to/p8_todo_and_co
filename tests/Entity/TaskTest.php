<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private User $user;
    private Task $task;
    private DateTimeImmutable $date;

    /**
     * It creates a user, a task and a date, and then assigns the task to the user
     */
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

    /**
     * This function is used to reset the task and user objects to their default values
     */
    public function tearDown(): void
    {
        $this->task = new Task();
        $this->user = new User();
    }

    /**
     * This function tests the setId() function in the Task class
     * 
     * @param int id The id of the task
     */
    public function testSetId(int $id = 2): void
    {
        $this->task->setId($id);
        $this->assertSame(2, $this->task->getId());
    }

    /**
     * It tests the getId() function.
     */
    public function testGetId(): void
    {
        $this->assertSame(1, $this->task->getId());
    }

    /**
     * It tests the getCreatedAt function.
     */
    public function testGetCreatedAt(): void
    {
        $this->assertSame($this->date, $this->task->getCreatedAt());
    }

    /**
     * It sets the created at date to the date passed in.
     */
    public function testSetCreatedAt(): void
    {
        $date = new DateTimeImmutable('2022-09-24 04:10:00');
        $this->task->setCreatedAt($date);
        $this->assertSame($date, $this->task->getCreatedAt());
    }

    /**
     * It tests that the getTitle() method returns the correct value
     */
    public function testGetTitle(): void
    {
        $this->assertSame('Tâche n°1', $this->task->getTitle());
    }

    /**
     * It tests that the setTitle() method of the Task class sets the title of the task.
     * 
     * @param string title The title of the task
     */
    public function testSetTitle(string $title = 'Nouveau titre de la tâche n°1'): void
    {
        $this->task->setTitle($title);
        $this->assertSame($title, $this->task->getTitle());
    }

    /**
     * It tests the getContent() function in the Task class.
     */
    public function testGetContent(): void
    {
        $this->assertSame('Contenu de la tâche n°1', $this->task->getContent());
    }

    /**
     * This function tests the setContent() function in the Task class.
     * 
     * @param string content The value to be passed to the setContent() method.
     */
    public function testSetContent(string $content = 'Nouveau contenu de la tâche n°1'): void
    {
        $this->task->setContent($content);
        $this->assertSame($content, $this->task->getContent());
    }

    /**
     * This function tests the isDone() function in the Task class.
     */
    public function testIsDone(): void
    {
        $this->assertSame(false, $this->task->isDone());
    }

    /**
     * This function tests the setIsDone() function in the Task class.
     * 
     * @param bool isDone true
     */
    public function testSetIsDone(bool $isDone = true): void
    {
        $this->task->setIsDone($isDone);
        $this->assertSame($isDone, $this->task->isDone());
    }

    /**
     * This function tests the getUser() function in the Task class.
     */
    public function testGetUser(): void
    {
        $this->assertSame($this->user, $this->task->getUser());
    }

    /**
     * This function tests the setUser() function in the Task class.
     */
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

    /**
     * This function tests the toggle() function in the Task class.
     */    public function testToggle($flag = true): void
    {
        $this->task->toggle($flag);
        $this->assertSame($flag, $this->task->isDone());
    }
}
