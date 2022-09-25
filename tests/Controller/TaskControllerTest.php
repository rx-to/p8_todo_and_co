<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\TaskFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $client;
    private const USER_EMAIL = 'utilisateur@todo-and-co.com';

    /**
     * This function creates a client that can be used to make requests to the application.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class, TaskFixtures::class]);
    }

    /**
     * The `tearDown()` function is called after each test is run. It is used to clean up the
     * environment after each test
     */
    public function tearDown(): void
    {
        self::ensureKernelShutdown();
        parent::tearDown();
        unset($this->databaseTool);
    }

    /**
     * It logs in a user
     * 
     * @param email The email address of the user you want to log in.
     */
    private function login($email = 'utilisateur@todo-and-co.com')
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail($email);
        $this->client->loginUser($user);
    }
  
    /**
     * This function logs in a user with the email  and then visits the route  and
     * asserts that the response is successful.
     * 
     * @param userEmail The email address of the user you want to log in as.
     * @param route The route to the page you want to test.
     */
    private function accessPageWhileLoggedIn($userEmail, $route)
    {
        $this->login($userEmail);
        $this->client->request('GET', $route);
        $this->assertResponseIsSuccessful();
    }

    /* Testing the route `/tasks` */
    public function testListTasks()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/tasks');
    }

    /**
     * This function tests that a user can access the completed tasks page while logged in.
     */
    public function testListCompletedTasks()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/completed-tasks');
    }

    /**
     * This function tests that the user can access the to do tasks page while logged in.
     */
    public function testListToDoTasks()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/to-do-tasks');
    }

    /**
     * This function tests that the user can access the create task page while logged in.
     */
    public function testDisplayCreateAction()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/tasks/create');
    }

    /**
     * It logs in, goes to the task creation page, fills in the form and submits it
     */
    public function testSubmitFormCreateAction()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerNode = $crawler->selectButton('submit-btn');

        $form = $buttonCrawlerNode->form([
            'task_form[title]'   => 'Titre de la tâche',
            'task_form[content]' => 'Description de la tâche'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
    }

    /**
     * This function tests that the user can access the edit page for a task.
     */
    public function testDisplayEditAction()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/tasks/1/edit');
    }

    /**
     * If you're not logged in, you can't edit a task.
     */
    public function testEditActionWhileNotBeingAuthorized()
    {
        $this->login();
        $this->client->request('GET', '/tasks/2/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * It tests that the form is correctly submitted and that the user is redirected to the tasks list
     */
    public function testEditActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $buttonCrawlerNode = $crawler->selectButton('submit-btn');

        $form = $buttonCrawlerNode->form([
            'edit_task_form[title]'   => 'Titre de la tâche',
            'edit_task_form[content]' => 'Description de la tâche',
            'edit_task_form[isDone]'  => 1
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
    }

    /**
     * It tests that a user who is not authorized to toggle a task cannot toggle it
     */
    public function testToggleTaskActionWhileNotBeingAuthorized()
    {
        $this->login();
        $this->client->request('GET', '/tasks/2/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * It tests that when a user is logged in, he can toggle a task
     */
    public function testToggleTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $this->client->request('GET', '/tasks/1/toggle');
        $this->assertResponseRedirects('/tasks');
    }

    /**
     * This function tests that when a user tries to delete a task that they are not authorized to
     * delete, they are redirected to the homepage.
     */
    public function testDeleteTaskActionWhileNotBeingAuthorized()
    {
        $this->login();
        $this->client->request('GET', '/tasks/2/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * We're testing that when we're logged in, we can delete a task
     */
    public function testDeleteTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $this->client->request('GET', '/tasks/1/delete');
        $this->assertResponseRedirects('/tasks');
    }
}
