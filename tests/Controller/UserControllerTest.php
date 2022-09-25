<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\TaskFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $client;
    private const USER_EMAIL = 'utilisateur@todo-and-co.com';
    private const ADMIN_EMAIL = 'administrateur@todo-and-co.com';

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
     * "Log in as a user, then access a page, and assert that the response is successful."
     * 
     * @param userEmail The email of the user you want to log in as.
     * @param route the route to access
     * @param response The HTTP response code you expect to get back from the server.
     */
    private function accessPageWhileLoggedIn($userEmail, $route, $response = Response::HTTP_OK)
    {
        $this->login($userEmail);
        $crawler = $this->client->request('GET', $route);

        if ($response === Response::HTTP_OK)
            $this->assertResponseIsSuccessful();
        else
            $this->assertResponseStatusCodeSame($response);

        return $crawler;
    }

    /**
     * If I try to access the /users page as a user, I should get a 403 response.
     */
    public function testListActionAsAUser()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/users', Response::HTTP_FORBIDDEN);
    }

    /* It tests that the user can access the list of users while logged in as an admin. */
    public function testListActionAsAnAdmin()
    {
        $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users');
    }

    /**
     * This function tests that the user can access the create user page while logged in as a user.
     */
    public function testDisplayCreateActionAsAUser()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/users/create', Response::HTTP_FORBIDDEN);
    }

    /**
     * This function tests that the user can access the create user page while logged in as an admin.
     */
    public function testDisplayCreateActionAsAnAdmin()
    {
        $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users/create');
    }

    /**
     * It tests that an admin can create a user
     */
    public function testSubmitFormCreateActionAsAnAdmin()
    {
        $crawler = $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users/create');
        $buttonCrawlerNode = $crawler->selectButton('submit-btn');

        $form = $buttonCrawlerNode->form([
            'registration_form[username]'         => 'testUtilisateur',
            'registration_form[email]'            => 'test-utilisateur@todo-and-co.com',
            'registration_form[password][first]'  => 'TestUtilisateur1*',
            'registration_form[password][second]' => 'TestUtilisateur1*'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
    }

    /**
     * > This function tests that a user can logout of the application
     */
    public function testLogout()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/');
    }

    /**
     * This function tests that the login page is accessible to a user who is already logged in
     */
    public function testLoginPage()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/login');
    }

    /**
     * This function tests that the user can access the edit user page while logged in as a user.
     */
    public function testDisplayEditActionAsAUser()
    {
        $this->accessPageWhileLoggedIn(self::USER_EMAIL, '/users/2/edit', Response::HTTP_FORBIDDEN);
    }

    /**
     * This function tests that the user can access the edit user page while logged in as an admin.
     */
    public function testDisplayEditActionAsAnAdmin()
    {
        $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users/2/edit');
    }

    /**
     * It tests that an admin can edit a user
     */
    public function testSubmitFormEditActionAsAnAdmin()
    {
        $crawler = $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users/2/edit');
        $buttonCrawlerNode = $crawler->selectButton('submit-btn');

        $form = $buttonCrawlerNode->form([
            'user_form[username]' => 'Utilisateur',
            'user_form[email]'    => 'utilisateur@todo-and-co.com',
            'user_form[roles]'    => 'ROLE_ADMIN'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
    }

    /**
     * It tests that an admin can edit the password of a user
     */
    public function testSubmitFormEditPasswordActionAsAnAdmin()
    {
        $crawler = $this->accessPageWhileLoggedIn(self::ADMIN_EMAIL, '/users/2/edit');
        $buttonCrawlerNode = $crawler->selectButton('submit-btn-2');

        $form = $buttonCrawlerNode->form([
            'user_password_form[password][first]'  => 'NouveauMdp1*',
            'user_password_form[password][second]' => 'NouveauMdp1*',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
    }
}
