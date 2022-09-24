<?php

namespace App\Test\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;
    private Task $task;

    public function setUp(): void
    {
        $this->task = (new Task())
            ->setId(1)
            ->setContent('Contenu de la tâche n°1')
            ->setCreatedAt(new DateTimeImmutable())
            ->setIsDone(0)
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
        $this->user = new User();
    }

    public function testSetId(int $id = 2): void
    {
        $this->user->setId($id);
        $this->assertEquals(2, $this->user->getId());
    }

    public function testGetId(): void
    {
        $this->assertEquals(1, $this->user->getId());
    }

    public function testGetEmail(): void
    {
        $this->assertEquals("utilisateur@todo-and-co.fr", $this->user->getEmail());
    }

    public function testSetEmail($email = 'new-utilisateur@todo-and-co.fr'): void
    {
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function testGetUserIdentifier(): void
    {
        $this->assertSame("utilisateur@todo-and-co.fr", $this->user->getUserIdentifier());
    }

    /**
     * @see UserInterface
     */
    public function testGetRoles(): void
    {
        $roles = $this->user->getRoles();
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $this->assertEquals(['ROLE_USER'], array_unique($roles));
    }

    public function testSetRoles(array $roles = ['ROLE_NEW']): void
    {
        $this->user->setRoles($roles);
        $this->assertEquals($roles, array_unique($roles));
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function testGetPassword(): void
    {
        $this->assertEquals('mdp', $this->user->getPassword());
    }

    public function testSetPassword(string $password = 'new-mdp'): void
    {
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testGetUsername(): void
    {
        $this->assertEquals('Utilisateur', $this->user->getUsername());
    }

    public function testSetUsername(string $username = 'Utilisateur2'): void
    {
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }


    public function testGetTasks(): void
    {
        $countTasks = $this->user->getTasks()->count();
        $this->assertEquals(1, $countTasks);
    }

    public function testAddTask(): void
    {
        $task = (new Task())
            ->setId(2)
            ->setContent('Contenu de la tâche n°2')
            ->setCreatedAt(new DateTimeImmutable())
            ->setIsDone(1)
            ->setTitle('Tâche n°2');

        if (!$this->user->getTasks()->contains($task)) {
            $this->user->addTask($task);
            $task->setUser($this->user);

            $countTasks = $this->user->getTasks()->count();

            $this->assertEquals(2, $countTasks);
        }
    }

    public function testRemoveTask(): void
    {
        $this->user->removeTask($this->task);
        $countTasks = $this->user->getTasks()->count();
        $this->assertEquals(0, $countTasks);
    }
}
