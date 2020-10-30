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



namespace Mirasvit\PushNotification\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterfaceFactory;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\PushNotification\Model\ResourceModel\Notification\CollectionFactory;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var NotificationInterfaceFactory
     */
    private $notificationFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        NotificationInterfaceFactory $notificationFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->notificationFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $notification = $this->create();
        $notification = $this->entityManager->load($notification, $id);

        if (!$notification->getId()) {
            return false;
        }

        return $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(NotificationInterface $notification)
    {
        $this->entityManager->delete($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function save(NotificationInterface $notification)
    {
        if (!$notification->getState()) {
            $notification->setState(NotificationInterface::STATE_DRAFT);
        }

        if ($notification->getType() == NotificationInterface::TYPE_MANUAL) {
            if ($notification->isActive() && $notification->getState() == NotificationInterface::STATE_DRAFT) {
                $notification->setState(NotificationInterface::STATE_PENDING);
            }
        } elseif ($notification->getType() == NotificationInterface::TYPE_TRIGGER) {
            if ($notification->isActive()) {
                $notification->setState(NotificationInterface::STATE_SCHEDULED);
            }
        }

        return $this->entityManager->save($notification);
    }
}
