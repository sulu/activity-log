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

use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Events\FlushActivityLogEvent;
use Sulu\Component\ActivityLog\Events\PersistActivityLogEvent;
use Sulu\Component\ActivityLog\Model\ActivityLogInterface;
use Sulu\Component\ActivityLog\Storage\ActivityLogStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Implementation for ActivityLoggerInterface.
 */
class ActivityLogger implements ActivityLoggerInterface
{
    /**
     * @var ActivityLogStorageInterface
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ActivityLogInterface[]
     */
    protected $newActivities = [];

    /**
     * @param ActivityLogStorageInterface $storage
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ActivityLogStorageInterface $storage, EventDispatcherInterface $eventDispatcher)
    {
        $this->storage = $storage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $uuid = null)
    {
        return $this->storage->create($type, $uuid);
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
    public function findAllWithSearch(
        $query = null,
        $fields = null,
        $page = 1,
        $pageSize = null,
        $sortColumn = null,
        $sortOrder = null
    ) {
        return $this->storage->findAllWithSearch($query, $fields, $page, $pageSize, $sortColumn, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountForAllWithSearch($query = null, $fields = null)
    {
        return $this->storage->getCountForAllWithSearch($query, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function findByParent(ActivityLogInterface $activityLog, $page = 1, $pageSize = null)
    {
        return $this->storage->findByParent($activityLog, $page, $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ActivityLogInterface $activityLog)
    {
        $this->newActivities[] = $activityLog;
        $this->storage->persist($activityLog);

        $this->eventDispatcher->dispatch(Events::PERSIST_ACTIVITY_LOG_EVENT, new PersistActivityLogEvent($activityLog));

        return $activityLog;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->storage->flush();

        if (0 === count($this->newActivities)) {
            return;
        }

        $this->eventDispatcher->dispatch(
            Events::FLUSH_ACTIVITY_LOG_EVENT,
            new FlushActivityLogEvent($this->newActivities)
        );

        $this->newActivities = [];
    }
}
