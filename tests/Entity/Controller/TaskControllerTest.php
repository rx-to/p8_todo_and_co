<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    //TODO: test
    #[Route('/tasks/{id}/edit', name: 'task_edit'), IsGranted("ROLE_USER")]
    public function editAction(Task $task, Request $request, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $form = $this->createForm(EditTaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->isDone(0);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'taskForm' => $form->createView(),
            'task' => $task,
        ]);
    }

    //TODO: test
    #[Route('/tasks/{id}/toggle', name: 'task_toggle'), IsGranted("ROLE_USER")]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été mis à jour.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    //TODO: test
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    //TODO: test
    private function checkAuthorizations(Task $task)
    {
        $condition1 = $task->getUser() == $this->getUser();
        $condition2 = $this->isGranted('ROLE_ADMIN') && !$task->getUser();

        if (!$condition1 && !$condition2) throw $this->createAccessDeniedException();
    }
}
