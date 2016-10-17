<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\ActivityLog\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface for activities.
 */
interface ActivityLogInterface
{
    /**
     * Returns Uuid.
     *
     * @return string
     */
    public function getUuid();

    /**
     * Returns Type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns Message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Returns Data.
     *
     * @return array
     */
    public function getData();

    /**
     * Returns Created.
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Returns Creator.
     *
     * @return UserInterface
     */
    public function getCreator();

    /**
     * Returns Parent.
     *
     * @return ActivityLogInterface
     */
    public function getParent();
}
