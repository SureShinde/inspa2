<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmsNotification\Block\Adminhtml\System\Config;
require_once BP . '/vendor/lof/smsnotification-lib/Twilio/autoload.php';
use Twilio\Rest\Client;

/**
 * "Reset to Defaults" button renderer
 *
 */
class TestButton extends \Magento\Config\Block\System\Config\Form\Field
{
   /** @var UrlInterface */
    protected $_urlBuilder;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_urlBuilder = $context->getUrlBuilder();
       
       
    }
    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Lof_SmsNotification::system/config/testbutton.phtml');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
 
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'smsnotification_debug_result_button',
                'label' => __('Send Test Sms'),
                'onclick' => 'javascript:SmsNotificationSmsDebugTest(); return false;',
            ]
        );

        return $button->toHtml();
    }
    
    public function getAdminUrl(){
        return $this->_urlBuilder->getUrl('lofsmsnotification/test/index', ['store' => $this->_request->getParam('store')]);
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}