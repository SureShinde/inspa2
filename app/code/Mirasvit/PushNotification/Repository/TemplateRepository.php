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
use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Api\Data\TemplateInterfaceFactory;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\PushNotification\Model\ResourceModel\Template\CollectionFactory;

class TemplateRepository implements TemplateRepositoryInterface
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
     * @var TemplateInterfaceFactory
     */
    private $templateFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        TemplateInterfaceFactory $templateFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->templateFactory = $templateFactory;
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
        return $this->templateFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $template = $this->create();
        $template = $this->entityManager->load($template, $id);

        if (!$template->getId()) {
            return false;
        }

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TemplateInterface $template)
    {
        $this->entityManager->delete($template);
    }

    /**
     * {@inheritdoc}
     */
    public function save(TemplateInterface $template)
    {
        return $this->entityManager->save($template);
    }
}
