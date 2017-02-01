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
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Returns Message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * Returns Data.
     *
     * @return array
     */
    public function getData();

    /**
     * Set data.
     *
     * @param string|array $data
     *
     * @return $this
     */
    public function setData($data);

    /**
     * Returns Created.
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated(\DateTime $created);

    /**
     * Returns Creator.
     *
     * @return UserInterface
     */
    public function getCreator();

    /**
     * Set creator.
     *
     * @param UserInterface $creator
     *
     * @return $this
     */
    public function setCreator(UserInterface $creator = null);

    /**
     * Returns Parent.
     *
     * @return ActivityLogInterface
     */
    public function getParent();

    /**
     * Set parent.
     *
     * @param ActivityLogInterface $parent
     *
     * @return $this
     */
    public function setParent(ActivityLogInterface $parent = null);
}
