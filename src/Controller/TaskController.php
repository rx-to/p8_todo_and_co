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
    /**
     * It takes a TaskRepository object as an argument, and then uses it to find all the tasks in the
     * database
     * 
     * @param TaskRepository taskRepository The repository class for the Task entity.
     * 
     * @return The render method is a shortcut that combines the following two steps:
     */
    #[Route('/tasks', name: 'task_list')]
    public function listTasks(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }

    /**
     * It renders the template task/list.html.twig and passes it an array of tasks that are completed
     * 
     * @param TaskRepository taskRepository The repository class for the Task entity.
     * 
     * @return The render method is returning a Response object.
     */
    #[Route('/completed-tasks', name: 'completed_task_list')]
    public function listCompletedTasks(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => 1])]);
    }

    /**
     * It renders a template called list.html.twig and passes it an array of tasks that are not done
     * 
     * @param TaskRepository taskRepository The repository class for the Task entity.
     * 
     * @return The render method returns a Response object.
     */
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

    /**
     * It checks if the user is authorized to edit the task, then it creates a form, handles the request,
     * checks if the form is submitted and valid, then it sets the task to undone, persists the task,
     * flushes the entity manager, adds a flash message, and redirects to the task list.
     * 
     * @param Task task The Task object that's being edited.
     * @param Request request The request object, which is used to get the POST data submitted from the
     * form.
     * @param EntityManagerInterface entityManager This is the service that allows us to persist and flush
     * our Task object to the database.
     * 
     * @return The return value of the controller is a Response object.
     */
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

    /**
     * The function toggleTaskAction() is a controller function that takes a Task object and an
     * EntityManagerInterface object as parameters. It checks the authorizations of the task, toggles
     * the task's status, and then flushes the entity manager. It then adds a flash message to the
     * session and redirects to the task list
     * 
     * @param Task task The Task object that matches the {id} parameter in the URL.
     * @param EntityManagerInterface entityManager The entity manager is the object that allows you to
     * persist objects in the database.
     * 
     * @return The return value of the method is a RedirectResponse object.
     */
    #[Route('/tasks/{id}/toggle', name: 'task_toggle'), IsGranted("ROLE_USER")]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été mis à jour.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * If the user is logged in, and the user is the author of the task, then delete the task.
     * 
     * @param Task task The Task object that is being deleted.
     * @param EntityManagerInterface entityManager The EntityManagerInterface is the object that allows
     * you to persist and retrieve objects from the database.
     * 
     * @return The return value of the method is a Symfony\Component\HttpFoundation\RedirectResponse
     * object.
     */
    #[Route('/tasks/{id}/delete', name: 'task_delete'), IsGranted("ROLE_USER")]
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager)
    {
        $this->checkAuthorizations($task);
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    /**
     * If the user is not the owner of the task and is not an admin, throw an exception
     * 
     * @param Task task The task object that we want to delete.
     */
    private function checkAuthorizations(Task $task)
    {
        $condition1 = $task->getUser() == $this->getUser();
        $condition2 = $this->isGranted('ROLE_ADMIN') && !$task->getUser();

        if (!$condition1 && !$condition2) throw $this->createAccessDeniedException();
    }
}
