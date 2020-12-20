<?php


namespace DigitalAscetic\BaseUserBundle\Tests\Entity;


use DigitalAscetic\BaseUserBundle\Entity\AbstractBaseUser;
use Doctrine\ORM\Mapping as ORM;



/**
 * Class TestUser
 * @package DigitalAscetic\BaseUserBundle\Tests\Entity
 *
 * @ORM\Table(name="test_user")
 * @ORM\Entity()
 */
class TestUser extends AbstractBaseUser
{

}