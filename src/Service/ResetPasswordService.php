<?php


namespace EDC\BaseUserBundle\Service;


use EDC\BaseUserBundle\Entity\AbstractBaseUser;
use EDC\BaseUserBundle\Event\BaseUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResetPasswordService
{
    const SERVICE_NAME = 'edc_base_user.service.reset_password';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $class;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var UserPasswordEncoderService */
    private $userPasswordEncoderService;

    /**
     * ResetPasswordService constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param UserPasswordEncoderService $userPasswordEncoderService
     * @param string $class
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderService $userPasswordEncoderService,
        string $class
    ) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->userPasswordEncoderService = $userPasswordEncoderService;
        $this->class = $class;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateResetPasswordToken()
    {
        return bin2hex(random_bytes(32));
    }


    /**
     * This method generate a new password token for requested user
     *
     * @param AbstractBaseUser $user
     * @throws \Exception
     */
    public function requestResetPassword(AbstractBaseUser $user): void
    {
        /** @var AbstractBaseUser $user */
        $user = $this->em->getRepository($this->class)->findOneBy(['id' => $user->getId()]);
        $token = $this->generateResetPasswordToken();
        $user->setPasswordRequestToken($token);
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new BaseUserEvent($user), BaseUserEvent::USER_RESET_PASSWORD_REQUESTED);
    }


    /**
     * Validate is token is valid and assigned to some user
     *
     * @param string $token
     * @return AbstractBaseUser|bool
     */
    public function validateResetPasswordToken(string $token)
    {
        /** @var AbstractBaseUser $user */
        $user = $this->em->getRepository($this->class)->findOneBy(['passwordRequestToken' => $token]);

        if (isset($user)) {
            return $user;
        }

        return false;
    }

    /**
     * Performs reset user password and clear passwordRequestToken
     *
     * @param AbstractBaseUser $user
     * @param string $newPlainPassword
     */
    public function doResetUserPassword(AbstractBaseUser $user, string $newPlainPassword): void
    {
        $newPasswordEncoded = $this->userPasswordEncoderService->encodeUserPassword($user, $newPlainPassword);

        $user->setPassword($newPasswordEncoded);
        $user->clearPasswordRequestToken();
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new BaseUserEvent($user), BaseUserEvent::USER_RESET_PASSWORD_SUCCESS);
    }
}
