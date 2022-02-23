<?php


namespace EDC\BaseUserBundle\Tests\Functional;


use EDC\BaseUserBundle\Tests\Entity\TestUser;
use Symfony\Component\HttpFoundation\Response;

class ResetUserPasswordTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testResetPasswordRequest()
    {
        $this->createUser();

        $client = $this->requestResetPassword();

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        /** @var TestUser $user */
        $user = $this->getEm()->getRepository(TestUser::class)->findOneBy(['email' => 'test@test.com']);

        $token = $user->getPasswordRequestToken();

        $this->assertNotNull($token);
    }

    public function testResetPasswordConfirm()
    {
        $this->createUser();

        $client = $this->requestResetPassword();

        /** @var TestUser $user */
        $user = $this->getEm()->getRepository(TestUser::class)->findOneBy(['email' => 'test@test.com']);

        $token = $user->getPasswordRequestToken();

        $crawler = $client->request('GET', '/reset_password/confirm/'.$token);

        // you can also pass an array of field values that overrides the default ones
        $form = $crawler->filter('form')->form(
            [
                'edc_base_user_reset_password[password][first]' => '12345678',
                'edc_base_user_reset_password[password][second]' => '12345678',
            ]
        );

        // submit the Form object
        $client->submit($form);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->getEm()->clear();

        /** @var TestUser $user */
        $user = $this->getEm()->getRepository(TestUser::class)->findOneBy(['email' => 'test@test.com']);

        $token = $user->getPasswordRequestToken();

        $this->assertNull($token);
    }

    private function requestResetPassword()
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        $crawler = $client->request('GET', '/reset_password');

        // you can also pass an array of field values that overrides the default ones
        $form = $crawler->filter('form')->form(
            [
                'edc_base_user_reset_password_request[email]' => 'test@test.com',
            ]
        );

        // submit the Form object
        $client->submit($form);

        return $client;
    }
}
