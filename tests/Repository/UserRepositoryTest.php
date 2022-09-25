<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserRepositoryTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /**
     * It gets the database tool from the container and assigns it to a property
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
    }

    /**
     * The `tearDown()` function is called after each test is run. It is used to clean up the
     * environment after each test
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    /**
     * It loads the UserFixtures, then checks that the UserRepository::count() method returns 2
     */
    public function testCount(): void
    {
        self::bootKernel();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->assertEquals(2, $userRepository->count([]));
    }
}
