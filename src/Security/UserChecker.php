<?php


namespace EDC\BaseUserBundle\Security;


use EDC\BaseUserBundle\Entity\AbstractBaseUser;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    const SERVICE_NAME = 'edc_base_user.security.user_checker';

    /** @var UserCheckerInterface */
    private $innerUserChecker;

    /**
     * UserChecker constructor.
     * @param UserCheckerInterface $innerUserChecker
     */
    public function __construct(UserCheckerInterface $innerUserChecker)
    {
        $this->innerUserChecker = $innerUserChecker;
    }


    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AbstractBaseUser) {
            return;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }

        $this->innerUserChecker->checkPreAuth($user);
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof AbstractBaseUser) {
            return;
        }

        if (!$user->isEnabled()) {
            throw new AccessDeniedException('Your user account is not enabled.');
        }

        $this->innerUserChecker->checkPostAuth($user);
    }
}
