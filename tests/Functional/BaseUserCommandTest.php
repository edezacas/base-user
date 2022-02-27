<?php


namespace EDC\BaseUserBundle\Tests\Functional;


use EDC\BaseUserBundle\Tests\Entity\TestUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class BaseUserCommandTest extends BaseTest
{
    public function testUpdatePasswordCommandSuccess()
    {
        $this->createUser(true);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('edc_base_user:update-user-password');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'identifier' => 'test',
            'password' => '12345678'
        ]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertEquals('Username test has been updated with 12345678 password', $output);
    }

    public function testUpdatePasswordCommandAndLogin()
    {
        $this->createUser(true);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('edc_base_user:update-user-password');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'identifier' => 'test',
            'password' => '12345678'
        ]);

        self::ensureKernelShutdown();

        $client = self::createClient();

        $client->request(
            'POST',
            '/login_check',
            ['_username' => "test", "_password" => "12345678"]
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testUpdatePasswordCommandFail()
    {
        $this->createUser(true);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('edc_base_user:update-user-password');
        $commandTester = new CommandTester($command);

        $code = $commandTester->execute([
            'identifier' => 'fake',
            'password' => '12345678'
        ]);

        $this->assertEquals(404, $code);
    }
}
