<?php


namespace EDC\BaseUserBundle\Controller;


use EDC\BaseUserBundle\Entity\AbstractBaseUser;
use EDC\BaseUserBundle\Form\ResetPasswordRequestType;
use EDC\BaseUserBundle\Form\ResetPasswordType;
use EDC\BaseUserBundle\Service\ResetPasswordService;
use EDC\BaseUserBundle\Service\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ResetController extends AbstractController
{

    /** @var UserManagerInterface */
    private $userManager;

    /** @var ResetPasswordService */
    private $resetPasswordService;

    /** @var Environment */
    private $twig;

    public function __construct(
        UserManagerInterface $userManager,
        ResetPasswordService $resetPasswordService,
        Environment          $twig
    )
    {
        $this->userManager = $userManager;
        $this->resetPasswordService = $resetPasswordService;
        $this->twig = $twig;
    }

    public function resetPasswordRequest(Request $request)
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $form = $formFactory->create(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userManager->findUserBy(['email' => $email]);

            if ($user instanceof AbstractBaseUser) {
                $this->resetPasswordService->requestResetPassword($user);
            }
        }

        return new Response($this->twig->render('@EDCBaseUser/Reset/reset_password.html.twig', ['form' => $form->createView()]));
    }

    public function resetPasswordConfirm(Request $request, string $token)
    {
        /** @var AbstractBaseUser|bool $user */
        $user = $this->resetPasswordService->validateResetPasswordToken($token);

        $error = null;

        if (!$user) {
            $error = 'USER_NOT_FOUND';
        }

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $form = $formFactory->create(ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if ($user) {
                $this->resetPasswordService->doResetUserPassword($user, $plainPassword);
                $this->redirectToRoute('edc_base_user_reset_password_request');
            }
        }

        return new Response($this->twig->render(
            '@EDCBaseUser/Reset/reset_password.html.twig',
            [
                'form' => $form->createView(),
                'error' => $error,
            ]
        ));
    }
}
