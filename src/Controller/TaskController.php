<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Form\EditTaskFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list')]
    public function listTasks(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }

    #[Route('/completed-tasks', name: 'completed_task_list')]
    public function listCompletedTasks(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => 1])]);
    }

    #[Route('/to-do-tasks', name: 'to_do_task_list')]
    public function listToDoTasks(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => 0])]);
    }

    #[Route('/tasks/create', name: 'task_create'), IsGranted("ROLE_USER")]
    /**
     * It creates a new task, creates a form for it, handles the request, checks if the form is valid, sets
     * the user, sets the task as not done, persists the task, flushes the task, adds a flash message, and
     * redirects to the task list.
     * 
     * @param Request request The request object, which contains information about the current HTTP
     * request.
     * @param EntityManagerInterface entityManager The EntityManagerInterface instance.
     */
    public function createAction(Request $request, EntityManagerInterface $entityManager)
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $task->isDone(0);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['taskForm' => $form->createView()]);
    }

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

    #[Route('/tasks/{id}/toggle', name: 'task_toggle'), IsGranted("ROLE_USER")]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été mis à jour.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete'), IsGranted("ROLE_USER")]
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    private function checkAuthorizations(Task $task)
    {
        $condition1 = $task->getUser() == $this->getUser();
        $condition2 = $this->isGranted('ROLE_ADMIN') && !$task->getUser();

        if (!$condition1 && !$condition2) throw $this->createAccessDeniedException();
    }
}
