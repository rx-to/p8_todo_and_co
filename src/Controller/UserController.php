<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\RegistrationFormType;
use App\Form\UserPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * It renders a template called list.html.twig, which is located in the user directory, and passes
     * it an array of users
     * 
     * @param UserRepository userRepository The repository class for the User entity.
     * 
     * @return The render method returns a Response object.
     */
    #[Route('/users', name: 'user_list'), IsGranted("ROLE_ADMIN")]
    public function listAction(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * It creates a new user, then it creates a form with the user's data, then it handles the request,
     * then it checks if the form is submitted and valid, then it hashes the password, then it persists the
     * user, then it flushes the entity manager, then it adds a flash message, then it redirects to the
     * user list.
     * 
     * That's a lot of stuff.
     * 
     * Let's break it down.
     * 
     * First, we create a new user:
     * 
     *      = new User();
     * 
     * Then we create a form with the user's data:
     * 
     *      = ->createForm(RegistrationFormType::class, );
     * 
     * Then we handle the request:
     * 
     *     ->handleRequest();
     * 
     * Then we check if the form is submitted and valid:
     * 
     * @param Request request The incoming request object.
     * @param UserPasswordHasherInterface userPasswordHasher This is the service that will be used to hash
     * the user's password.
     * @param EntityManagerInterface entityManager This is the service that allows us to persist and flush
     * our data.
     * 
     * @return Response The user/create.html.twig template is being returned.
     */
    #[Route('/users/create', name: 'user_create'), IsGranted("ROLE_ADMIN")]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur <strong>" . $user->getUsername() . '</strong> a bien été créé.');

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * The function is called when the user tries to access the login page. If the user is already logged
     * in, they are redirected to the homepage. If the user is not logged in, they are shown the login
     * page
     * 
     * @param AuthenticationUtils authenticationUtils This is a service that Symfony provides to help you
     * get information about the user's login attempt.
     * 
     * @return Response The last username entered by the user and the error if there is one.
     */
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * "The logout function is a route that is called when the user clicks the logout button. It's a GET
     * request that throws an exception."
     * 
     * The exception is thrown because the logout function is not actually implemented in the controller.
     * Instead, it is implemented in the security.yaml file.
     */
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * It's a function that allows you to edit a user's information and/or password
     * 
     * @param User user The user object that will be edited.
     * @param Request request The request object.
     * @param EntityManagerInterface entityManager This is the object that allows us to interact with the
     * database.
     * @param UserPasswordHasherInterface userPasswordHasher This is the service that will hash the user's
     * password.
     * 
     * @return The user object.
     */
    #[Route('/users/{user}/edit', name: 'user_edit'), IsGranted("ROLE_ADMIN")]
    public function editAction(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $userPasswordForm = $this->createForm(UserPasswordFormType::class, $user)->handleRequest($request);
        $userForm = $this->createForm(UserFormType::class, $user)->handleRequest($request);

        $formIsSubmittedAndValid = false;

        // Form #1 • User info.
        if ($this->getUser() != $user && $userForm->isSubmitted() && $userForm->isValid()) {
            $formIsSubmittedAndValid = true;
            $flash = "Les informations de l'utilisateur <strong>" . $user->getUsername() . '</strong> ont bien été mises à jour.';
        }

        // Form #2 • User password.
        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userPasswordForm->get('password')->getData()
                )
            );

            $formIsSubmittedAndValid = true;
            $flash = "Le mot de passe de l'utilisateur <strong>" . $user->getUsername() . '</strong> a bien été modifié.';
        }

        if ($formIsSubmittedAndValid) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $flash);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['editUserForm' => $userForm->createView(), 'editUserPasswordForm' => $userPasswordForm->createView(), 'user' => $user]);
    }
}
