<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Events;

use Sulu\Component\ActivityLog\Model\ActivityLogInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event-Args for activity-log persist-event.
 */
class FlushActivityLogEvent extends Event
{
    /**
     * @var ActivityLogInterface[]
     */
    private $activityLogs;

    /**
     * @param ActivityLogInterface[] $activityLogs
     */
    public function __construct(array $activityLogs)
    {
        $this->activityLogs = $activityLogs;
    }

    /**
     * Returns activityLogs.
     *
     * @return ActivityLogInterface[]
     */
    public function getActivityLogs()
    {
        return $this->activityLogs;
    }
}
