<?php


namespace DigitalAscetic\BaseUserBundle\Tests\Functional;


use DigitalAscetic\BaseUserBundle\Tests\Entity\TestUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetUserPasswordTest extends WebTestCase
{

    /**
     * @var EntityManagerInterface
     */
    private $em;


    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        $this->importDatabaseSchema();

        $testUser = new TestUser();
        $testUser->setUsername('test');
        $testUser->setEmail('test@test.com');

        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = static::$kernel->getContainer()
            ->get('security.password_encoder');

        $testUser->setPassword($encoder->encodePassword($testUser, 'piripino9030'));
        $this->em->persist($testUser);
        $this->em->flush();
    }

    public function testRequestResetPassword()
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        $client->request(
            'POST',
            '/reset_password/request',
            [],
            [],
            [],
            json_encode(array('email' => 'test@test.com'))
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $message = $client->getResponse()->getContent();
        $content = json_decode($message);
        $this->assertNotNull($content);
        $this->assertEquals('ok', $content->data);
    }

    public function testResetPasswordCheck()
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        $client->request(
            'POST',
            '/reset_password/request',
            [],
            [],
            [],
            json_encode(array('email' => 'test@test.com'))
        );

        /** @var TestUser $user */
        $user = $this->em->getRepository(TestUser::class)->findOneBy(['email' => 'test@test.com']);

        $token = $user->getPasswordRequestToken();

        $client->request(
            'GET',
            '/reset_password/validate/'.$token
        );

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $message = $client->getResponse()->getContent();
        $content = json_decode($message);
        $this->assertNotNull($content);
        $this->assertTrue($content->data->isTokenValid);
    }

    public function testErrorRequestResetPasswword()
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        $client->request(
            'POST',
            '/reset_password/request',
            [],
            [],
            [],
            json_encode(array('username' => 'test'))
        );
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
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