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



namespace Mirasvit\PushNotification\Plugin;

use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;

class MessageSynchronizationPlugin
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param MessageRepositoryInterface $repository
     * @param MessageInterface $message
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetPushed(MessageRepositoryInterface $repository, MessageInterface $message)
    {
        if ($message->getNotificationId()) {
            $notification = $this->notificationRepository->get($message->getNotificationId());

            if (!$notification) {
                return;
            }

            $notification->setPushed($notification->getPushed() + 1)
                ->setState(NotificationInterface::STATE_SENT);

            $this->notificationRepository->save($notification);
        }
    }

    /**
     * @param MessageRepositoryInterface $repository
     * @param MessageInterface $message
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetFetched(MessageRepositoryInterface $repository, MessageInterface $message)
    {
        if ($message->getNotificationId()) {
            $notification = $this->notificationRepository->get($message->getNotificationId());

            if (!$notification) {
                return;
            }

            $notification->setFetched($notification->getFetched() + 1);

            $this->notificationRepository->save($notification);
        }
    }

    /**
     * @param MessageRepositoryInterface $repository
     * @param MessageInterface $message
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetClicked(MessageRepositoryInterface $repository, MessageInterface $message)
    {
        if ($message->getNotificationId()) {
            $notification = $this->notificationRepository->get($message->getNotificationId());

            if (!$notification) {
                return;
            }

            $notification->setClicked($notification->getClicked() + 1);

            $this->notificationRepository->save($notification);
        }
    }
}
