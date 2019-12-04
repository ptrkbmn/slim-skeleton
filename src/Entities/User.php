<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Description of User
 *
 * @ORM\Entity
 * @ORM\Table(name="user", indexes={@ORM\Index(name="user_idx", columns={"email"})})
 */
class User extends BaseEntity
{

    /**
     * @ORM\Column(type="string")
     */
    public $email;

    /**
     * @ORM\Column(type="string")
     */
    public $password;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users", cascade={"persist"})
     */
    protected $role;

    public function email()
    {
        return $this->email;
    }

    public function emailShort()
    {
        $index = stripos($this->email, "@");
        if ($index !== false) {
            return substr($this->email, 0, $index);
        }
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Returns the password hash.
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getCourses()
    {
        return $this->courses;
    }

    public function __toString()
    {
        return $this->email();
    }
}
