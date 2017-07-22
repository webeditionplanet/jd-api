<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class AuthUser
 *
 * @ORM\Entity()
 * @ORM\Table(name="auth_user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="auth_user_login",columns={"login"})}
 * )
 * @UniqueEntity("login")
 */
class AuthUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\Type(
     *     type="string"
     * )
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $login;

    /**
     * @var string
     * @ORM\Column(
     *     type="string"
     * )
     */
    private $password;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 4,
     *      max = 50
     * )
     */
    private $plainPassword;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AuthUser
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     *
     * @return AuthUser
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     *
     * @return AuthUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return AuthUser
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->login;
    }

    /**
     * Erase plain password, but it's necessary to compare both
     */
    public function eraseCredentials()
    {
        // Suppression des données sensibles
        $this->plainPassword = null;
    }
}