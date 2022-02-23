<?php


namespace EDC\BaseUserBundle\Tests\Entity;


use EDC\BaseUserBundle\Entity\AbstractBaseUser;
use Doctrine\ORM\Mapping as ORM;



/**
 * Class TestUser
 * @package EDC\BaseUserBundle\Tests\Entity
 *
 * @ORM\Table(name="test_user")
 * @ORM\Entity()
 */
class TestUser extends AbstractBaseUser
{

}
