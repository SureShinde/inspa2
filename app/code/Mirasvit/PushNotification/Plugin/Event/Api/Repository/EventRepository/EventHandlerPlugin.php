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



namespace Mirasvit\PushNotification\Plugin\Event\Api\Repository\EventRepository;

use Mirasvit\Event\Api\Data\EventInterface;
use Mirasvit\Event\Api\Repository\EventRepositoryInterface;
use Mirasvit\PushNotification\Service\NotificationService;

class EventHandlerPlugin
{
    private $notificationService;

    public function __construct(
        NotificationService $notificationService
    ) {
        $this->notificationService = $notificationService;
    }

    /**
     * @param EventRepositoryInterface $subject
     * @param EventInterface|bool      $event
     *
     * @return EventInterface
     */
    public function afterRegister($subject, $event)
    {
        if ($event) {
            $this->notificationService->handleEvent($event->getId());
        }

        return $event;
    }
}