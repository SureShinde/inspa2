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



namespace Mirasvit\PushNotification\Service;


use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\Event\Api\Service\EventKeeperInterface;

class EventKeeper implements EventKeeperInterface
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    /**
     * EventKeeper constructor.
     *
     * @param NotificationRepositoryInterface $notificationRepository
     */
    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        $events = [];
        $notifications = $this->notificationRepository->getCollection();

        $notifications->addFieldToFilter(NotificationInterface::IS_ACTIVE, NotificationInterface::STATUS_ACTIVE);

        foreach ($notifications as $notification) {
            $events[] = $notification->getEventIdentifier();
        }

        return $events;
    }
}
