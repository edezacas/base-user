<?php


namespace DigitalAscetic\BaseUserBundle\Service;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPasswordEncoderService
{
    const SERVICE_NAME = 'digital_ascetic_base_user.service.password_encoder';

    /** @var UserPasswordEncoderInterface */
    private $userPasswordEncoder;

    /**
     * UserPasswordEncoderService constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function encodeUserPassword(UserInterface $user, $newPassword): string
    {
        return $this->userPasswordEncoder->encodePassword($user, $newPassword);
    }
}