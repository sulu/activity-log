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
use Sulu\Component\ActivityLog\Activity\ActivityInterface;
use Sulu\Component\ActivityLog\Storage\ActivityStorageInterface;

/**
 * Implementation of activity-storage using doctrine-collection.
 */
class ArrayActivityStorage implements ActivityStorageInterface
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
            function ($index, ActivityInterface $activity) {
                if (!$activity->getParent()) {
                    return true;
                }

                $this->buildChildren($activity);

                return true;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function find($uuid)
    {
        /** @var ActivityInterface $activity */
        foreach ($this->collection as $activity) {
            if ($uuid === $activity->getUuid()) {
                return $activity;
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
    public function findByParent(ActivityInterface $activity, $page = 1, $pageSize = null)
    {
        if (!array_key_exists($activity->getUuid(), $this->children)) {
            return [];
        }

        return array_values($this->children[$activity->getUuid()]->slice(($page - 1) * $pageSize, $pageSize));
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ActivityInterface $activity)
    {
        if (!$activity->getParent()) {
            $this->collection->add($activity);

            return $activity;
        }

        return $this->buildChildren($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        // do nothing
    }

    private function buildChildren(ActivityInterface $activity)
    {
        $parentActivity = $activity->getParent();
        if (!array_key_exists($parentActivity->getUuid(), $this->children)) {
            $this->children[$parentActivity->getUuid()] = new ArrayCollection();
        }

        $this->children[$parentActivity->getUuid()][] = $activity;

        return $activity;
    }
}
