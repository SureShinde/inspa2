<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-push-notification
 * @version   1.1.18
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\PushNotification\Api\Repository;

use Mirasvit\PushNotification\Api\Data\MessageInterface;

interface MessageRepositoryInterface
{
    /**
     * @return \Mirasvit\PushNotification\Model\ResourceModel\Message\Collection|MessageInterface[]
     */
    public function getCollection();

    /**
     * @return MessageInterface
     */
    public function create();

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function save(MessageInterface $message);

    /**
     * @param int $id
     * @return MessageInterface|false
     */
    public function get($id);

    /**
     * @param string $endpoint
     * @return MessageInterface[]
     */
    public function getByEndpoint($endpoint);

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function delete(MessageInterface $message);

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function setPushed(MessageInterface $message);

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function setFetched(MessageInterface $message);

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function setClicked(MessageInterface $message);
}
