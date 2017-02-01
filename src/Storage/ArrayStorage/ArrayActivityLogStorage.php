<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Storage\ArrayStorage;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sulu\Component\ActivityLog\Model\ActivityLog;
use Sulu\Component\ActivityLog\Model\ActivityLogInterface;
use Sulu\Component\ActivityLog\Storage\ActivityLogStorageInterface;

/**
 * Implementation of activity-log-storage using doctrine-collection.
 */
class ArrayActivityLogStorage implements ActivityLogStorageInterface
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Collection[]
     */
    protected $children = [];

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?: new ArrayCollection();

        $this->collection->forAll(
            function ($index, ActivityLogInterface $activityLog) {
                if (!$activityLog->getParent()) {
                    return true;
                }

                $this->buildChildren($activityLog);

                return true;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $uuid = null)
    {
        return new ActivityLog($type, $uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function find($uuid)
    {
        /** @var ActivityLogInterface $activityLog */
        foreach ($this->collection as $activityLog) {
            if ($uuid === $activityLog->getUuid()) {
                return $activityLog;
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($page = 1, $pageSize = null)
    {
        if ($pageSize) {
            return array_values($this->collection->slice(($page - 1) * $pageSize, $pageSize));
        }

        return $this->collection->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByParent(ActivityLogInterface $activityLog, $page = 1, $pageSize = null)
    {
        if (!array_key_exists($activityLog->getUuid(), $this->children)) {
            return [];
        }

        return array_values($this->children[$activityLog->getUuid()]->slice(($page - 1) * $pageSize, $pageSize));
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ActivityLogInterface $activityLog)
    {
        if (!$activityLog->getParent()) {
            $this->collection->add($activityLog);

            return $activityLog;
        }

        return $this->buildChildren($activityLog);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        // do nothing
    }

    private function buildChildren(ActivityLogInterface $activityLog)
    {
        $parentActivityLog = $activityLog->getParent();
        if (!array_key_exists($parentActivityLog->getUuid(), $this->children)) {
            $this->children[$parentActivityLog->getUuid()] = new ArrayCollection();
        }

        $this->children[$parentActivityLog->getUuid()][] = $activityLog;

        return $activityLog;
    }
}
