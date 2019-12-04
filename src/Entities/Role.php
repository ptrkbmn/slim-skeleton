<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Role
 *
 * @ORM\Entity
 * @ORM\Table(name="role", indexes={@ORM\Index(name="role_idx", columns={"alias"})})
 */
class Role extends BaseNamedEntity
{
  /**
   * @ORM\OneToMany(targetEntity="User", mappedBy="role")
   */
  protected $users;

  public function __construct($name)
  {
    $this->setName($name);
  }
}
