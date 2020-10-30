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

namespace Lof\SmsNotification\Controller\Adminhtml\Test;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
require_once BP .'/vendor/lof/smsnotification-lib/Twilio/autoload.php';
require_once BP .'/vendor/lof/smsnotification-lib/Messagebird/autoload.php';
use Twilio\Rest\Client;
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Lof\SmsNotification\Helper\Data $helper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->helper           = $helper;
        parent::__construct($context);
        
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
   public function execute() {

        $request = $this->getRequest();
        
        $sid = $request->getPost('sid');
        $token = $request->getPost('token'); 
        $phone = $request->getPost('phone');
        $type_sms = $request->getPost('type_sms');
        $username = $request->getPost('username');
        $password = $request->getPost('password');
        $msg91_authkey = $request->getPost('msg91_authkey');
        $to = $phone ? $phone : $this->helper->getConfig('sms_settings/phone');
        $message = __('Hey Jenny! Good luck on the bar exam!');
        
        if($type_sms == 'twilio') {
            if(!$request->getParam('store', false)){
                if(empty($token) || empty($token)){
                    $this->getResponse()->setBody(__('Please enter a valid sid/token'));
                    return;
                }
            }

            $result = __('Sent... Please check your sms').' '.$to ;
          
            try {
                 $client = new Client($sid, $token);

                // Use the client to do fun stuff like send text messages!
                 $number = $client->messages->create(
                    // the number you'd like to send the message to
                    '+'.$to,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' =>'+'.$this->helper->getConfig('sms_settings/phone'),
                        // the body of the text message you'd like to send
                        'body' => $message
                    )
                );
              
            } catch (\Exception $e) {
                $result = __($e->getMessage());
            }
            $this->getResponse()->setBody($this->makeClickableLinks($result));
        } elseif($type_sms == 'bulksms') {
             if(!$request->getParam('store', false)){
                if(empty($token) || empty($token)){
                    $this->getResponse()->setBody(__('Please enter a valid username/password'));
                    return;
                }
            }

            $result = __('Sent... Please check your sms').' '.$to ;
            $this->getResponse()->setBody($this->makeClickableLinks($result));
            $this->helper->send_sms($username, $password,$message,$this->helper->getConfig('sms_settings/phone'));
  
        } elseif ($type_sms == 'messagebird') {
            try {
                $username =  $request->getPost('messagebird_username');
                $password =  $request->getPost('messagebird_password');
                $key      =  $request->getPost('messagebird_key');

                $messageBird = new \MessageBird\Client("'".$key."'");
                $message = new \MessageBird\Objects\Message();
                $message->originator = 'MessageBird';
                $message->recipients = [$to];
                $message->body = 'This is a test message.';
                      
                $messageBird->messages->create($message);
                $result = __('Sent... Please check your sms').' '.$to ;
                $this->getResponse()->setBody($this->makeClickableLinks($result));
            } catch (\Exception $e) {
                $result = __($e->getMessage());
                $this->getResponse()->setBody($this->makeClickableLinks($result));
            }
        } elseif($type_sms == 'msg91') {
            $authKey = $msg91_authkey;
            $mobileNumbers = $to;
            $senderId = "MSGIND";
            $route=4;
            $postData = array('authkey' => $authKey,'mobiles' => $mobileNumbers,'message' => $message,'sender' => $senderId,'route' =>$route);
            $url="http://api.msg91.com/api/sendhttp.php";
            $ch = curl_init();
            curl_setopt_array($ch, array(CURLOPT_URL => $url,CURLOPT_RETURNTRANSFER => true,CURLOPT_POST => true,CURLOPT_POSTFIELDS => $postData));
            $result = curl_exec($ch);
            if(curl_errno($ch)){
                $result =  'error:' . curl_error($ch);
               
            }
            curl_close($ch);
             $this->getResponse()->setBody($this->makeClickableLinks($result));
        } elseif($type_sms == 'dialog') {
            $destination = $request->getPost('destination');
            $qpassword = $request->getPost('qpassword');
            $msg='This%20is%20a%20test%20message';
            $url='https://cpsolutions.dialog.lk/index.php/cbs/sms/send?destination='.$phone.'&q='.$qpassword.'&message='.$msg;
            $URL = curl_init( $url );
               curl_setopt( $URL, CURLOPT_HEADER, 0 );
               curl_setopt( $URL, CURLOPT_CUSTOMREQUEST, "GET" );
               curl_setopt( $URL, CURLOPT_RETURNTRANSFER, true );
               curl_setopt($URL,CURLOPT_TIMEOUT,30);
               curl_setopt( $URL, CURLOPT_HTTPHEADER, array(
                   "Content-Type: application/json"
            ) );
            $result = curl_exec($URL);
            $this->getResponse()->setBody($this->makeClickableLinks($result));
        } elseif($type_sms == 'smsindiahub') {
            $user =  $request->getPost('india_username');
            $password =  $request->getPost('india_password');
            $sid = "SMSHUB";
            $cSession = curl_init(); 
            //step2
            curl_setopt($cSession,CURLOPT_URL,"http://cloud.smsindiahub.in/vendorsms/pushsms.aspx?user=".$user."&password=".$password."&msisdn=".$phone."&sid=".$sid."&msg=".$msg."&fl=0&gwid=2");
            curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($cSession,CURLOPT_HEADER, false); 
            //step3
            $result=curl_exec($cSession);
            //step4
            curl_close($cSession);
            $this->getResponse()->setBody($this->makeClickableLinks($result));
        } elseif ($type_sms == 'outreach') {
            $type = "xml";
            $id = $request->getPost('outreach_id');
            $pass = $request->getPost('outreach_pass');
            $lang = $request->getPost('outreach_lang');
            if(!$lang) {
                $lang = "English";
            }
            $mask = "Outreach";
            // Data for text message
            $to = $phone;
            $message = "Test with an ampersand (&) and a 5 note";
            $message = urlencode($message);
            // Prepare data for POST request
            $data =
            "id=".$id."&pass=".$pass."&msg=".$message."&to=".$to."&lang=".$lang."&mask=".$mask."&type=".$type;
            // Send the POST request with URL 
            $ch = curl_init('http://www.outreach.pk/api/sendsms.php/sendsms/url');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch); //This is the result from Outreach
            curl_close($ch);
            $this->getResponse()->setBody($this->makeClickableLinks($result));
        }
    }
    
    /**
     * Make link clickable
     * @param string $s
     * @return string
     */
    public function makeClickableLinks($s) {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('');
    }


}
