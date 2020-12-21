<?php


namespace DigitalAscetic\BaseUserBundle\EventListener;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use DigitalAscetic\BaseUserBundle\Service\UserPasswordEncoderService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserDoctrineSubscriber implements EventSubscriberInterface
{
    const SERVICE_NAME = 'digital_ascetic_base_user.event_listener.user_doctrine';

    /** @var UserPasswordEncoderService */
    private $userPasswordEncoder;

    /**
     * UserDoctrineSubscriber constructor.
     * @param UserPasswordEncoderService $userPasswordEncoderService
     */
    public function __construct(UserPasswordEncoderService $userPasswordEncoderService)
    {
        $this->userPasswordEncoder = $userPasswordEncoderService;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if ($this->isUser($args->getObject())) {
            /** @var AbstractBaseUser $user */
            $user = $args->getObject();

            $this->updatePassword($user);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($this->isUser($args->getObject())) {
            /** @var AbstractBaseUser $user */
            $user = $args->getObject();
            $this->updatePassword($user);
        }
    }

    private function updatePassword(AbstractBaseUser $user)
    {
        if ($user->getPlainPassword()) {
            $newPasswordEncoded = $this->userPasswordEncoder->encodeUserPassword($user, $user->getPlainPassword());
            $user->setPassword($newPasswordEncoded);
        }
    }

    private function isUser($entity)
    {
        return ($entity instanceof AbstractBaseUser);
    }
}