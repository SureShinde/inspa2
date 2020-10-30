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
use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Data\MessageInterfaceFactory;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\PushNotification\Model\ResourceModel\Message\CollectionFactory;

class MessageRepository implements MessageRepositoryInterface
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
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        MessageInterfaceFactory $messageFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->messageFactory = $messageFactory;
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
        return $this->messageFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $message = $this->create();
        $message = $this->entityManager->load($message, $id);

        if (!$message->getId()) {
            return false;
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getByEndpoint($endpoint)
    {
        return $this->getCollection()
            ->addFieldToFilter(MessageInterface::ENDPOINT, $endpoint)
            ->addFieldToFilter(MessageInterface::IS_FETCHED, false);
    }

    /**
     * {@inheritdoc}
     */
    public function save(MessageInterface $message)
    {
        return $this->entityManager->save($message);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(MessageInterface $message)
    {
        $this->entityManager->delete($message);
    }

    /**
     * {@inheritdoc}
     */
    public function setPushed(MessageInterface $message)
    {
        $message->setIsPushed(true);

        return $this->save($message);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetched(MessageInterface $message)
    {
        $message->setIsFetched(true);

        return $this->save($message);
    }

    /**
     * {@inheritdoc}
     */
    public function setClicked(MessageInterface $message)
    {
        $message->setIsClicked(true);

        return $this->save($message);
    }
}
