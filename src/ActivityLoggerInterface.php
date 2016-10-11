<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog;

use Sulu\Component\ActivityLog\Activity\ActivityInterface;

/**
 * Interface for activity-logger.
 */
interface ActivityLoggerInterface
{
    /**
     * Returns activity by given uuid.
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
     * Find activities by given parent.
     *
     * @param ActivityInterface $activity
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityInterface[]
     */
    public function findByParent(ActivityInterface $activity, $page = 1, $pageSize = null);

    /**
     * Stores activity.
     *
     * @param ActivityInterface $activity
     *
     * @return ActivityInterface
     */
    public function log(ActivityInterface $activity);

    /**
     * Flush storage.
     */
    public function flush();
}
