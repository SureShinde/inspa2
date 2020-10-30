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
namespace Lof\SmsNotification\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
 const XML_PATH_EMAIL_TEMPLATE = 'contact/email/email_template';
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'lofsmsnotification/email/testemail';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'lofsmsnotification/email/sender';


    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * Brand config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    protected $scopeConfig;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $inlineTranslation;

    protected $customer;
      /**
     * @var address
     */
      protected $address;
	/**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager 
     */
  public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
    \Magento\Customer\Model\Customer $customer,
    \Magento\Customer\Model\Address $address,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
    parent::__construct($context);
    $this->_filterProvider = $filterProvider;
    $this->_storeManager = $storeManager; 
    $this->inlineTranslation = $inlineTranslation;
    $this->scopeConfig = $context->getScopeConfig();
    $this->customer = $customer;
    $this->customerSession = $customerSession;
    $this->address              = $address;
  }
   public function isLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }
    public function getCustomer() {
        return $this->customerSession;
    }
  public function randomString($length = 6,$format = 'num') {
    $str = "";
    if($format == 'alphanum') {
      $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
    } elseif($format == 'alpha') {
      $characters = array_merge(range('A','Z'), range('a','z'));
    } else {
      $characters = array_merge(range('0','9'));
    } 
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
      $rand = mt_rand(0, $max);
      $str .= $characters[$rand];
    }
    return $str;
  }
  
  public function filter($str)
  {
    $html = $this->_filterProvider->getPageFilter()->filter($str);
    return $html;
  }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
      $store = $this->_storeManager->getStore($store);
      $websiteId = $store->getWebsiteId();

      $result = $this->scopeConfig->getValue(
        'lofsmsnotification/'.$key,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $store);
      return $result;
    }
    public function getStorePhone()
    {
      return $this->scopeConfig->getValue(
        'general/store_information/phone',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getAllStores() {
      $allStores = $this->_storeManager->getStores();
      $stores = array();
      foreach ($allStores as $_eachStoreId => $val)
      {
        $stores[]  = $this->_storeManager->getStore($_eachStoreId)->getId();
      }
      return $stores;
    }
    public function getPhoneById($id) {
      $customer = $this->customer->load($id);
      $telephone = $customer->getTelephone();
      if ($telephone!='') {
        return $telephone;
      }
      $billingID = $customer->getDefaultBilling();
      $addressBilling = $this->address->load($billingID);
      return $addressBilling->getTelephone();
    }
    public function send_message ( $post_body, $url ) {
        /*
        *Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn it into in a
        * multipart formpost, which is not supported:
        */

        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
        // Allowing cUrl funtions 20 second to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Waiting 20 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 20 );

        $response_string = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );

        $sms_result = array();
        $sms_result['success'] = 0;
        $sms_result['details'] = '';
        $sms_result['transient_error'] = 0;
        $sms_result['http_status_code'] = $curl_info['http_code'];
        $sms_result['api_status_code'] = '';
        $sms_result['api_message'] = '';
        $sms_result['api_batch_id'] = '';

        if ( $response_string == FALSE ) {
          $sms_result['details'] .= "cURL error: " . curl_error( $ch ) . "\n";
        } elseif ( $curl_info[ 'http_code' ] != 200 ) {
          $sms_result['transient_error'] = 1;
          $sms_result['details'] .= "Error: non-200 HTTP status code: " . $curl_info[ 'http_code' ] . "\n";
        } else {
          $sms_result['details'] .= "Response from server: $response_string\n";
          $api_result = explode( '|', $response_string );
          $status_code = $api_result[0];
          $sms_result['api_status_code'] = $status_code;
          $sms_result['api_message'] = $api_result[1];
          if ( count( $api_result ) != 3 ) {
            $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count( $api_result );
          } else {
            if ($status_code == '0') {
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
              $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
            } else if ($status_code == '1') {
                # Success: scheduled for later sending.
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
            } else {
              $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
            }
          }
        }
        curl_close( $ch );
        return $sms_result;
      }
      public function send_sms($username, $password, $message, $msisdn) {
        $url = 'https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
        /*
        * Sending unicode message
        */
        $post_body = $this->unicode_sms( $username, $password, $message, $msisdn );
        $result = $this->send_message( $post_body, $url );
        if( $result['success'] ) {
          return $this->print_ln( $this->formatted_server_response( $result ) );
        }
        else {
          return  $this->print_ln( $this->formatted_server_response( $result ) );
        }
      }

      public function send_smslog($username, $password, $message, $msisdn) {
        
        $url = 'https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
        /*
        * Sending unicode message
        */
        $post_body = $this->unicode_sms( $username, $password, $message, $msisdn );
        $result = $this->send_message( $post_body, $url );
        if( $result['success'] ) {
          return 'Sent';
        }
        else {
          return  'Failed';
        }
      }
    //print_ln("Script ran to completion");

      function print_ln($content) {
        if (isset($_SERVER["SERVER_NAME"])) {
          print $content."<br />";
        }
        else {
          print $content."\n";
        }
      }
      function formatted_server_response( $result ) {
        $this_result = "";

        if ($result['success']) {
          $this_result .= "Success: batch ID " .$result['api_batch_id']. "API message: ".$result['api_message']. "\nFull details " .$result['details'];
        }
        else {
          $this_result .= "Fatal error: HTTP status " .$result['http_status_code']. ", API status " .$result['api_status_code']. " API message " .$result['api_message']. " full details " .$result['details'];

          if ($result['transient_error']) {
            $this_result .=  "This is a transient error - you should retry it in a production environment";
          }
        }
        return $this_result;
      }
      function seven_bit_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
          'username' => $username,
          'password' => $password,
          'message'  => character_resolve( $message ),
          'msisdn'   => $msisdn,
      'allow_concat_text_sms' => 0, # Change to 1 to enable long messages
      'concat_text_sms_max_parts' => 2
      );

        return make_post_body($post_fields);
      }
      function eight_bit_sms( $username, $password, $message, $msisdn ) {
        $post_fields = array (
          'username' => $username,
          'password' => $password,
          'message'  => $message,
          'msisdn'   => $msisdn,
          'dca'      => '8bit'
          );
        return make_post_body($post_fields);
      }

      function make_post_body($post_fields) {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
          $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach( $post_fields as $key => $value ) {
          $post_body .= urlencode( $key ).'='.urlencode( $value ).'&';
        }
        $post_body = rtrim( $post_body,'&' );

        return $post_body;
      }

      function character_resolve($body) {
        $special_chrs = array(
          'Δ'=>'0xD0', 'Φ'=>'0xDE', 'Γ'=>'0xAC', 'Λ'=>'0xC2', 'Ω'=>'0xDB',
          'Π'=>'0xBA', 'Ψ'=>'0xDD', 'Σ'=>'0xCA', 'Θ'=>'0xD4', 'Ξ'=>'0xB1',
          '¡'=>'0xA1', '£'=>'0xA3', '¤'=>'0xA4', '¥'=>'0xA5', '§'=>'0xA7',
          '¿'=>'0xBF', 'Ä'=>'0xC4', 'Å'=>'0xC5', 'Æ'=>'0xC6', 'Ç'=>'0xC7',
          'É'=>'0xC9', 'Ñ'=>'0xD1', 'Ö'=>'0xD6', 'Ø'=>'0xD8', 'Ü'=>'0xDC',
          'ß'=>'0xDF', 'à'=>'0xE0', 'ä'=>'0xE4', 'å'=>'0xE5', 'æ'=>'0xE6',
          'è'=>'0xE8', 'é'=>'0xE9', 'ì'=>'0xEC', 'ñ'=>'0xF1', 'ò'=>'0xF2',
          'ö'=>'0xF6', 'ø'=>'0xF8', 'ù'=>'0xF9', 'ü'=>'0xFC',
          );

        $ret_msg = '';
        if( mb_detect_encoding($body, 'UTF-8') != 'UTF-8' ) {
          $body = utf8_encode($body);
        }
        for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
          $c = mb_substr( $body, $i, 1, 'UTF-8' );
          if( isset( $special_chrs[ $c ] ) ) {
            $ret_msg .= chr( $special_chrs[ $c ] );
          }
          else {
            $ret_msg .= $c;
          }
        }
        return $ret_msg;
      }
      function unicode_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
          'username' => $username,
          'password' => $password,
          'message'  => $this->string_to_utf16_hex( $message ),
          'msisdn'   => $msisdn,
          'dca'      => '16bit'
          );
        return $this->make_post_body($post_fields);
      }
      function make_stop_dup_id() {
        return 0;
      }
      function string_to_utf16_hex( $string ) {
        return bin2hex(mb_convert_encoding($string, "UTF-16", "UTF-8"));
      }

  public function getStoreId()
  {
      return $this->_storeManager->getStore()->getId();
  }
  public function getWebsiteId()
  {
      return $this->_storeManager->getStore()->getWebsiteId();
  }
  public function getStoreCode()
  {
      return $this->_storeManager->getStore()->getCode();
  }
 public function getStoreName()
  {
      return $this->_storeManager->getStore()->getName();
  }
  public function getStoreUrl($fromStore = true)
  {
      return $this->_storeManager->getStore()->getBaseUrl();
  }
  public function isStoreActive()
  {
      return $this->_storeManager->getStore()->isActive();
  }

  public function generateRandomString()
  {
  try{
    $length = $this->getOtpStringLength();
    $randomString  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

    return $randomString;
     
     }catch(Exception $e) {
    }
  }
  public function getSignupMessageForUser($firstname,$lastname,$email,$storeName,$mobilenumber,$storeUrl)
  {
    $codes = array('{{first_name}}','{{last_name}}','{{email}}','{{shop_name}}','{{mobilenumber}}','{{shop_url}}');
    $accurate = array($firstname,$lastname,$email,$storeName,$mobilenumber,$storeUrl);
    return str_replace($codes,$accurate,$this->getConfig('sms_customer/message_sms_register_customer'));
  }
  public function getSmsOtp($opt_code) {
    $codes = array('{{var otp_code}}');
    $accurate = array($opt_code);
    return str_replace($codes,$accurate,$this->getConfig('sms_otp/opt_message'));
  }
  public function getSignupMessageForAdmin($firstname,$lastname,$email,$storeName,$mobilenumber,$storeUrl)
  {
    $codes = array('{{first_name}}','{{last_name}}','{{email}}','{{shop_name}}','{{mobilenumber}}','{{shop_url}}');
    $accurate = array($firstname,$lastname,$email,$storeName,$mobilenumber,$storeUrl);
    return str_replace($codes,$accurate,$this->getConfig('sms_admin/message_sms_register_customer'));
  }
  public function getOrderPlaceMessageForAdmin($orderIncrementId,$adminMobile,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail  ,$storeName)
  {
    $codes = array('{{order_id}}','{{mobilenumber}}','{{order_total}}',
            '{{first_name}}','{{last_name}}','{{email}}','{{shop_name}}');
    $accurate = array($orderIncrementId,$adminMobile,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail,$storeName);

    return str_replace($codes,$accurate,$this->getConfig('sms_admin/message_sms_new_order'));
  }
  public function getShipmentMessageForUser($orderCretaedAt,$shipmentCretaedAt,$orderTotal,$orderEmail,
              $orderId,$orderOldStatus,$orderNewStatus,$storeName,$mobilenumber,$firstname,$lastname)
  {
    $codes = array('{{order_created_at}}','{{shipment_created_at}}','{{order_total}}','{{order_email}}','{{order_id}}','{{order_old_status}}','{{order_new_status}}','{{store_name}}','{{user_mobilenumber}}','{{first_name}}','{{last_name}}');
    $accurate = array($orderCretaedAt,$shipmentCretaedAt,$orderTotal,$orderEmail,
              $orderId,$orderOldStatus,$orderNewStatus,$storeName,
              $mobilenumber,$firstname,$lastname);
    return str_replace($codes,$accurate,$this->getConfig('sms_customer/message_new_shipment'));
  }
  public function getCreditMemoMessageForUser($creditMemoId,$creditMemoCreatedAt,$orderTotal,$orderIncrementId,$email,$orderCrated,$firstName,$lastName)
  {
    $codes = array('{{credit_memo_id}}','{{credit_memo_created_at}}','{{order_total}}','{{order_id}}',
            '{{email}}','{{order_created_at}}','{{first_name}}','{{last_name}}');
    $accurate = array($creditMemoId,$creditMemoCreatedAt,$orderTotal,$orderIncrementId,$email,$orderCrated,$firstName,$lastName);

    $refund = str_replace($codes,$accurate,$this->getConfig('sms_customer/message_new_credit_memo')); 
    return $refund;
  }
  public function getInvoiceMessageForUser($orderCretaedAt,$orderTotal,$orderId,$orderOldStatus,$orderNewStatus,$storeName,$firstName,$lastName)
  {
    $codes = array('{{created_at}}','{{order_total}}','{{order_id}}',
            '{{order_old_status}}','{{order_new_status}}','{{store_name}}','{{first_name}}','{{last_name}}');
    $accurate = array($orderCretaedAt,$orderTotal,$orderId,$orderOldStatus,$orderNewStatus,$storeName,$firstName,$lastName);

    return str_replace($codes,$accurate,$this->getConfig('sms_customer/message_new_invoice'));
  }
  public function getContactFormMessageForAdmin($name,$email,$CustomerNumber,$message)
  {
    $codes = array('{{name}}','{{email}}','{{mobilenumber}}','{{message}}');
    $accurate = array($name,$email,$CustomerNumber,$message);
    return str_replace($codes,$accurate,$this->getConfig('sms_admin/message_sms_contact_us'));
  }
  public function getOrderPlaceMessageForUser($orderIncrementId,$mobileNumber,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail,$storeName)
  {
    $orderTotal = number_format((float)$orderTotal, 2, '.', '');
    $codes = array('{{order_id}}','{{mobilenumber}}','{{order_total}}',
            '{{first_name}}','{{last_name}}','{{customer_email}}','{{shop_name}}');
    $accurate = array($orderIncrementId,$mobileNumber,$orderTotal,$orderCustomerFirstName,$orderCustomerLastName,$orderCustomerEmail,$storeName);

    return str_replace($codes,$accurate,$this->getConfig('sms_customer/message_sms_new_order'));
  }
  public function callApiUrl($mobilenumbers,$message)
  {
    try{
  
    $account_sid = $this->getTwilioSid();
    $auth_token = $this->getTwilioToken();
  
    $client = new Services_Twilio($account_sid, $auth_token);
    
    if (substr($mobilenumbers, 0, 1) !== '+') {
      $mobilenumbers = '+'.$mobilenumbers;
    }
       
    $reutnrTwilio =  $client->account->messages->create(
      array(
        'To' => $mobilenumbers,
        'From' => $this->getTwilioMobile(),
        'Body' => $message,
      )
    );  
  
    return true;    
    }catch (\Exception $e) {
      return false;
    }
  }
}