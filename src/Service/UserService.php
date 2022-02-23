<?php


namespace EDC\BaseUserBundle\Service;


use EDC\BaseUserBundle\Entity\AbstractBaseUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService implements UserManagerInterface
{
    const SERVICE_NAME = 'edc_base_user.service.user';

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderService */
    private $userPasswordEncoder;

    /** @var string */
    private $class;

    /** @var bool */
    private $isUserEnabled;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderService $userPasswordEncoder
     * @param string $class
     * @param bool $isUserEnabled
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderService $userPasswordEncoder,
        string $class,
        bool $isUserEnabled
    ) {
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->class = $class;
        $this->isUserEnabled = $isUserEnabled;
    }

    public function findUserBy(array $criteria): ?UserInterface
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

    public function findUser(string $usernameOrEmail): ?UserInterface
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        $user->setPassword($newEncodedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function updatePassword(UserInterface $user, string $plainPassword): void
    {
        $newPasswordEncoded = $this->userPasswordEncoder->encodeUserPassword($user, $plainPassword);
        $user->setPassword($newPasswordEncoded);
    }

    public function updateUser(UserInterface $user, $flush = true): void
    {
        /** AbstractBaseUser $user  */
        if ($this->isAbstractUser($user)) {
            if (!empty($user->getPlainPassword())) {
                $this->updatePassword($user, $user->getPlainPassword());
            }

            if (is_null($user->isEnabled())) {
                $user->setEnabled($this->isUserEnabled);
            }
        }

        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }
    }

    private function isAbstractUser($entity)
    {
        return ($entity instanceof AbstractBaseUser);
    }
}
