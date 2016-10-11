<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Tests\Functional;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Prophecy\Argument;
use Sulu\Component\ActivityLog\Activity\Activity;
use Sulu\Component\ActivityLog\ActivityLogger;
use Sulu\Component\ActivityLog\ActivityLoggerInterface;
use Sulu\Component\ActivityLog\Events\ActivityEvent;
use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Storage\ActivityStorageInterface;
use Sulu\Component\ActivityLog\Storage\ArrayStorage\ArrayActivityStorage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Functional tests for activity-logger.
 */
class ActivityLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ActivityStorageInterface
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ActivityLoggerInterface
     */
    protected $logger;

    public function setUp()
    {
        $this->collection = new ArrayCollection();
        $this->storage = new ArrayActivityStorage($this->collection);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->logger = new ActivityLogger($this->storage, $this->eventDispatcher->reveal());
    }

    public function testLog()
    {
        $activity = new Activity('default');
        $this->logger->log($activity);

        $this->eventDispatcher->dispatch(Events::PRE_LOG_ACTIVITY_EVENT, Argument::type(ActivityEvent::class))
            ->shouldBeCalledTimes(1);

        $this->assertEquals([$activity], $this->collection->toArray());
    }

    public function testLogMultipleTimes()
    {
        $this->logger->log(new Activity('default'));
        $this->logger->log(new Activity('default'));

        $this->eventDispatcher->dispatch(Events::PRE_LOG_ACTIVITY_EVENT, Argument::type(ActivityEvent::class))
            ->shouldBeCalledTimes(2);

        $this->assertCount(2, $this->collection->toArray());
    }

    public function testFlush()
    {
        $this->logger->log(new Activity('default'));
        $this->logger->log(new Activity('default'));

        $this->eventDispatcher->dispatch(Events::PRE_LOG_ACTIVITY_EVENT, Argument::type(ActivityEvent::class))
            ->shouldBeCalledTimes(2);
        $this->eventDispatcher->dispatch(Events::POST_LOG_ACTIVITY_EVENT, Argument::type(ActivityEvent::class))
            ->shouldBeCalledTimes(2);

        $this->logger->flush();
    }

    public function testFind()
    {
        $activity = new Activity('default');
        $this->collection->add($activity);

        $this->assertEquals($activity, $this->logger->find($activity->getUuid()));
    }

    public function testFindAll()
    {
        $this->collection->add(new Activity('default'));
        $this->collection->add(new Activity('default'));
        $this->collection->add(new Activity('default'));

        $this->assertCount(3, $this->logger->findAll());
        $this->assertCount(2, $this->logger->findAll(1, 2));
        $this->assertCount(1, $this->logger->findAll(2, 2));
    }

    public function testFindByParent()
    {
        $parentActivity = new Activity('default');
        $activity = new Activity('default');
        $activity->setParent($parentActivity);

        $this->logger->log($parentActivity);
        $this->logger->log($activity);

        $this->assertCount(1, $this->logger->findByParent($parentActivity));
        $this->assertEquals([$activity], $this->logger->findByParent($parentActivity));
        $this->assertCount(0, $this->logger->findByParent($activity));
    }
}
