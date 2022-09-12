<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list'), IsGranted("ROLE_ADMIN")]
    public function listAction(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

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

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

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

        if($formIsSubmittedAndValid) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $flash);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['editUserForm' => $userForm->createView(), 'editUserPasswordForm' => $userPasswordForm->createView(), 'user' => $user]);
    }
}
