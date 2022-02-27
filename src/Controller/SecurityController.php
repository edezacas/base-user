<?php


namespace EDC\BaseUserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var AuthenticationUtils */
    private $authenticationUtils;

    /** @var Environment */
    private $twig;

    /**
     * SecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     * @param Environment $twig
     * @param CsrfTokenManagerInterface|null $csrfTokenManager
     */
    public function __construct(
        AuthenticationUtils       $authenticationUtils,
        Environment               $twig,
        CsrfTokenManagerInterface $csrfTokenManager = null
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function login()
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $csrfToken = $this->csrfTokenManager
            ? $this->csrfTokenManager->getToken('authenticate')->getValue()
            : null;

        return new Response($this->twig->render(
            '@EDCBaseUser/Security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'csrfToken' => $csrfToken,
            ]
        ));
    }

    public function check()
    {
        throw new \RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.'
        );
    }


    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
