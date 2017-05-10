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
use Sulu\Component\ActivityLog\Model\ActivityLogInterface;
use Sulu\Component\ActivityLog\Storage\ArrayStorage\ArrayActivityLogStorage;

/**
 * Tests for array-storage.
 */
class ArrayActivityStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $storage = new ArrayActivityLogStorage();

        $activityLog = $storage->create('default');
        $this->assertInstanceOf(ActivityLogInterface::class, $activityLog);
        $this->assertEquals('default', $activityLog->getType());
        $this->assertNotNull($activityLog->getUuid());
    }

    public function testCreateWithUuid()
    {
        $storage = new ArrayActivityLogStorage();

        $activityLog = $storage->create('default', '123-123-123');
        $this->assertInstanceOf(ActivityLogInterface::class, $activityLog);
        $this->assertEquals('default', $activityLog->getType());
        $this->assertEquals('123-123-123', $activityLog->getUuid());
    }

    public function testFind()
    {
        $activityLog = new ActivityLog('default');

        $collection = new ArrayCollection([new ActivityLog('default'), $activityLog, new ActivityLog('default')]);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals($activityLog, $storage->find($activityLog->getUuid()));
    }

    public function testFindNoResult()
    {
        $collection = new ArrayCollection([new ActivityLog('default'), new ActivityLog('default')]);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertNull($storage->find('123-123-123'));
    }

    public function testFindAll()
    {
        $activityLogs = [new ActivityLog('default'), new ActivityLog('default'), new ActivityLog('default')];
        $collection = new ArrayCollection($activityLogs);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals($activityLogs, $storage->findAll());
        $this->assertEquals([$activityLogs[0], $activityLogs[1]], $storage->findAll(1, 2));
        $this->assertEquals([$activityLogs[2]], $storage->findAll(2, 2));
    }

    public function testFindAllWithSearch()
    {
        $activityLogs = [
            new ActivityLog('default'),
            new ActivityLog('default'),
            new ActivityLog('default'),
            new ActivityLog('default'),
        ];
        $activityLogs[0]->setTitle('testA');
        $activityLogs[2]->setTitle('testC');
        $activityLogs[3]->setTitle('testB');
        $collection = new ArrayCollection($activityLogs);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals([$activityLogs[0], $activityLogs[1], $activityLogs[2], $activityLogs[3]], $storage->findAllWithSearch());
        $this->assertEquals([$activityLogs[0], $activityLogs[2], $activityLogs[3]], $storage->findAllWithSearch('test'));
        $this->assertEquals([$activityLogs[0], $activityLogs[2]], $storage->findAllWithSearch('test', null, 1, 2));
        $this->assertEquals([$activityLogs[3]], $storage->findAllWithSearch('test', null, 2, 2));
        $this->assertEquals([$activityLogs[0], $activityLogs[3], $activityLogs[2]], $storage->findAllWithSearch('test', null, null, null, null, 'asc'));
        $this->assertEquals([$activityLogs[2], $activityLogs[3], $activityLogs[0]], $storage->findAllWithSearch('test', null, null, null, null, 'desc'));
        $this->assertEquals([$activityLogs[0], $activityLogs[3]], $storage->findAllWithSearch('test', null, 1, 2, null, 'asc'));
        $this->assertEquals([$activityLogs[2]], $storage->findAllWithSearch('test', null, 2, 2, null, 'asc'));
    }

    public function testGetCountForAllWithSearch()
    {
        $activityLogs = [
            new ActivityLog('default'),
            new ActivityLog('default'),
            new ActivityLog('default'),
            new ActivityLog('default'),
        ];
        $activityLogs[0]->setTitle('testA');
        $activityLogs[2]->setTitle('testC');
        $activityLogs[3]->setTitle('testB');
        $collection = new ArrayCollection($activityLogs);
        $storage = new ArrayActivityLogStorage($collection);

        $this->assertEquals(3, $storage->getCountForAllWithSearch('test'));
    }

    public function testFindByParent()
    {
        $activityLog = new ActivityLog('default');
        $activityLogs = [new ActivityLog('default'), new ActivityLog('default'), new ActivityLog('default')];

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
        $activityLog = new ActivityLog('default');
        $storage = new ArrayActivityLogStorage();

        $this->assertEquals($activityLog, $storage->persist($activityLog));
        $this->assertEquals([$activityLog], $storage->findAll());
    }

    public function testPersistWithParent()
    {
        $parentActivityLog = new ActivityLog('default');
        $activityLog = new ActivityLog('default');
        $activityLog->setParent($parentActivityLog);

        $storage = new ArrayActivityLogStorage();

        $this->assertEquals($parentActivityLog, $storage->persist($parentActivityLog));
        $this->assertEquals($activityLog, $storage->persist($activityLog));
        $this->assertEquals([$parentActivityLog], $storage->findAll());
        $this->assertEquals([$activityLog], $storage->findByParent($parentActivityLog));
    }
}
