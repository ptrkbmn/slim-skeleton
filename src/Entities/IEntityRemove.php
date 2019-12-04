<?php

namespace App\Entities;

/**
 * Interface
 */
interface IEntityRemove
{
    public function beforeRemove(\Doctrine\ORM\EntityManager $em);
}
