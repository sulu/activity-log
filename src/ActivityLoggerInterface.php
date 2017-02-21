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

use Sulu\Component\ActivityLog\Model\ActivityLogInterface;

/**
 * Interface for activity-logger.
 */
interface ActivityLoggerInterface
{
    /**
     * Create new activity-log.
     *
     * @param string $type
     * @param string $uuid
     *
     * @return ActivityLogInterface
     */
    public function create($type, $uuid = null);

    /**
     * Returns activity-log by given uuid.
     *
     * @param string $uuid
     *
     * @return ActivityLogInterface
     */
    public function find($uuid);

    /**
     * Find all activities.
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityLogInterface[]
     */
    public function findAll($page = 1, $pageSize = null);

    /**
     * Find activities by given parent.
     *
     * @param ActivityLogInterface $activityLog
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityLogInterface[]
     */
    public function findByParent(ActivityLogInterface $activityLog, $page = 1, $pageSize = null);

    /**
     * Stores activity-log.
     *
     * @param ActivityLogInterface $activityLog
     *
     * @return ActivityLogInterface
     */
    public function persist(ActivityLogInterface $activityLog);

    /**
     * Flush storage.
     */
    public function flush();
}
