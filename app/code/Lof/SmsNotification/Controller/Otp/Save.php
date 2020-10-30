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
class Save extends \Magento\Framework\App\Action\Action
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

    protected $_customerRepositoryInterface;
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
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, 
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession
        ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
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

        $responseData = [];
        $model = $this->_objectManager->create('Lof\SmsNotification\Model\Phone');
        if(isset($data['mobile_number']) && $data['mobile_number']) {
            $model->load($data['customer_id'],'customer_id'); 
            $model->setPhone($data['mobile_number'])->setCountryCode($data['country_code'])->setCustomerId($data['customer_id']);

            $customer = $this->_customerRepositoryInterface->getById($data['customer_id']);
            $customer->setCustomAttribute('mobilenumber',$data['mobilenumber']);
            try  {
            $model->save();
            $this->_customerRepositoryInterface->save($customer);
         
              $this->messageManager->addSuccess(__('You updated phone success.'));     
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while updating phone.'));
            }
        } 
        $this->_redirect('sms/notification');
    }
}