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

use Sulu\Component\ActivityLog\Model\ActivityLogInterface;

/**
 * Interface for activity-log-storage.
 */
interface ActivityLogStorageInterface
{
    /**
     * Find activity-log by uuid.
     *
     * @param string $uuid
     *
     * @return ActivityLogInterface
     */
    public function find($uuid);

    /**
     * Find all activity-logs.
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityLogInterface[]
     */
    public function findAll($page = 1, $pageSize = null);

    /**
     * Find activities by given parent activity-log.
     *
     * @param ActivityLogInterface $activityLog
     * @param int $page
     * @param int $pageSize
     *
     * @return ActivityLogInterface
     */
    public function findByParent(ActivityLogInterface $activityLog, $page = 1, $pageSize = null);

    /**
     * Persists activity-log.
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
