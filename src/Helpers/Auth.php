<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entities\IUserEntity;
use App\Entities\User;

/**
 * A simple wrapper class that...
 */
class Auth
{
    private $signedin;
    private $user;

    public function init(bool $signedin, $user)
    {
        $this->signedin = $signedin;
        $this->user = $user;
    }

    public function check()
    {
        if ($this->signedin) {
            return $this->user->hasId();
        }
        return false;
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }
        return null;
    }

    public function userid()
    {
        if ($this->user) {
            return $this->user->id();
        }
        return 0;
    }

    public function role()
    {
        if ($this->user) {
            return $this->user->getRole();
        }
        return null;
    }

    public function can(IUserEntity $entity)
    {
        return $this->user === $entity->getUser();
    }
}
