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



namespace Mirasvit\PushNotification\Cron;

use Mirasvit\Event\Api\Repository\EventRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;

class EventCheckCron
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        EventRepositoryInterface $eventRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->eventRepository = $eventRepository;
    }

    public function execute()
    {
        foreach ($this->notificationRepository->getCollection() as $notification) {
            $eventIdentifier = $notification->getEventIdentifier();

            $eventInstance = $this->eventRepository->getInstance($eventIdentifier);

            if ($eventInstance) {
                $events = $eventInstance->check($eventIdentifier, []);

                echo $eventIdentifier . ': ' . count($events) . PHP_EOL;
            }
        }
    }
}