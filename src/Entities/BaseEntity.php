<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity
{
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  public function id()
  {
    return $this->id;
  }

  public function getId()
  {
    return $this->id();
  }

  public function hasId()
  {
    return is_numeric($this->id);
  }

  public function equals(BaseEntity $entity)
  {
    return (get_class($this) == get_class($entity))
      && ($this->id() == $entity->id());
  }
}
