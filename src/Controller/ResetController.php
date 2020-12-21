<?php


namespace DigitalAscetic\BaseUserBundle\Controller;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Form\ResetPasswordRequestType;
use DigitalAscetic\BaseUserBundle\Form\ResetPasswordType;
use DigitalAscetic\BaseUserBundle\Service\ResetPasswordService;
use DigitalAscetic\BaseUserBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ResetController extends AbstractController
{

    /** @var UserService */
    private $userService;

    /** @var ResetPasswordService */
    private $resetPasswordService;

    public function __construct(
        UserService $userService,
        ResetPasswordService $resetPasswordService
    ) {
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
        $user = $this->resetPasswordService->validateResetPasswordToken($token);
        $valid = false;

        if ($user) {
            $this->resetPasswordService->clearPasswordRequestToken($user);
            $valid = true;
        }

        return new JsonResponse(['data' => ['isTokenValid' => $valid]]);
    }

    public function resetPassword(Request $request)
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userService->findUserBy(['email' => $email]);

            if ($user instanceof AbstractBaseUser) {
                $this->resetPasswordService->requestResetPassword($user);
            }
        }

        return $this->render('@DigitalAsceticBaseUser/Reset/reset_password.html.twig', ['form' => $form->createView()]);
    }

    public function resetPasswordConfirm(Request $request, string $token)
    {
        /** @var UserInterface $user */
        $user = $this->resetPasswordService->validateResetPasswordToken($token);

        $error = null;

        if (!$user) {
            $error = 'USER_NOT_FOUND';
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if ($user) {
                $this->resetPasswordService->doResetUserPassword($user, $plainPassword);
                $this->redirectToRoute('digital_ascetic_base_user_reset_password_request');
            }
        }

        return $this->render(
            '@DigitalAsceticBaseUser/Reset/reset_password.html.twig',
            [
                'form' => $form->createView(),
                'error' => $error,
            ]
        );
    }
}