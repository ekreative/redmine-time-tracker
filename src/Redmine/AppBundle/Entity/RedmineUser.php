<?php

namespace Redmine\AppBundle\Entity;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Class BaseUser
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Redmine\AppBundle\Entity\Repository\RedmineUserRepository")
 */
class RedmineUser implements UserInterface
{
    use CreateUpdateTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", unique=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", nullable=true)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", nullable=true)
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(name="redmineUserId", type="integer", nullable=true)
     */
    protected $redmineUserID;

    /**
     * @ORM\Column(name="redmineToken", type="string", nullable=true)
     */
    protected $redmineToken;

    /**
     * @var string
     */
    protected $plainPassword;

    public function __construct()
    {
        $this->setSalt(md5(uniqid()));
        $this->setRoles('ROLE_USER');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return RedmineUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RedmineUser
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return RedmineUser
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return [$this->roles];
    }

    /**
     * Set role
     *
     * @param Array $role
     * @return RedmineUser
     */
    public function setRoles($role)
    {
        $this->roles = $role;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return RedmineUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return RedmineUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return RedmineUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getRedmineUserID()
    {
        return $this->redmineUserID;
    }

    /**
     * @param mixed $redmineUserID
     * @return RedmineUser
     */
    public function setRedmineUserID($redmineUserID)
    {
        $this->redmineUserID = $redmineUserID;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRedmineToken()
    {
        return $this->redmineToken;
    }

    /**
     * @param mixed $redmineToken
     * @return RedmineUser
     */
    public function setRedmineToken($redmineToken)
    {
        $this->redmineToken = $redmineToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     * @return RedmineUser
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

       return $this;
    }
}
