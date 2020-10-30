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

namespace Lof\SmsNotification\Block\Sms;

class Notification extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\SmsNotification\Helper\Data
     */
    protected $_eventHelper;

    /**
     * @var \Lof\SmsNotification\Model\Phone
     */
    protected $_phone;


    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Lof\SmsNotification\Helper\Data                           $eventHelper  
     * @param \Lof\SmsNotification\Model\Phone                           $phone        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data  
     * @param \Magento\Framework\Data\Form\FormKey $formKey       
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\SmsNotification\Helper\Data $eventHelper,
        \Lof\SmsNotification\Model\Phone $phone,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
        ) { 
        $this->phone = $phone;
         $this->formKey = $formKey;
        $this->_coreRegistry = $registry;
        $this->_eventHelper = $eventHelper;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }
    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }
    public function _construct()
    {
        parent::_construct();
    }

    
    public function getConfig($key, $default = '')
    {
        $result = $this->_eventHelper->getConfig($key);
        if(!$result){

            return $default;
        }
        return $result;
    }
    public function getCustomerId() { 
        return $this->customerSession->getCustomer()->getId();
    }
    public function getPhone() {
        $phone = $this->phone->load($this->getCustomerId(),'customer_id');
        return $phone;
    }
    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        
        $this->pageConfig->getTitle()->set(__('Sms Notification'));   
       
        return parent::_prepareLayout();
    }
}