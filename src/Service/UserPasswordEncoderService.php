<?php


namespace EDC\BaseUserBundle\Service;



use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserPasswordEncoderService
{
    const SERVICE_NAME = 'edc_base_user.service.password_encoder';

    /** @var UserPasswordHasherInterface */
    private $userPasswordEncoder;

    /**
     * UserPasswordEncoderService constructor.
     * @param UserPasswordHasherInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordHasherInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function encodeUserPassword(PasswordAuthenticatedUserInterface $user, $newPassword): string
    {
        return $this->userPasswordEncoder->hashPassword($user, $newPassword);
    }
}
