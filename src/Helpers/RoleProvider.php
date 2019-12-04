<?php

declare(strict_types=1);

namespace App\Helpers;

use Psr\Http\Message\ServerRequestInterface;
use Tkhamez\Slim\RoleAuth\RoleProviderInterface;

/**
 * A simple wrapper class that allows easier access to debugbar functions.
 */
class RoleProvider implements RoleProviderInterface
{

  /**
   * Returns roles from an authenticated user.
   *
   * Example: ['role.one', 'role.two']
   *
   * @param ServerRequestInterface $request
   * @return string[]
   */
  public function getRoles(ServerRequestInterface $request): array
  {
    if (auth()->check()) {
      return [auth()->user()->getRole()->alias()];
    } else {
      return ['guest'];
    }
  }
}
