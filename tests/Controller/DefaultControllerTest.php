<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $client;

    /**
     * This function creates a client that can be used to make requests to the application.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
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
     * It creates a client, sends a GET request to the homepage, and asserts that the response is
     * successful
     */
    public function testIndexAction(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
