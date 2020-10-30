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



namespace Mirasvit\PushNotification\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;

class NotificationState implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     **/
    public function toOptionArray()
    {
        return [
            [
                'value' => NotificationInterface::STATE_DRAFT,
                'label' => 'Draft',
            ],
            [
                'value' => NotificationInterface::STATE_PENDING,
                'label' => 'Pending',
            ],
            [
                'value' => NotificationInterface::STATE_SCHEDULED,
                'label' => 'Scheduled',
            ],
            [
                'value' => NotificationInterface::STATE_SENT,
                'label' => 'Sent',
            ],
        ];
    }
}
