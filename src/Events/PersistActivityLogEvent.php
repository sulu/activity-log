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
class PersistActivityLogEvent extends Event
{
    /**
     * @var ActivityLogInterface
     */
    protected $activityLog;

    /**
     * @param ActivityLogInterface $activityLog
     */
    public function __construct(ActivityLogInterface $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Returns activity-log.
     *
     * @return ActivityLogInterface
     */
    public function getActivityLog()
    {
        return $this->activityLog;
    }
}
