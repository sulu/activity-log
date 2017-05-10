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
use Sulu\Component\ActivityLog\ActivityLogger;
use Sulu\Component\ActivityLog\ActivityLoggerInterface;
use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Events\FlushActivityLogEvent;
use Sulu\Component\ActivityLog\Events\PersistActivityLogEvent;
use Sulu\Component\ActivityLog\Model\ActivityLog;
use Sulu\Component\ActivityLog\Storage\ActivityLogStorageInterface;
use Sulu\Component\ActivityLog\Storage\ArrayStorage\ArrayActivityLogStorage;
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
     * @var ActivityLogStorageInterface
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
        $this->storage = new ArrayActivityLogStorage($this->collection);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->logger = new ActivityLogger($this->storage, $this->eventDispatcher->reveal());
    }

    public function testPersist()
    {
        $activityLog = new ActivityLog('default');
        $this->logger->persist($activityLog);

        $this->eventDispatcher->dispatch(Events::PERSIST_ACTIVITY_LOG_EVENT, Argument::type(PersistActivityLogEvent::class))
            ->shouldBeCalledTimes(1);

        $this->assertEquals([$activityLog], $this->collection->toArray());
    }

    public function testPersistMultipleTimes()
    {
        $this->logger->persist(new ActivityLog('default'));
        $this->logger->persist(new ActivityLog('default'));

        $this->eventDispatcher->dispatch(Events::PERSIST_ACTIVITY_LOG_EVENT, Argument::type(PersistActivityLogEvent::class))
            ->shouldBeCalledTimes(2);

        $this->assertCount(2, $this->collection->toArray());
    }

    public function testFlush()
    {
        $this->logger->persist(new ActivityLog('default'));
        $this->logger->persist(new ActivityLog('default'));

        $this->eventDispatcher->dispatch(Events::PERSIST_ACTIVITY_LOG_EVENT, Argument::type(PersistActivityLogEvent::class))
            ->shouldBeCalledTimes(2);
        $this->eventDispatcher->dispatch(Events::FLUSH_ACTIVITY_LOG_EVENT, Argument::type(FlushActivityLogEvent::class))
            ->shouldBeCalledTimes(1);

        $this->logger->flush();
    }

    public function testFind()
    {
        $activityLog = new ActivityLog('default');
        $this->collection->add($activityLog);

        $this->assertEquals($activityLog, $this->logger->find($activityLog->getUuid()));
    }

    public function testFindAll()
    {
        $this->collection->add(new ActivityLog('default'));
        $this->collection->add(new ActivityLog('default'));
        $this->collection->add(new ActivityLog('default'));

        $this->assertCount(3, $this->logger->findAll());
        $this->assertCount(2, $this->logger->findAll(1, 2));
        $this->assertCount(1, $this->logger->findAll(2, 2));
    }

    /**
     * Add three logs with a specific title and one without and check if only three items get returned by the method.
     */
    public function testFindAllWithSearch()
    {
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testA');
        $this->collection->add($activityLog);
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testB');
        $this->collection->add($activityLog);
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testC');
        $this->collection->add($activityLog);
        $this->collection->add(new ActivityLog('default'));

        $this->assertCount(3, $this->logger->findAllWithSearch('test'));
        $this->assertCount(2, $this->logger->findAllWithSearch('test', null, 1, 2));
        $this->assertCount(1, $this->logger->findAllWithSearch('test', null, 2, 2));
    }

    public function testGetCountForAllWithSearch()
    {
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testA');
        $this->collection->add($activityLog);
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testB');
        $this->collection->add($activityLog);
        $activityLog = new ActivityLog('default');
        $activityLog->setTitle('testC');
        $this->collection->add($activityLog);
        $this->collection->add(new ActivityLog('default'));

        $this->assertEquals(3, $this->logger->getCountForAllWithSearch('test'));
    }

    public function testFindByParent()
    {
        $parentActivityLog = new ActivityLog('default');
        $activityLog = new ActivityLog('default');
        $activityLog->setParent($parentActivityLog);

        $this->logger->persist($parentActivityLog);
        $this->logger->persist($activityLog);

        $this->assertCount(1, $this->logger->findByParent($parentActivityLog));
        $this->assertEquals([$activityLog], $this->logger->findByParent($parentActivityLog));
        $this->assertCount(0, $this->logger->findByParent($activityLog));
    }
}
