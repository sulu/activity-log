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
use Sulu\Component\ActivityLog\Activity\Activity;
use Sulu\Component\ActivityLog\ActivityLogger;
use Sulu\Component\ActivityLog\ActivityLoggerInterface;
use Sulu\Component\ActivityLog\Events\ActivityEvent;
use Sulu\Component\ActivityLog\Events\Events;
use Sulu\Component\ActivityLog\Storage\ActivityStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for activity-logger.
 */
class ActivityLoggerTest extends \PHPUnit_Framework_TestCase
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
     * @var ActivityLoggerInterface
     */
    protected $logger;

    public function setUp()
    {
        $this->storage = $this->prophesize(ActivityStorageInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->logger = new ActivityLogger($this->storage->reveal(), $this->eventDispatcher->reveal());
    }

    public function testFind()
    {
        $activity = new Activity('default');
        $this->storage->find($activity->getUuid())->willReturn($activity);

        $this->assertEquals($activity, $this->logger->find($activity->getUuid()));
    }

    public function testFindAll()
    {
        $activities = [new Activity('default'), new Activity('default')];
        $this->storage->findAll(2, 2)->willReturn($activities);

        $this->assertEquals($activities, $this->logger->findAll(2, 2));
    }

    public function testFindByParent()
    {
        $activity = new Activity('default');
        $activities = [new Activity('default'), new Activity('default')];
        $this->storage->findByParent($activity, 2, 2)->willReturn($activities);

        $this->assertEquals($activities, $this->logger->findByParent($activity, 2, 2));
    }

    public function testLog()
    {
        $activity = new Activity('default');

        $this->storage->persist($activity)->shouldBeCalledTimes(1);
        $this->eventDispatcher->dispatch(
            Events::PRE_LOG_ACTIVITY_EVENT,
            Argument::that(
                function (ActivityEvent $event) use ($activity) {
                    return $event->getActivity() === $activity;
                }
            )
        )->shouldBeCalledTimes(1);

        $this->assertEquals($activity, $this->logger->log($activity));

        return $activity;
    }

    public function testFlush()
    {
        $activities = [
            $this->logger->log(new Activity('default')),
            $this->logger->log(new Activity('default')),
        ];

        foreach ($activities as $activity) {
            $this->eventDispatcher->dispatch(
                Events::POST_LOG_ACTIVITY_EVENT,
                Argument::that(
                    function (ActivityEvent $event) use ($activity) {
                        return $activity === $event->getActivity();
                    }
                )
            )->shouldBeCalledTimes(1);
        }

        $this->storage->flush()->shouldBeCalledTimes(1);

        $this->logger->flush();
    }

    public function testFlushEmpty()
    {
        $this->eventDispatcher->dispatch(Argument::cetera())->shouldNotBeCalled();

        $this->logger->flush();
    }
}
