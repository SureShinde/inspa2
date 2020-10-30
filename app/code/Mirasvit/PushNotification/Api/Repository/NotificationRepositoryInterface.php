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

use Mirasvit\PushNotification\Api\Data\NotificationInterface;

interface NotificationRepositoryInterface
{
    /**
     * @return \Mirasvit\PushNotification\Model\ResourceModel\Notification\Collection|NotificationInterface[]
     */
    public function getCollection();

    /**
     * @return NotificationInterface
     */
    public function create();

    /**
     * @param NotificationInterface $notification
     * @return NotificationInterface
     */
    public function save(NotificationInterface $notification);

    /**
     * @param int $id
     * @return NotificationInterface|false
     */
    public function get($id);

    /**
     * @param NotificationInterface $notification
     * @return bool
     */
    public function delete(NotificationInterface $notification);
}
