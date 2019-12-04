<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use App\Helpers\EntityManagerHelper;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseNamedEntity extends BaseEntity
{

  /**
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * @ORM\Column(type="string")
   */
  protected $alias;

  public function name()
  {
    return $this->name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    if ($this->name != $name) {
      $this->name = $name;
      $this->setAlias();
    }
  }

  public function alias()
  {
    return $this->alias;
  }

  public function getAlias()
  {
    return $this->alias;
  }

  protected function setAlias($alias = null)
  {
    if ($alias) {
      $this->alias = EntityManagerHelper::generateUniqueAlias($this, $alias);
    } else if ($this->name) {
      $this->alias = EntityManagerHelper::generateAlias($this, $this->name);
    }
  }

  public function __toString()
  {
    return $this->getName() ?: "";
  }
}
