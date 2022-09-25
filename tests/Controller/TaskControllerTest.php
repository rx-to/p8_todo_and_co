<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;

    /**
     * This function creates a client that can be used to make requests to the application.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

   /**
    * The `tearDown()` function is called after each test is run. It is used to clean up the
    * environment after each test
    */
    public function tearDown(): void
    {
        $this->client = null;
    }

    /* A function that is used to log in a user. */
    private function login()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('utilisateur@todo-and-co.com');
        $this->client->loginUser($user);
    }

    /**
     * "This function logs in a user, then requests a page, and asserts that the response is
     * successful."
     * 
     * This function is used in the following way:
     * 
     * @param route The route to the page you want to access.
     */
    private function accessPageWhileLoggedIn($route)
    {
        $this->login();
        $crawler = $this->client->request('GET', $route);

        $this->assertResponseIsSuccessful();
    }

    /* Testing the route `/tasks` */
    public function testListTasks()
    {
        $this->accessPageWhileLoggedIn('/tasks');
    }

    /**
     * This function tests that a user can access the completed tasks page while logged in.
     */
    public function testListCompletedTasks()
    {
        $this->accessPageWhileLoggedIn('/completed-tasks');
    }

   /**
     * This function tests that the user can access the to do tasks page while logged in.
    */
    public function testListToDoTasks()
    {
        $this->accessPageWhileLoggedIn('/to-do-tasks');
    }

    /**
     * This function tests that the user can access the create task page while logged in.
     */
    public function testDisplayCreateAction()
    {
        $this->accessPageWhileLoggedIn('/tasks/create');
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
        $this->accessPageWhileLoggedIn('/tasks/555/edit');
    }

    /**
     * If you're not logged in, you can't edit a task.
     */
    public function testEditActionWhileNotBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/666/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * It tests that the form is correctly submitted and that the user is redirected to the tasks list
     */
    public function testEditActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/edit');
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
        $crawler = $this->client->request('GET', '/tasks/666/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * It tests that when a user is logged in, he can toggle a task
     */
    public function testToggleTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/toggle');
        $this->assertResponseRedirects('/tasks');
    }

    /**
     * This function tests that when a user tries to delete a task that they are not authorized to
     * delete, they are redirected to the homepage.
     */
    public function testDeleteTaskActionWhileNotBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * We're testing that when we're logged in, we can delete a task
     */
    public function testDeleteTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/delete');
        $this->assertResponseRedirects('/tasks');
    }

    /* A function that is used to test that the user is authorized to manage a task. */
    private function testCheckAuthorizationsIsOk()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->find(666);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /* A function that is used to test that the user is not authorized to manage a task. */
    private function testCheckAuthorizationsIsNotOk()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->find(666);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
