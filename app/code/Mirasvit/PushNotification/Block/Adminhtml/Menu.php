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



namespace Mirasvit\PushNotification\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['push_notification']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_PushNotification::push_notification_notification',
            'title'    => __('Notifications'),
            'url'      => $this->urlBuilder->getUrl('push_notification/notification/index'),
        ])->addItem([
            'resource' => 'Mirasvit_PushNotification::push_notification_subscriber',
            'title'    => __('Subscribers'),
            'url'      => $this->urlBuilder->getUrl('push_notification/subscriber/index'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_PushNotification::push_notification_template',
            'title'    => __('Templates'),
            'url'      => $this->urlBuilder->getUrl('push_notification/template/index'),
        ])->addItem([
            'resource' => 'Mirasvit_PushNotification::push_notification_prompt',
            'title'    => __('Prompts'),
            'url'      => $this->urlBuilder->getUrl('push_notification/prompt/index'),
        ]);

        return $this;
    }
}
