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



namespace Mirasvit\PushNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Mirasvit\Event\Api\Service\EventServiceInterface;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\MessageServiceInterface;
use Mirasvit\PushNotification\Api\Service\PushServiceInterface;
use Mirasvit\PushNotification\Controller\Adminhtml\Template;

class Preview extends Template
{
    /**
     * @var PushServiceInterface
     */
    private $pushService;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var EventServiceInterface
     */
    private $eventService;

    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    public function __construct(
        PushServiceInterface $pushService,
        TemplateRepositoryInterface $templateRepository,
        MessageRepositoryInterface $messageRepository,
        EventServiceInterface $eventService,
        MessageServiceInterface $messageService,
        Context $context
    ) {
        $this->pushService = $pushService;
        $this->messageRepository = $messageRepository;
        $this->messageService = $messageService;
        $this->eventService = $eventService;

        parent::__construct($templateRepository, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $subject = $this->getRequest()->getParam('subject');
        $body = $this->getRequest()->getParam('body');
        $url = $this->getRequest()->getParam('url');
        $image = $this->getRequest()->getParam('image');
        $icon = $this->getRequest()->getParam('icon');
        $endpoint = $this->getRequest()->getParam('endpoint');

        $message = $this->messageRepository->create();

        $message->setSubject($subject)
            ->setBody($body)
            ->setUrl($url)
            ->setImage($image)
            ->setIcon($icon)
            ->setEndpoint($endpoint);

        $message = $this->messageService->prepareMessage($message, $this->eventService->getRandomParams());

        $message = $this->messageRepository->save($message);

        $this->pushService->push($message);

        $this->messageRepository->setPushed($message);

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(['success' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        return true;
    }
}
