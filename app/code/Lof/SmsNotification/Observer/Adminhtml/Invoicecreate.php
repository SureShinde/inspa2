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

class Invoicecreate implements ObserverInterface
{
	  protected $objectManager;
	  protected $sendsms;

	  public function __construct(
	  	\Lof\SmsNotification\Model\SendSms $sendsms,
	  \Magento\Framework\ObjectManagerInterface $objectManager
	   )
	   {
		   $this->objectManager = $objectManager;
		   $this->sendsms = $sendsms;
	   }
	 
   public function execute(\Magento\Framework\Event\Observer $observer)
   {
       /** @var Http $request */
			$helperData = $this->objectManager->create('Lof\SmsNotification\Helper\Data');
			
			$order = $observer->getData('order');
			$invoice = $observer->getData('invoice');
			
			$customerid = $order->getCustomerId();
			$customer = $this->objectManager->create('Magento\Customer\Model\Customer')->load($customerid);
			
			$storeName = $helperData->getStoreName();
			$orderCretaedAt = date("F j, Y",strtotime($order->getCreatedAt()));
			$orderTotal = number_format($order->getGrandTotal(), 2, '.', '');
			$orderEmail = $order->getCustomerEmail();
			$orderId = $order->getIncrementId();	
			$orderOldStatus = $order->getStatus();	
			$orderNewStatus = "Processing";	
			if($order->getShippingAddress()) {
				$mobilenumber = $order->getShippingAddress()->getTelephone();
			} else {
				$mobilenumber = '';
			}
			$firstName = $order->getCustomerFirstname();
			$lastName = $order->getCustomerLastname();
			$send_to = 'customer';
		
			if($helperData->getConfig('sms_customer/enable_new_invoice')) {	
				$message = $helperData->getInvoiceMessageForUser(
							$orderCretaedAt,$orderTotal,$orderId,$orderOldStatus,$orderNewStatus,$storeName,$firstName,$lastName) ; 
			
				$temp = $this->sendsms->send($mobilenumber,$message,$send_to); 
			}
			
	 return true;
   }
}