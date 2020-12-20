<?php


namespace DigitalAscetic\BaseUserBundle\Controller;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Service\ResetPasswordService;
use DigitalAscetic\BaseUserBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ResetController extends AbstractController
{

    /** @var UserService */
    private $userService;

    /** @var ResetPasswordService */
    private $resetPasswordService;

    public function __construct(UserService $userService, ResetPasswordService $resetPasswordService)
    {
        $this->userService = $userService;
        $this->resetPasswordService = $resetPasswordService;
    }


    public function resetPasswordRequest(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        if (!isset($body['email'])) {
            throw new \Exception('Email must be present to reset password');
        }

        $email = $body['email'];

        $user = $this->userService->findUserBy(['email' => $email]);

        if ($user instanceof AbstractBaseUser) {
            $this->resetPasswordService->requestResetPassword($user);
        }

        return new JsonResponse(['data' => 'ok']);
    }

    public function validateResetPasswordToken(string $token)
    {
        $valid = $this->resetPasswordService->validateResetPasswordToken($token);

        return new JsonResponse(['data' => ['isTokenValid' => $valid]]);
    }
}