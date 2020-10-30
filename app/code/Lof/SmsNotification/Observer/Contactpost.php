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

class Contactpost implements ObserverInterface
{
	  protected $objectManager;
	  protected $_modelOtpFactory;
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
	   if($helperData->getConfig('sms_admin/enable_sms_contact_us')){
		
		   $request = $observer->getRequest();
		   $telephone = $request->getParam('telephone');
		   $name = $request->getParam('name');
		   $email = $request->getParam('email');	   
		   $countrycode = $request->getParam('countrycode');
		   $comment = $request->getParam('comment');
		   $tempMobile = $countrycode."".$telephone;
		   $send_to = 'admin';
		   $adminMobile = $helperData->getConfig('sms_settings/phone');
			// Customize Template for sending sms

		   $message = $helperData->getContactFormMessageForAdmin($name,$email,$tempMobile,$comment) ;  

			// sent SMS for contact us message
		   $temp = $this->sendsms->send($adminMobile,$message,$send_to);	
		   
		}
	 return true;
   }
}