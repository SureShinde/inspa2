<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
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
namespace Lof\SmsNotification\Controller\Otp;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Display Hello on screen
 */
class Send extends \Magento\Framework\App\Action\Action
{
    protected $_cacheTypeList;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\SmsNotification\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    protected $_message;

    protected $_chat;

    protected $sender;

    protected $otp;

    protected $resultJsonFactory;

    protected $phone;

    protected $sendSms;
        /**
     * @var \Lof\SmsNotification\Model\Smslog
     */
    protected $smslog;
    /**
     * @param Context                                             $context              
     * @param \Magento\Store\Model\StoreManager                   $storeManager         
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    
     * @param \Lof\SmsNotification\Helper\Data                               $helper           
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory 
     * @param \Magento\Framework\Registry                         $registry             
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\SmsNotification\Helper\Data $helper,
        \Lof\SmsNotification\Model\Otp $otp,
        \Lof\SmsNotification\Model\Phone $phone,
        \Lof\SmsNotification\Model\SendSms $sendSms,
        \Lof\SmsNotification\Model\Smslog $smslog,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, 
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession
        ) {
        $this->smslog               = $smslog;
        $this->phone                = $phone;
        $this->resultJsonFactory    = $resultJsonFactory;
        $this->otp                  = $otp;
        $this->resultPageFactory    = $resultPageFactory;
        $this->sendSms              = $sendSms;
        $this->_helper              = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry        = $registry;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_customerSession     = $customerSession;
        $this->_request             = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    { 
         $resultJson = $this->resultJsonFactory->create();
        $data = $this->_request->getPostValue();
        $sms = $this->smslog;
        $responseData = [];
        $length = $this->_helper->getConfig('sms_otp/opt_length');
        $format = $this->_helper->getConfig('sms_otp/opt_format');
        //$data['digit_code'] = mt_rand(100000, 999999);
        $data['digit_code'] = $this->_helper->randomString($length,$format);
        
        $model = $this->_objectManager->create('Lof\SmsNotification\Model\Otp');
        if($data['mobile']) {
            if($sms->isBlacklist($data['mobile'])) {
                $status = false;
                $responseData['msg'] = __('Phone numbers have been blacklisted');
            } else {
                $phone = $this->phone->getCollection()->addFieldToFilter('phone',$data['mobile']);
                if(count($phone->getData()) >0) {
                      $status = false;
                      $responseData['msg'] = __('Phone number already exists, please select another one');
                } else {
                    $status = true;
                    $model->load($data['mobile'],'phone');

                    if(count($model->getData()) > 0) {
                       $model->setDigitCode($data['digit_code']);
                    } else {
                        $model->setPhone($data['mobile'])->setDigitCode($data['digit_code'])->setCountryCode($data['country_code']);
                    }
                    $model->save();

                    if($this->_helper->getConfig('sms_otp/opt_message')) {
                        $message = $this->_helper->getSmsOtp($data['digit_code']);
                    } else {
                        $message = __('Your code active here:').' '.$data['digit_code'];
                    }
                    $send_to = 'customer';
                    $this->sendSms->send($data['mobile'],$message,$send_to);  
               
                }
                $responseData['mobile_id'] = $data['mobile'];
                $responseData['success'] = $status;
            }
        } else {
             $status = false;
            $responseData['success'] = $status;
        }
         $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                );
            return;
    }
}