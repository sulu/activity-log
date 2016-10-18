<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Tests\Unit\Storage\ArrayStorage;

use Doctrine\Common\Collections\ArrayCollection;
use Sulu\Component\ActivityLog\Model\ActivityLog;
use Sulu\Component\ActivityLog\Storage\ArrayStorage\ArrayActivityLogStorage;

/**
 * Tests for array-storage.
 */
class ArrayActivityStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $activityLog = ActivityLog::create('default');

        $collection = new ArrayCollection([ActivityLog::create('default'), $activityLog, ActivityLog::create('default')]);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals($activityLog, $storage->find($activityLog->getUuid()));
    }

    public function testFindNoResult()
    {
        $collection = new ArrayCollection([ActivityLog::create('default'), ActivityLog::create('default')]);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertNull($storage->find('123-123-123'));
    }

    public function testFindAll()
    {
        $activityLogs = [ActivityLog::create('default'), ActivityLog::create('default'), ActivityLog::create('default')];
        $collection = new ArrayCollection($activityLogs);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals($activityLogs, $storage->findAll());
        $this->assertEquals([$activityLogs[0], $activityLogs[1]], $storage->findAll(1, 2));
        $this->assertEquals([$activityLogs[2]], $storage->findAll(2, 2));
    }

    public function testFindByParent()
    {
        $activityLog = ActivityLog::create('default');
        $activityLogs = [ActivityLog::create('default'), ActivityLog::create('default'), ActivityLog::create('default')];

        foreach ($activityLogs as $childActivity) {
            $childActivity->setParent($activityLog);
        }

        $collection = new ArrayCollection($activityLogs);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals($activityLogs, $storage->findByParent($activityLog));
        $this->assertEquals([$activityLogs[0], $activityLogs[1]], $storage->findByParent($activityLog, 1, 2));
        $this->assertEquals([$activityLogs[2]], $storage->findByParent($activityLog, 2, 2));
    }

    public function testPersist()
    {
        $activityLog = ActivityLog::create('default');
        $storage = new ArrayActivityLogStorage();

        $this->assertEquals($activityLog, $storage->persist($activityLog));
        $this->assertEquals([$activityLog], $storage->findAll());
    }

    public function testPersistWithParent()
    {
        $parentActivityLog = ActivityLog::create('default');
        $activityLog = ActivityLog::create('default');
        $activityLog->setParent($parentActivityLog);

        $storage = new ArrayActivityLogStorage();

        $this->assertEquals($parentActivityLog, $storage->persist($parentActivityLog));
        $this->assertEquals($activityLog, $storage->persist($activityLog));
        $this->assertEquals([$parentActivityLog], $storage->findAll());
        $this->assertEquals([$activityLog], $storage->findByParent($parentActivityLog));
    }
}
