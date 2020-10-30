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

namespace Lof\SmsNotification\Observer\Adminhtml;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

class Creditmemo implements ObserverInterface
{
	protected $objectManager;
	protected $sendsms;

	public function __construct(
	  	\Lof\SmsNotification\Model\SendSms $sendsms,
		  \Magento\Framework\ObjectManagerInterface $objectManager
	){
		$this->objectManager = $objectManager;
		$this->sendsms = $sendsms;
	}
	 
   public function execute(\Magento\Framework\Event\Observer $observer)
   {
       /** @var Http $request */
	   $helperData = $this->objectManager->create('Lof\SmsNotification\Helper\Data');
	   $creditmemo =  $observer->getEvent ()->getCreditmemo ();
	   $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($creditmemo->getOrderId());
	   $order_information = $order->loadByIncrementId($creditmemo->getOrderId());
	   $customerid = $creditmemo->getCustomerId();
	   $customer = $this->objectManager->create('Magento\Customer\Model\Customer')->load($customerid);
	   
		
		$creditMemoCreatedAt = $creditmemo->getCreatedAt();
		$creditMemoId = $creditmemo->getIncrementId();
		
		$orderTotal = $order_information->getGrandTotal();
		$orderIncrementId = $order_information->getIncrementId();
		$email = $order_information->getCustomerEmail();
		$orderCrated = $order_information->getCreatedAt();	
		if($order->getShippingAddress()) {
			$mobilenumber = $order->getShippingAddress()->getTelephone();
		} else {
			$mobilenumber = '';
		}	
		$firstName = $order_information->getCustomerFirstname();
		$lastName = $order_information->getCustomerLastname();
		$send_to = 'customer';
	
	 	if($helperData->getConfig('sms_customer/enable_new_credit_memo')){	
			$message = $helperData->getCreditMemoMessageForUser($creditMemoId,$creditMemoCreatedAt,$orderTotal,$orderIncrementId,$email,$orderCrated,$firstName,$lastName);	
			$temp = $this->sendsms->send($mobilenumber,$message,$send_to); 
		}

	 return true;
   }
}