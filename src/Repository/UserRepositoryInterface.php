<?php

namespace Sulu\Component\ActivityLog\Repository;

use Sulu\Component\Security\Authentication\UserInterface;
use Sulu\Component\Security\Authentication\UserRepositoryInterface as BaseUserRepositoryInterface;

interface UserRepositoryInterface extends BaseUserRepositoryInterface
{
    /**
     * Find a user by id.
     *
     * @param string $id
     *
     * @return UserInterface
     */
    public function findOneById($id);
}
