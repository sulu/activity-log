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
use Sulu\Component\ActivityLog\Activity\Activity;
use Sulu\Component\ActivityLog\Storage\ArrayStorage\ArrayActivityStorage;

/**
 * Tests for array-storage.
 */
class ArrayActivityStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $activity = new Activity('default');

        $collection = new ArrayCollection([new Activity('default'), $activity, new Activity('default')]);
        $storage = new ArrayActivityStorage($collection);

        $this->assertEquals($activity, $storage->find($activity->getUuid()));
    }

    public function testFindNoResult()
    {
        $collection = new ArrayCollection([new Activity('default'), new Activity('default')]);
        $storage = new ArrayActivityStorage($collection);

        $this->assertNull($storage->find('123-123-123'));
    }

    public function testFindAll()
    {
        $activities = [new Activity('default'), new Activity('default'), new Activity('default')];
        $collection = new ArrayCollection($activities);
        $storage = new ArrayActivityStorage($collection);

        $this->assertEquals($activities, $storage->findAll());
        $this->assertEquals([$activities[0], $activities[1]], $storage->findAll(1, 2));
        $this->assertEquals([$activities[2]], $storage->findAll(2, 2));
    }

    public function testFindByParent()
    {
        $activity = new Activity('default');
        $activities = [new Activity('default'), new Activity('default'), new Activity('default')];

        foreach ($activities as $childActivity) {
            $childActivity->setParent($activity);
        }

        $collection = new ArrayCollection($activities);
        $storage = new ArrayActivityStorage($collection);

        $this->assertEquals($activities, $storage->findByParent($activity));
        $this->assertEquals([$activities[0], $activities[1]], $storage->findByParent($activity, 1, 2));
        $this->assertEquals([$activities[2]], $storage->findByParent($activity, 2, 2));
    }

    public function testPersist()
    {
        $activity = new Activity('default');
        $storage = new ArrayActivityStorage();

        $this->assertEquals($activity, $storage->persist($activity));
        $this->assertEquals([$activity], $storage->findAll());
    }

    public function testPersistWithParent()
    {
        $parentActivity = new Activity('default');
        $activity = new Activity('default');
        $activity->setParent($parentActivity);

        $storage = new ArrayActivityStorage();

        $this->assertEquals($parentActivity, $storage->persist($parentActivity));
        $this->assertEquals($activity, $storage->persist($activity));
        $this->assertEquals([$parentActivity], $storage->findAll());
        $this->assertEquals([$activity], $storage->findByParent($parentActivity));
    }
}
