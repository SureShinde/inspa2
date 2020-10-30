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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

 
namespace Lof\SmsNotification\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Data\Customer;
//use Lof\SmsNotification\Model\OtpFactory;

class CustomerRegister implements ObserverInterface
{
	  protected $objectManager;
	  protected $customerFactory;
	  protected $customerData;
	  protected $repository;
	  //protected $_modelOtpFactory;
	  protected $customerAddressFactory;
	  protected $dataObjectProcessor;
	  protected $sendsms;
	  
	  
	  public function __construct(
	  \Magento\Framework\ObjectManagerInterface $objectManager,
	  \Magento\Customer\Model\CustomerFactory $customerFactory,
	  \Magento\Customer\Model\Data\Customer $customerData,
	  \Lof\SmsNotification\Model\SendSms $sendsms
	  //OtpFactory $modelOtpFactory
   )
   {
	   $this->objectManager = $objectManager;
	   $this->customerFactory = $customerFactory;
	   $this->customerData = $customerData;
	   //$this->_modelOtpFactory = $modelOtpFactory;
	   $this->sendsms = $sendsms;
	     
   }
 
   public function execute(\Magento\Framework\Event\Observer $observer)
   {
		$helperData = $this->objectManager->create('Lof\SmsNotification\Helper\Data');
		if($helperData->getConfig('general_settings/enable_module') != 1) {
			return;
		}

       /** @var Http $request */
	  	$_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
	  	$model = $_objectManager->create('Lof\SmsNotification\Model\Phone');
	  	$request = $observer->getRequest();
	  	$accountController = $observer->getData('account_controller');
	  	$customer = $observer->getData('customer');
	  	$mobilenumber = $accountController->getRequest()->getParam('mobile_number');
	  	$country_code = $accountController->getRequest()->getParam('country_code');
	  	$firstname = $customer->getFirstName();
	  	$lastname = $customer->getLastName();
	  	$email = $customer->getEmail();
	  	$storeName = $helperData->getStoreName();
	  	$storeUrl = $helperData->getStoreUrl();	  
  
		$model->setPhone($mobilenumber)->setCustomerId($customer->getId())->setCountryCode($country_code)->save();
		if($helperData->getConfig('sms_customer/enable_sms_register_customer')){
		
			$message = $helperData->getSignupMessageForUser($firstname,$lastname,$email,$storeName,$mobilenumber,$storeUrl); 	
			$send_to = 'customer';
		  //call api for send SMS from Helper Data		
		  $temp = $this->sendsms->send($mobilenumber,$message,$send_to);	
		}
		if($helperData->getConfig('sms_admin/enable_sms_register_customer')){
			$adminMobile = $helperData->getConfig('sms_settings/phone');
			$message = $helperData->getSignupMessageForAdmin($firstname,$lastname,$email,$storeName,$adminMobile,$storeUrl); 
			$send_to = 'admin';
		
		//call api for send SMS from Helper Data
			$temp = $this->sendsms->send($mobilenumber,$message,$send_to);	
		}
	 return true;
   }
}