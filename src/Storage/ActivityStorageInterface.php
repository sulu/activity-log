<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Storage;

use Sulu\Component\ActivityLog\Activity\ActivityInterface;

/**
 * Interface for activity-storage.
 */
interface ActivityStorageInterface
{
    /**
     * Find activity by uuid.
     *
     * @param string $uuid
     *
     * @return ActivityInterface
     */
    public function find($uuid);

    /**
     * Find all activities.
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityInterface[]
     */
    public function findAll($page = 1, $pageSize = null);

    /**
     * Find activities by given parent activity.
     *
     * @param ActivityInterface $activity
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityInterface
     */
    public function findByParent(ActivityInterface $activity, $page = 1, $pageSize = null);

    /**
     * Persists activity.
     *
     * @param ActivityInterface $activity
     *
     * @return ActivityInterface
     */
    public function persist(ActivityInterface $activity);

    /**
     * Flush storage.
     */
    public function flush();
}
