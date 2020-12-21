<?php


namespace DigitalAscetic\BaseUserBundle\Service;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Event\BaseUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ResetPasswordService
{
    const SERVICE_NAME = 'digital_ascetic_base_user.service.reset_password';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $class;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var UserPasswordEncoderService */
    private $userPasswordEncoderService;

    /**
     * UserService constructor.
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

    public function clearPasswordRequestToken(AbstractBaseUser $user): void
    {
        $user->clearPasswordRequestToken();
        $this->em->persist($user);
        $this->em->flush();
    }

    public function doResetUserPassword(UserInterface $user, string $newPlainPassword): void
    {
        $newPasswordEncoded = $this->userPasswordEncoderService->encodeUserPassword($user, $newPlainPassword);

        $user->setPassword($newPasswordEncoded);
        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new BaseUserEvent($user), BaseUserEvent::USER_RESET_PASSWORD_SUCCESS);
    }
}