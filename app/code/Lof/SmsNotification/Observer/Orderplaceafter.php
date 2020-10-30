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

class Orderplaceafter implements ObserverInterface
{
	 protected $sendsms;
	 protected $objectManager;
	 protected $phone;

	  public function __construct(
	  \Lof\SmsNotification\Model\SendSms $sendsms,
	  \Lof\SmsNotification\Model\Phone $phone,
	  \Magento\Framework\ObjectManagerInterface $objectManager
   )
   {
	   $this->objectManager = $objectManager;
	   $this->sendsms = $sendsms;
	   $this->phone = $phone;
   }
 
   public function execute(\Magento\Framework\Event\Observer $observer)
   {
       /** @var Http $request */
	 	 
      	$order_id = $observer->getData('order_ids');
        $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($order_id[0]);
		$customerid = $order->getCustomerId();
		$customer = $this->objectManager->create('Magento\Customer\Model\Customer')->load($customerid);
		$helperData = $this->objectManager->create('Lof\SmsNotification\Helper\Data');
		
		$order_information = $order->loadByIncrementId($order_id[0]);
		$orderIncrementId = $order_information->getIncrementId();
		$orderTotal =  $order_information->getGrandTotal();
		$orderCustomerFirstName = $order_information->getCustomerFirstname();	 	 
		$orderCustomerLastName = $order_information->getCustomerLastname();	 	 
	 	$orderCustomerEmail =  $order_information->getCustomerEmail();
	 	 	 
	 	$storeName = $helperData->getStoreName(); 
        $phone = $this->phone->load($customerid,'customer_id');

        if($phone->getData('phone') && count($phone->getData('phone')) > 0) {
        	$mobile = $phone->getPhone();
        } else {
        	
        	$mobile = $order_information->getShippingAddress()->getData('telephone');
        }
 		
		if($helperData->getConfig('sms_customer/enable_sms_new_order')){

	   		$message = $helperData->getOrderPlaceMessageForUser($orderIncrementId,$mobile,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail,$storeName) ; 
	   		$send_to = 'customer';
			$temp = $this->sendsms->send($mobile,$message,$send_to); 	
		}
		if($helperData->getConfig('sms_admin/enable_sms_new_order')){
			$adminMobile = $helperData->getConfig('sms_settings/phone');
			$send_to = 'admin';
	   		$message = $helperData->getOrderPlaceMessageForAdmin($orderIncrementId,$adminMobile,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail,$storeName) ; 
			$temp = $this->sendsms->send($adminMobile,$message,$send_to); 	
		}
	 return true;
   }
}