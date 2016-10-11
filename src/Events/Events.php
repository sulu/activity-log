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

/**
 * Container for activity event-names.
 */
final class Events
{
    const PRE_LOG_ACTIVITY_EVENT = 'sulu_activity.pre_log';
    const POST_LOG_ACTIVITY_EVENT = 'sulu_activity.post_log';

    /**
     * Private constructor to avoid instanciation of this class.
     */
    private function __construct()
    {
    }
}
