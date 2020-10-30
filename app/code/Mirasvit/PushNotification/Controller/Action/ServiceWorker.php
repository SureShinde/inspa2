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
use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\SubscriberServiceInterface;
use Mirasvit\PushNotification\Model\Config;

class ServiceWorker extends Action
{
    /**
     * @var SubscriberServiceInterface
     */
    private $subscriberService;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        SubscriberServiceInterface $subscriberService,
        MessageRepositoryInterface $messageRepository,
        Config $config,
        Context $context
    ) {
        $this->subscriberService = $subscriberService;
        $this->messageRepository = $messageRepository;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('endpoint')) {
            $result = [
                'success'  => true,
                'messages' => [],
            ];
            $endpoint = $this->getRequest()->getParam('endpoint');

            if ($endpoint) {
                $collection = $this->messageRepository->getCollection();
                $collection->addFieldToFilter(MessageInterface::ENDPOINT, $endpoint)
                    ->addFieldToFilter(MessageInterface::IS_PUSHED, true)
                    ->addFieldToFilter(MessageInterface::IS_FETCHED, false);

                foreach ($collection as $message) {
                    $result['messages'][] = [
                        'subject'            => $message->getSubject(),
                        'body'               => $message->getBody(),
                        'url'                => $message->getUrl(),
                        'icon'               => $message->getIcon()
                            ? $this->config->getMediaUrl($message->getIcon()) : '',
                        'image'              => $message->getImage()
                            ? $this->config->getMediaUrl($message->getImage()) : '',
                        'requireInteraction' => true,
                        'tag'                => $message->getId(),
                    ];

                    $this->messageRepository->setFetched($message);
                }
            }
        } elseif ($this->getRequest()->getParam('tag')) {
            $result = [
                'success' => true,
            ];

            $messageId = $this->getRequest()->getParam('tag');
            $message = $this->messageRepository->get($messageId);

            $this->messageRepository->setClicked($message);
        } else {
            $result = [
                'success' => false,
            ];
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json_Encoder::encode($result));
    }
}
