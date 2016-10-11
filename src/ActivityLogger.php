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
use Sulu\Component\ActivityLog\Events\ActivityEvent;
use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Storage\ActivityStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Implementation for ActivityLoggerInterface.
 */
class ActivityLogger implements ActivityLoggerInterface
{
    /**
     * @var ActivityStorageInterface
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ActivityInterface[]
     */
    protected $newActivities = [];

    /**
     * @param ActivityStorageInterface $storage
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ActivityStorageInterface $storage, EventDispatcherInterface $eventDispatcher)
    {
        $this->storage = $storage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function find($uuid)
    {
        return $this->storage->find($uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($page = 1, $pageSize = null)
    {
        return $this->storage->findAll($page, $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function findByParent(ActivityInterface $activity, $page = 1, $pageSize = null)
    {
        return $this->storage->findByParent($activity, $page, $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function log(ActivityInterface $activity)
    {
        $this->newActivities[] = $activity;
        $this->storage->persist($activity);

        $this->eventDispatcher->dispatch(Events::PRE_LOG_ACTIVITY_EVENT, new ActivityEvent($activity));

        return $activity;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->storage->flush();

        foreach ($this->newActivities as $activity) {
            $this->eventDispatcher->dispatch(Events::POST_LOG_ACTIVITY_EVENT, new ActivityEvent($activity));
        }

        $this->newActivities = [];
    }
}
