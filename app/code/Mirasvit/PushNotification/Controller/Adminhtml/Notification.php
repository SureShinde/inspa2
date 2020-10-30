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



namespace Mirasvit\PushNotification\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;

abstract class Notification extends Action
{
    /**
     * @var NotificationRepositoryInterface
     */
    protected $notificationRepository;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        Context $context
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::marketing');
        $resultPage->getConfig()->getTitle()->prepend(__('Push Notifications'));
        $resultPage->getConfig()->getTitle()->prepend(__('Notifications'));

        return $resultPage;
    }

    /**
     * @return NotificationInterface
     */
    public function initModel()
    {
        $model = $this->notificationRepository->create();

        if ($this->getRequest()->getParam(NotificationInterface::ID)) {
            $model = $this->notificationRepository->get($this->getRequest()->getParam(NotificationInterface::ID));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed(
            'Mirasvit_PushNotification::push_notification_notification'
        );
    }
}
