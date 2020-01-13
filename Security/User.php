<?php
/**
 * user entity
 */
namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Class User
 * @package evaisse\SimpleAuthBundle\Security
 */
class User implements UserInterface, EquatableInterface, AdvancedUserInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * arbitrary data set to attach to current security user
     * @var array
     */
    protected $data = [];


    /**
     * @param string $username
     * @param array  $data     arbitrary data set to attach to current security user
     * @param array  $roles
     */
    public function __construct($username, array $data = [], array $roles = [])
    {
        $this->username = $username;
        $this->roles = empty($roles) ? ['ROLE_USER'] : $roles;
        $this->data = $data;
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
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = array_unique($roles);
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $roles = $this->getRoles();
        if (!in_array($role, $roles)) {
            $roles[] = $role;
        }
        $this->setRoles($roles);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return md5($this->password);
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return substr(md5($this->username), 0, 10);
    }


    /**
     * @param string $key
     * @param mixed  $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        return $this->data[$key] ?? $defaultValue;
    }


    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set($key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->username;
    }


    /**
     * Erase credentials
     * @return bool
     */
    public function eraseCredentials(): bool
    {
        return true;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if ($user instanceof User) {
            // Check that the roles are the same, in any order
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            if ($this->username !== $user->getUsername()) {
                return false;
            }
            return $isEqual;
        }

        return false;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled(): bool
    {
        return true;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * Check if user as a specific role
     * @param string $role
     * @return boolean
     */
    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }


}
