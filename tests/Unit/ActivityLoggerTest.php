<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Tests\Unit;

use Prophecy\Argument;
use Sulu\Component\ActivityLog\ActivityLogger;
use Sulu\Component\ActivityLog\ActivityLoggerInterface;
use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Events\FlushActivityLogEvent;
use Sulu\Component\ActivityLog\Events\PersistActivityLogEvent;
use Sulu\Component\ActivityLog\Model\ActivityLog;
use Sulu\Component\ActivityLog\Storage\ActivityLogStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for activity-logger.
 */
class ActivityLoggerTest extends \PHPUnit_Framework_TestCase
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
     * @var ActivityLoggerInterface
     */
    protected $logger;

    public function setUp()
    {
        $this->storage = $this->prophesize(ActivityLogStorageInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->logger = new ActivityLogger($this->storage->reveal(), $this->eventDispatcher->reveal());
    }

    public function testFind()
    {
        $activityLog = ActivityLog::create('default');
        $this->storage->find($activityLog->getUuid())->willReturn($activityLog);

        $this->assertEquals($activityLog, $this->logger->find($activityLog->getUuid()));
    }

    public function testFindAll()
    {
        $activityLogs = [ActivityLog::create('default'), ActivityLog::create('default')];
        $this->storage->findAll(2, 2)->willReturn($activityLogs);

        $this->assertEquals($activityLogs, $this->logger->findAll(2, 2));
    }

    public function testFindByParent()
    {
        $activityLog = ActivityLog::create('default');
        $activityLogs = [ActivityLog::create('default'), ActivityLog::create('default')];
        $this->storage->findByParent($activityLog, 2, 2)->willReturn($activityLogs);

        $this->assertEquals($activityLogs, $this->logger->findByParent($activityLog, 2, 2));
    }

    public function testPersist()
    {
        $activityLog = ActivityLog::create('default');

        $this->storage->persist($activityLog)->shouldBeCalledTimes(1);
        $this->eventDispatcher->dispatch(
            Events::PERSIST_ACTIVITY_LOG_EVENT,
            Argument::that(
                function (PersistActivityLogEvent $event) use ($activityLog) {
                    return $event->getActivityLog() === $activityLog;
                }
            )
        )->shouldBeCalledTimes(1);

        $this->assertEquals($activityLog, $this->logger->persist($activityLog));

        return $activityLog;
    }

    public function testFlush()
    {
        $activityLogs = [
            $this->logger->persist(ActivityLog::create('default')),
            $this->logger->persist(ActivityLog::create('default')),
        ];

        $this->eventDispatcher->dispatch(
            Events::FLUSH_ACTIVITY_LOG_EVENT,
            Argument::that(
                function (FlushActivityLogEvent $event) use ($activityLogs) {
                    return $activityLogs === $event->getActivityLogs();
                }
            )
        )->shouldBeCalledTimes(1);

        $this->storage->flush()->shouldBeCalledTimes(1);

        $this->logger->flush();
    }

    public function testFlushEmpty()
    {
        $this->eventDispatcher->dispatch(Argument::cetera())->shouldNotBeCalled();

        $this->logger->flush();
    }
}
