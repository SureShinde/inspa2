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



namespace Mirasvit\PushNotification\Controller\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\PushNotification\Api\Service\SubscriberServiceInterface;

class Subscribe extends Action
{
    /**
     * @var SubscriberServiceInterface
     */
    private $subscriberService;

    public function __construct(
        SubscriberServiceInterface $subscriberService,
        Context $context
    ) {
        $this->subscriberService = $subscriberService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $endpoint = $this->getRequest()->getParam('endpoint');

        if ($endpoint) {
            $this->subscriberService->saveSubscriber($endpoint);
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json_Encoder::encode([
            'success' => true,
        ]));
    }
}
