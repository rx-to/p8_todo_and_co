<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;
    private Task $task;

    /**
     * It creates a user and a task, and then adds the task to the user.
     */
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

    /**
     * This function is called after each test is run.
     */
    public function tearDown(): void
    {
        $this->user = new User();
        $this->tast = new Task();
    }

    /**
     * This function tests the getId() function in the User class.
     */
    public function testGetId(): void
    {
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * This function tests the setId() function in the User class.
     * 
     * @param int id The id of the user.
     */
    public function testSetId(int $id = 2): void
    {
        $this->user->setId($id);
        $this->assertEquals(2, $this->user->getId());
    }

    /**
     * It tests the getEmail() function in the User class.
     */
    public function testGetEmail(): void
    {
        $this->assertEquals("utilisateur@todo-and-co.fr", $this->user->getEmail());
    }

    /**
     * This function tests the setEmail() function in the User class.
     * 
     * @param email The email address to set.
     */
    public function testSetEmail($email = 'new-utilisateur@todo-and-co.fr'): void
    {
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    /**
     * It tests the getUserIdentifier() function in the User class.
     */
    public function testGetUserIdentifier(): void
    {
        $this->assertSame("utilisateur@todo-and-co.fr", $this->user->getUserIdentifier());
    }

    /**
     * The function tests the getRoles() method of the User class.
     */
    public function testGetRoles(): void
    {
        $roles = $this->user->getRoles();
        $this->assertEquals(['ROLE_USER'], array_unique($roles));
    }

    /**
     * This function tests the setRoles() function in the User class.
     * 
     * @param array roles The roles to set.
     */
    public function testSetRoles(array $roles = ['ROLE_NEW']): void
    {
        $this->user->setRoles($roles);
        $this->assertEquals($roles, array_unique($roles));
    }

    /**
     * It tests the getPassword() function in the User class.
     */
    public function testGetPassword(): void
    {
        $this->assertEquals('mdp', $this->user->getPassword());
    }

    /**
     * This function tests if the password is set correctly.
     * 
     * @param string password The password to set.
     */
    public function testSetPassword(string $password = 'new-mdp'): void
    {
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    /**
     * It tests the getUsername() function in the User class.
     */
    public function testGetUsername(): void
    {
        $this->assertEquals('Utilisateur', $this->user->getUsername());
    }

    /**
     * This function tests the setUsername() function of the User class.
     * 
     * @param string username The value to be passed to the setUsername() method.
     */
    public function testSetUsername(string $username = 'Utilisateur2'): void
    {
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    /**
     * The function is testing the relationship between the user and the task.
     */
    public function testGetTasks(): void
    {
        $countTasks = $this->user->getTasks()->count();
        $this->assertEquals(1, $countTasks);
    }

    /**
     * This function tests the addTask() function of the User class.
     */
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

    /**
     * This function tests the removeTask() function of the User class.
     */
    public function testRemoveTask(): void
    {
        $this->user->removeTask($this->task);
        $countTasks = $this->user->getTasks()->count();
        $this->assertEquals(0, $countTasks);
    }
}
