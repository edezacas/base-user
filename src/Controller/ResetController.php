<?php


namespace DigitalAscetic\BaseUserBundle\Controller;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Form\ResetPasswordRequestType;
use DigitalAscetic\BaseUserBundle\Form\ResetPasswordType;
use DigitalAscetic\BaseUserBundle\Service\ResetPasswordService;
use DigitalAscetic\BaseUserBundle\Service\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ResetController extends AbstractController
{

    /** @var UserManagerInterface */
    private $userManager;

    /** @var ResetPasswordService */
    private $resetPasswordService;

    public function __construct(
        UserManagerInterface $userManager,
        ResetPasswordService $resetPasswordService
    ) {
        $this->userManager = $userManager;
        $this->resetPasswordService = $resetPasswordService;
    }

    public function resetPasswordRequest(Request $request)
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userManager->findUserBy(['email' => $email]);

            if ($user instanceof AbstractBaseUser) {
                $this->resetPasswordService->requestResetPassword($user);
            }
        }

        return $this->render('@DigitalAsceticBaseUser/Reset/reset_password.html.twig', ['form' => $form->createView()]);
    }

    public function resetPasswordConfirm(Request $request, string $token)
    {
        /** @var AbstractBaseUser|bool $user */
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