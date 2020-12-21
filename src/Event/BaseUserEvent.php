<?php


namespace DigitalAscetic\BaseUserBundle\Event;


use Symfony\Component\Security\Core\User\UserInterface;

class BaseUserEvent
{
    const USER_RESET_PASSWORD_REQUESTED = "USER_RESET_PASSWORD_REQUESTED";
    const USER_RESET_PASSWORD_SUCCESS = "USER_RESET_PASSWORD_SUCCESS";

    /** @var UserInterface */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}