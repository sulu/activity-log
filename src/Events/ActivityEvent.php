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

use Sulu\Component\ActivityLog\Activity\ActivityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event-Args for activity events.
 */
class ActivityEvent extends Event
{
    /**
     * @var ActivityInterface
     */
    protected $activity;

    /**
     * @param ActivityInterface $activity
     */
    public function __construct(ActivityInterface $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Returns activity.
     *
     * @return ActivityInterface
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
