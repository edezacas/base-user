<?php


namespace DigitalAscetic\BaseUserBundle\Security;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Service\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    const SERVICE_NAME = 'digital_ascetic_base_user.security.user_provider';

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * UserProvider constructor.
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }


    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        $this->userManager->upgradePassword($user, $newEncodedPassword);
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUser($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (!$this->supportsClass($user)) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class)
    {
        return AbstractBaseUser::class === $class || is_subclass_of($class, AbstractBaseUser::class);
    }
}