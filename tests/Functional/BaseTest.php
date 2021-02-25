<?php


namespace DigitalAscetic\BaseUserBundle\Tests\Functional;


use DigitalAscetic\BaseUserBundle\Service\UserManagerInterface;
use DigitalAscetic\BaseUserBundle\Tests\Entity\TestUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        $this->importDatabaseSchema();

        // gets the special container that allows fetching private services
        $container = self::$container;

        $this->userManager = $container->get(UserManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }

    public function createUser(bool $enabled = null)
    {
        /** TestUser $testUser */
        $testUser = new TestUser();
        $testUser->setUsername('test');
        $testUser->setEmail('test@test.com');

        $testUser->setPlainPassword('piripino9030');

        if (!is_null($enabled)) {
            $testUser->setEnabled($enabled);
        }

        $this->userManager->updateUser($testUser);
    }

    private function importDatabaseSchema()
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($this->em);
            $schemaTool->dropDatabase();
            $schemaTool->createSchema($metadata);
        }
    }
}