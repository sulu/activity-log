<?php

namespace Sulu\Component\ActivityLog\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserRepositoryInterface
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
