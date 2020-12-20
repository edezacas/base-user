<?php


namespace DigitalAscetic\BaseUserBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    const SERVICE_NAME = 'digital_ascetic_base_user.service.user';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $class;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     * @param string $class
     */
    public function __construct(
        EntityManagerInterface $em,
        string $class
    ) {
        $this->em = $em;
        $this->class = $class;
    }

    public function findUserBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

    public function findUserByEmail($email)
    {
        return $this->findUserBy(['email' => $email]);
    }

    public function findUserByUsername($username)
    {
        return $this->findUserBy(['username' => $username]);
    }

    public function findUser(string $usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function updatePassword(UserInterface $user, string $newEncodedPassword)
    {
        $user->setPassword($newEncodedPassword);
        $this->em->persist($user);
        $this->em->flush();;
    }
}