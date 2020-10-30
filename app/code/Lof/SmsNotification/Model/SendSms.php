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
 **/

namespace Lof\SmsNotification\Model;
require_once BP .'/vendor/lof/smsnotification-lib/Twilio/autoload.php';
require_once BP .'/vendor/lof/smsnotification-lib/Messagebird/autoload.php';
use Twilio\Rest\Client;
use Magento\TestFramework\Inspection\Exception;

class SendSms
{
    /**
     * @var \Lof\SmsNotification\Helper\Data
     */
    protected $helper;
    /**
     * @var \Lof\SmsNotification\Model\Smslog
     */
    protected $smslog;
    /**
     * @var \Lof\SmsNotification\Model\Smsdebug
     */
    protected $smsdebug;


    protected $date;

    public function __construct(
        \Lof\SmsNotification\Model\Smslog $smslog,
        \Lof\SmsNotification\Model\Smsdebug $smsdebug,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Lof\SmsNotification\Helper\Data $helper
    ) {
        $this->helper  = $helper;
        $this->smslog  = $smslog;
        $this->smsdebug = $smsdebug;
        $this->date = $date;

    }

   

    public function send($mobile,$message,$send_to)
    {     
        $type_sms = $this->helper->getConfig('sms_settings/type_sms');
        $phone = $mobile;
        $phone_from = $this->helper->getConfig('sms_settings/phone');
        $sms = $this->smslog;
        $smsdebug = $this->smsdebug;
        $data['from'] = $phone_from;
        $to = $data['to'] = $phone;
        $data['created_at'] = $this->date->gmtDate();
        $smsdebug->setData($data);
        $data['message'] = $message;
        $data['send_to'] = $send_to;
        $sms->setData($data);
        if($sms->isBlacklist($phone)) {
            $sms->setStatus('Blacklist');
            $sms->save(); 
            $smsdebug->setMessage('Phone numbers have been blacklisted');
            $smsdebug->save();

        } else {
            if($type_sms == 'twilio') {
                try {

                    // Your Account SID and Auth Token from twilio.com/console
                    $sid    = $this->helper->getConfig('sms_settings/sid');
                    $token  = $this->helper->getConfig('sms_settings/token');
                    $client = new Client($sid, $token);

                    // the number you'd like to send the message to
                    
                   
                    if(strpos($phone,'+') == false) {
                        $phone = '+'.$phone;
                    }
                    if(strpos($phone_from,'+') == false) {
                        $phone_from = '+'.$phone_from;
                    }
                    if($phone) {
                        // Use the client to do fun stuff like send text messages!
                         $number = $client->messages->create(
                            $phone,
                            array(
                                // A Twilio phone number you purchased at twilio.com/console
                                'from' => $phone_from,
                                // the body of the text message you'd like to send
                                'body' => $message
                            )
                        ); 
                        $sms->setStatus('Sent');
                        $sms->save(); 
                        $smsdebug->setMessage('Sent Sms Success');
                        $smsdebug->save();
                    }  
                    

                } catch (\Twilio\Exceptions\RestException $e) { 
                    $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($e->getMessage()));
                }
             } elseif($type_sms == 'bulksms') {
               
                $username = $this->helper->getConfig('sms_settings/username');
                $password = $this->helper->getConfig('sms_settings/password');
                   
                if($phone) {
                    $status = $this->helper->send_smslog($username, $password,$message,$phone);
                    $sms->setStatus($status);
                    $sms->save();
                    $smsdebug->setMessage($status);
                    $smsdebug->save();
                }
                
            } elseif ($type_sms == 'messagebird') {
                try {
                    $username =  $this->helper->getConfig('sms_settings/messagebird_username');
                    $password =  $this->helper->getConfig('sms_settings/messagebird_password');
                    $key      =  $this->helper->getConfig('sms_settings/messagebird_key');

                    $messageBird = new \MessageBird\Client("'".$key."'");
                    $messageb = new \MessageBird\Objects\Message();
                    $messageb->originator = 'MessageBird';
                    $messageb->recipients = [$phone];
                    $messageb->body = $message;
                    $messageBird->messages->create($messageb);
                   /* $messageb = new MessageBird($username, $password);
                    $messageb->setSender($this->helper->getConfig('sms_settings/phone'));
                    $messageb->addDestination($phone);
                    $messageb->sendSms($message);*/
                        
                    $sms->setStatus('Sent');
                    $sms->save(); 
                    $smsdebug->setMessage('Sent Sms Success');
                    $smsdebug->save();
                } catch (\Exception $e) {
                    $result = __($e->getMessage());
                    $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($e->getMessage()));
                    $smsdebug->save();
                }
            } elseif($type_sms == 'msg91') {
                $authKey = $this->helper->getConfig('sms_settings/msg91_authkey');
                $message= str_replace(' ','%20',$message);
                $senderId = $this->helper->getStoreUrl();
                $route=4;
                $postData = array('authkey' => $authKey,'mobiles' => $phone,'message' => $message,'sender' => $senderId,'route' =>$route);
                $url="http://api.msg91.com/api/sendhttp.php";
                $ch = curl_init();
                curl_setopt_array($ch, array(CURLOPT_URL => $url,CURLOPT_RETURNTRANSFER => true,CURLOPT_POST => true,CURLOPT_POSTFIELDS => $postData));
                $result = curl_exec($ch);
                if(curl_errno($ch)){
                    $result =  'error:' . curl_error($ch);
                     $sms->setStatus('Failed');
                      $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($e->getMessage()));
                    $smsdebug->save();
                } else {
                    $sms->setStatus('Sent');
                    $sms->save(); 
                    $smsdebug->setMessage('Sent Sms Success');
                    $smsdebug->save();
                }
                curl_close($ch);

            } elseif ($type_sms == 'dialog') {
                $qpassword = $this->helper->getConfig('sms_settings/qpassword');
                $msg= str_replace(' ','%20',$message);
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

                if(curl_errno($URL)){
                    $result =  'error:' . curl_error($URL);
                     $sms->setStatus('Failed');
                      $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($result));
                    $smsdebug->save();
                } else {
                    $sms->setStatus('Sent');
                    $sms->save(); 
                    $smsdebug->setMessage('Sent Sms Success');
                    $smsdebug->save();
                }
                curl_close($URL);
            } elseif ($type_sms == 'smsindiahub') {
                $user =  $this->helper->getConfig('sms_settings/india_username');
                $password =  $this->helper->getConfig('sms_settings/india_password');
                $sid = $this->helper->getConfig('sms_settings/india_sid');
                $msg = urlencode($message);
                $cSession = curl_init(); 
                //step2
                curl_setopt($cSession,CURLOPT_URL,"http://cloud.smsindiahub.in/vendorsms/pushsms.aspx?user=".$user."&password=".$password."&msisdn=".$phone."&sid=".$sid."&msg=".$msg."&fl=0&gwid=2");
                curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($cSession,CURLOPT_HEADER, false); 
                //step3
                $result=curl_exec($cSession);
                if(curl_errno($cSession)){
                    $result =  'error:' . curl_error($cSession);
                     $sms->setStatus('Failed');
                      $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($result));
                    $smsdebug->save();
                } else {
                    $sms->setStatus('Sent');
                    $sms->save(); 
                    $smsdebug->setMessage('Sent Sms Success');
                    $smsdebug->save();
                }
                //step4
                curl_close($cSession);
            } elseif ($type_sms == 'outreach') {
                $type = "xml";
                $id = $this->helper->getConfig('sms_settings/outreach_id');
                $pass = $this->helper->getConfig('sms_settings/outreach_pass');
                $lang = $this->helper->getConfig('sms_settings/outreach_lang');
                if(!$lang) {
                    $lang = "English";
                }
                $mask = "Outreach";
                // Data for text message
                $to = $phone;
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
                if(curl_errno($cSession)){
                    $result =  'error:' . curl_error($cSession);
                     $sms->setStatus('Failed');
                      $sms->setStatus('Failed');
                    $sms->save();
                    $smsdebug->setMessage(__($result));
                    $smsdebug->save();
                } else {
                    $sms->setStatus('Sent');
                    $sms->save(); 
                    $smsdebug->setMessage('Sent Sms Success');
                    $smsdebug->save();
                }
                curl_close($ch);
            }
        }     
        return $this;
    }


   
    public function previewSms($data) {
       /* $history = $this->history->getCollection()->addFieldToFilter('id',$data['history_id']);
        
        foreach ($history as $key => $_history) { 
            $messages = $this->parseVariables($_history->getEmailData(),$data['saved_sms_message']);
            return $messages;
        }*/

    }
}