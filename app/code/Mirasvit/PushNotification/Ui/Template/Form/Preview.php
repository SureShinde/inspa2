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



namespace Mirasvit\PushNotification\Ui\Template\Form;

use Magento\Ui\Component\AbstractComponent;

class Preview extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'preview';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        $config['previewUrl'] = $this->context->getUrl('push_notification/template/preview');

        $this->setData('config', $config);
    }
}
