<?php


namespace DigitalAscetic\BaseUserBundle\Service;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Event\BaseUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResetPasswordService
{
    const SERVICE_NAME = 'digital_ascetic_base_user.service.reset_password';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $class;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param string $class
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        string $class
    ) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
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

    public function validateResetPasswordToken(string $token): bool
    {
        /** @var AbstractBaseUser $user */
        $user = $this->em->getRepository($this->class)->findOneBy(['passwordRequestToken' => $token]);

        if (isset($user)) {
            $user->clearPasswordRequestToken();
            $this->em->persist($user);
            $this->em->flush();

            return true;
        }

        return false;
    }
}