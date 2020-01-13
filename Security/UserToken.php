<?php

namespace evaisse\SimpleAuthBundle\Security;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserToken
 * @package evaisse\SimpleAuthBundle\Security
 */
class UserToken implements TokenInterface
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Role[] $roles
     */
    protected $roles = array();

    /**
     * @var bool $authenticated
     */
    protected $authenticated = false;

    /**
     * @var array $attributes
     */
    protected $attributes = array();


    /**
     * @param mixed[] $roles a list of roles as string or Role instances
     * @throws \InvalidArgumentException
     */
    protected function setRoles($roles)
    {
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = new Role($role);
            } elseif (!$role instanceof RoleInterface) {
                throw new \InvalidArgumentException(sprintf('$roles must be an array of strings, or RoleInterface instances, but got %s.', gettype($role)));
            }
            $this->roles[] = $role;
        }
    }

    /**
     * @param User
     * @throws \InvalidArgumentException
     */
    public function __construct($user)
    {
        $this->setUser($user);
        // If the user has roles, consider it authenticated
        $this->setAuthenticated(true);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return '.';
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($authenticated)
    {
        $this->authenticated = (bool) $authenticated;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        if ($this->getUser() instanceof UserInterface) {
            $this->getUser()->eraseCredentials();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user in the token.
     *
     * The user can be a UserInterface instance, or an object implementing
     * a __toString method or the username as a regular string.
     *
     * @param string|object $user The user
     *
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function setUser($user)
    {
        if (!($user instanceof UserInterface || (is_object($user) && method_exists($user, '__toString')) || is_string($user))) {
            throw new \InvalidArgumentException('$user must be an instanceof UserInterface, an object implementing a __toString method, or a primitive string.');
        }

        $this->setRoles($user->getRoles());

        if (null === $this->user) {
            $changed = false;
        } elseif ($this->user instanceof UserInterface) {
            if (!$user instanceof UserInterface) {
                $changed = true;
            } else {
                $changed = $this->hasUserChanged($user);
            }
        } elseif ($user instanceof UserInterface) {
            $changed = true;
        } else {
            $changed = (string) $this->user !== (string) $user;
        }

        if ($changed) {
            $this->setAuthenticated(false);
        }

        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                is_object($this->user) ? clone $this->user : $this->user,
                $this->authenticated,
                $this->getRoles(),
                $this->attributes,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $roles = $this->user->getRoles();
        foreach ($roles as $k => $role) {
            if (is_string($role)) {
                $roles[$k] = new Role($role);
            }
        }

        return $roles;
    }


    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->user, $this->authenticated, $this->roles, $this->attributes) = unserialize($serialized);
    }

    /**
     * Returns the token attributes.
     *
     * @return array The token attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the token attributes.
     *
     * @param array $attributes The token attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns true if the attribute exists.
     *
     * @param string $name The attribute name
     *
     * @return bool true if the attribute exists, false otherwise
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Returns an attribute value.
     *
     * @param string $name The attribute name
     *
     * @return mixed The attribute value
     *
     * @throws \InvalidArgumentException When attribute doesn't exist for this token
     */
    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new \InvalidArgumentException(sprintf('This token has no "%s" attribute.', $name));
        }

        return $this->attributes[$name];
    }

    /**
     * Sets an attribute.
     *
     * @param string $name  The attribute name
     * @param mixed  $value The attribute value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $class = get_class($this);
        $class = substr($class, strrpos($class, '\\') + 1);

        $roles = array();
        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }

        return sprintf('%s(user="%s", authenticated=%s, roles="%s")', $class, $this->getUsername(), json_encode($this->authenticated), implode(', ', $roles));
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        if ($this->user instanceof UserInterface) {
            return $this->user->getUsername();
        }

        return (string) $this->user;
    }

    /**
     * @param UserInterface $user
     * @return bool
     * @throws \BadMethodCallException
     */
    public function hasUserChanged(UserInterface $user)
    {
        if (!($this->user instanceof UserInterface)) {
            throw new \BadMethodCallException('Method "hasUserChanged" should be called when current user class is instance of "UserInterface".');
        }

        if ($this->user instanceof EquatableInterface) {
            return !(bool) $this->user->isEqualTo($user);
        }

        if ($this->user->getPassword() !== $user->getPassword()) {
            return true;
        }

        if ($this->user->getSalt() !== $user->getSalt()) {
            return true;
        }

        if ($this->user->getUserName() !== $user->getUsername()) {
            return true;
        }

        if ($this->user instanceof AdvancedUserInterface && $user instanceof AdvancedUserInterface) {
            if ($this->user->isAccountNonExpired() !== $user->isAccountNonExpired()) {
                return true;
            }

            if ($this->user->isAccountNonLocked() !== $user->isAccountNonLocked()) {
                return true;
            }

            if ($this->user->isCredentialsNonExpired() !== $user->isCredentialsNonExpired()) {
                return true;
            }

            if ($this->user->isEnabled() !== $user->isEnabled()) {
                return true;
            }
        } elseif ($this->user instanceof AdvancedUserInterface xor $user instanceof AdvancedUserInterface) {
            return true;
        }

        return false;
    }
}
