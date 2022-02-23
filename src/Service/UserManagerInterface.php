<?php

namespace EDC\BaseUserBundle\Service;

use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface extends PasswordUpgraderInterface
{
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void;

    public function findUser(string $usernameOrEmail): ?UserInterface;

    public function findUserBy(array $criteria): ?UserInterface;

    public function updatePassword(UserInterface $user, string $plainPassword): void;

    public function updateUser(UserInterface $user, $flush = true): void;
}
