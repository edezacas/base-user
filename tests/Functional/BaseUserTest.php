<?php


namespace EDC\BaseUserBundle\Tests\Functional;


use Symfony\Component\HttpFoundation\Response;

class BaseUserTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDisabledUserLoginByUsername()
    {
        self::ensureKernelShutdown();

        $this->createUser(false);

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test", "_password" => "piripino9030"]
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testLoginByUsername()
    {
        self::ensureKernelShutdown();

        $this->createUser();

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test", "_password" => "piripino9030"]
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testLoginByEmail()
    {
        self::ensureKernelShutdown();

        $this->createUser();

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test@test.com", "_password" => "piripino9030"]
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testLoginFailure()
    {
        self::ensureKernelShutdown();

        $this->createUser();

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test@test.com", "_password" => "piripino9031"]
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }


    /**
     * Look at EDC\BaseUserBundle\Tests\EventListener\LoginSuccessListener
     */
    public function testLoginAndSerializer()
    {
        self::ensureKernelShutdown();

        $this->createUser();

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test@test.com", "_password" => "piripino9030"]
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $message = $client->getResponse()->getContent();
        $content = json_decode($message);
        $this->assertNotNull($content);
        $this->assertNotNull($content->user);
        $this->assertEquals(1, $content->user->id);
    }
}
