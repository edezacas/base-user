<?php


namespace EDC\BaseUserBundle\Tests\Functional;


use EDC\BaseUserBundle\Tests\Entity\TestUser;
use Symfony\Component\HttpFoundation\Response;

class ResetUserPasswordTest extends BaseTest
{
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

        $client->request('GET', '/reset_password/confirm/' . $token);

        $client->submitForm('Submit', [
            'edc_base_user_reset_password[password][first]' => '12345678',
            'edc_base_user_reset_password[password][second]' => '12345678',
        ]);

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

        $client = static::createClient();

        $client->request('GET', '/reset_password');

        $client->submitForm('Send', [
            'edc_base_user_reset_password_request[email]' => 'test@test.com',
        ]);

        return $client;
    }
}
