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
use Mirasvit\PushNotification\Api\Repository\SubscriberRepositoryInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterfaceFactory;
use Mirasvit\PushNotification\Model\ResourceModel\Subscriber\CollectionFactory;

class SubscriberRepository implements SubscriberRepositoryInterface
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
     * @var SubscriberInterfaceFactory
     */
    private $subscriberFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        SubscriberInterfaceFactory $subscriberFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->subscriberFactory = $subscriberFactory;
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
        return $this->subscriberFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $subscriber = $this->create();
        $subscriber = $this->entityManager->load($subscriber, $id);

        if (!$subscriber->getId()) {
            return false;
        }

        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function getByEndpoint($endpoint)
    {
        $subscriber = $this->create();
        $subscriber->load($endpoint, SubscriberInterface::ENDPOINT);

        if (!$subscriber->getId()) {
            return false;
        }

        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SubscriberInterface $subscriber)
    {
        $this->entityManager->delete($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SubscriberInterface $subscriber)
    {
        return $this->entityManager->save($subscriber);
    }
}
