<?php


namespace DigitalAscetic\BaseUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractBaseUser
 * @package DigitalAscetic\BaseUserBundle\Entity
 */
abstract class AbstractBaseUser implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="json")
     */
    protected $roles = [];

    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;

    /**
     * The tokene generated at requesting reset password
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passwordRequestToken;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $enabled;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }


    /**
     * This method allow us to handle empty username field
     *
     * @return bool
     */
    public function isEmptyUsername(): bool
    {
        return empty($this->username);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole(string $role): self
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return $this
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    /**
     * @param string $passwordRequestToken
     * @return $this
     */
    public function setPasswordRequestToken(string $passwordRequestToken): self
    {
        $this->passwordRequestToken = $passwordRequestToken;

        return $this;
    }

    public function clearPasswordRequestToken(): self
    {
        $this->passwordRequestToken = null;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}