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
use Mirasvit\PushNotification\Api\Data\PromptInterface;
use Mirasvit\PushNotification\Api\Data\PromptInterfaceFactory;
use Mirasvit\PushNotification\Api\Repository\PromptRepositoryInterface;
use Mirasvit\PushNotification\Model\ResourceModel\Prompt\CollectionFactory;

class PromptRepository implements PromptRepositoryInterface
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
     * @var PromptInterfaceFactory
     */
    private $promptFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        PromptInterfaceFactory $notificationFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->promptFactory = $notificationFactory;
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
        return $this->promptFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $prompt = $this->create();
        $prompt = $this->entityManager->load($prompt, $id);

        if (!$prompt->getId()) {
            return false;
        }

        return $prompt;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PromptInterface $prompt)
    {
        $this->entityManager->delete($prompt);
    }

    /**
     * {@inheritdoc}
     */
    public function save(PromptInterface $prompt)
    {
        if (empty($prompt->getStoreIds())) {
            $prompt->setStoreIds([0]);
        }

        return $this->entityManager->save($prompt);
    }
}