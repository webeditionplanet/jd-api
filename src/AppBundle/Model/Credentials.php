<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Credentials
 */
class Credentials
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string"
     * )
     */
    private $login;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string"
     * )
     */
    private $password;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return Credentials
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return Credentials
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}