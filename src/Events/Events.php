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
 * Container for activity-log event-names.
 */
final class Events
{
    const PERSIST_ACTIVITY_LOG_EVENT = 'sulu_activity_log.persist';
    const FLUSH_ACTIVITY_LOG_EVENT = 'sulu_activity_log.flush';

    /**
     * Private constructor to avoid instanciation of this class.
     */
    private function __construct()
    {
    }
}
