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

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function login()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('utilisateur@todo-and-co.com');
        $this->client->loginUser($user);
    }

    private function accessPageWhileLoggedIn($route)
    {
        $this->login();
        $crawler = $this->client->request('GET', $route);

        $this->assertResponseIsSuccessful();
    }

    public function testListTasks()
    {
        $this->accessPageWhileLoggedIn('/tasks');
    }

    public function testListCompletedTasks()
    {
        $this->accessPageWhileLoggedIn('/completed-tasks');
    }

    public function testListToDoTasks()
    {
        $this->accessPageWhileLoggedIn('/to-do-tasks');
    }

    public function testDisplayCreateAction()
    {
        $this->accessPageWhileLoggedIn('/tasks/create');
    }

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

    public function testDisplayEditAction()
    {
        $this->accessPageWhileLoggedIn('/tasks/555/edit');
    }

    public function testEditActionWhileNotBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/666/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

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

    public function testToggleTaskActionWhileNotBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/666/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testToggleTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/toggle');
        $this->assertResponseRedirects('/tasks');
    }

    public function testDeleteTaskActionWhileNotBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteTaskActionWhileBeingAuthorized()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/tasks/555/delete');
        $this->assertResponseRedirects('/tasks');
    }

    private function testCheckAuthorizationsIsOk()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->find(666);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    private function testCheckAuthorizationsIsNotOk()
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->find(666);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
