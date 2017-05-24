<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface for user repository.
 */
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
