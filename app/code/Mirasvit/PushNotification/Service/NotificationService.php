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



namespace Mirasvit\PushNotification\Service;

use Mirasvit\Event\Api\Data\EventInterface;
use Mirasvit\Event\Api\Repository\EventRepositoryInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\SubscriberRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\MessageServiceInterface;
use Mirasvit\PushNotification\Api\Service\NotificationServiceInterface;
use Mirasvit\PushNotification\Api\Service\PushServiceInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NotificationService implements NotificationServiceInterface
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    /**
     * @var SubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        MessageRepositoryInterface $messageRepository,
        MessageServiceInterface $messageService,
        SubscriberRepositoryInterface $subscriberRepository,
        TemplateRepositoryInterface $templateRepository,
        EventRepositoryInterface $eventRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->messageRepository = $messageRepository;
        $this->messageService = $messageService;
        $this->subscriberRepository = $subscriberRepository;
        $this->templateRepository = $templateRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handleEvent($eventId)
    {
        $event = $this->eventRepository->get($eventId);

        if (!$event) {
            return $this;
        }

        $eventIdentifier = $event->getIdentifier();

        $notifications = $this->notificationRepository->getCollection();

        $notifications->addFieldToFilter(NotificationInterface::IS_ACTIVE, true)
            ->addFieldToFilter(NotificationInterface::TYPE, NotificationInterface::TYPE_TRIGGER)
            ->addFieldToFilter(NotificationInterface::EVENT_IDENTIFIER, $eventIdentifier);

        foreach ($notifications as $notification) {
            $subscribers = $this->subscriberRepository->getCollection();

            $customerId = $event->getParam('customer_id');
            $endpoint = $event->getParam('endpoint');

            if ($customerId) {
                $subscribers->addFieldToFilter(SubscriberInterface::CUSTOMER_ID, $customerId);
            } elseif ($endpoint) {
                $subscribers->addFieldToFilter(SubscriberInterface::ENDPOINT, $endpoint);
            } else {
                continue;
            }

            $template = $this->templateRepository->get($notification->getTemplateId());

            foreach ($subscribers as $subscriber) {
                $message = $this->messageRepository->create();
                $message
                    ->setSubject($template->getSubject())
                    ->setBody($template->getBody())
                    ->setIcon($template->getIcon())
                    ->setImage($template->getImage())
                    ->setUrl($template->getUrl())
                    ->setEndpoint($subscriber->getEndpoint())
                    ->setSubscriberId($subscriber->getId())
                    ->setNotificationId($notification->getId())
                    ->setTemplateId($template->getId());

                $message = $this->messageService->prepareMessage($message, $event->getParams());
                $this->messageRepository->save($message);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function scheduleNotifications()
    {
        $collection = $this->notificationRepository->getCollection();

        $collection->addFieldToFilter(NotificationInterface::IS_ACTIVE, true)
            ->addFieldToFilter(NotificationInterface::STATE, NotificationInterface::STATE_PENDING);

        foreach ($collection as $notification) {

            foreach ($notification->getStoreIds() as $storeId) {
                $subscribers = $this->getSubscribers($storeId);
                $template = $this->templateRepository->get($notification->getTemplateId());

                foreach ($subscribers as $subscriber) {
                    $message = $this->messageRepository->create();
                    $message
                        ->setSubject($template->getSubject())
                        ->setBody($template->getBody())
                        ->setIcon($template->getIcon())
                        ->setImage($template->getImage())
                        ->setUrl($template->getUrl())
                        ->setEndpoint($subscriber->getEndpoint())
                        ->setSubscriberId($subscriber->getId())
                        ->setNotificationId($notification->getId())
                        ->setTemplateId($template->getId())
                        ->setStoreId($storeId);

                    $message = $this->messageService->prepareMessage($message, []);
                    $this->messageRepository->save($message);
                }
            }

            $notification->setState(NotificationInterface::STATE_SCHEDULED);
            $this->notificationRepository->save($notification);
        }
    }

    /**
     * @param int $storeId
     * @return SubscriberInterface[]
     */
    private function getSubscribers($storeId)
    {
        $collection = $this->subscriberRepository->getCollection();
        $collection->addStoreFilter($storeId);

        return $collection;
    }
}
