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
 * @package    Lof_Formbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Formbuilder\Helper;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    protected $_logger = null;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $postData = null;



    /**
     * @param \Magento\Framework\App\Helper\Context                $context         
     * @param \Magento\Cms\Model\Template\FilterProvider           $filterProvider  
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager    
     * @param \Magento\Framework\Locale\CurrencyInterface          $localeCurrency  
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate      
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager   
     * @param \Magento\Customer\Model\Session                      $customerSession 
     * @param \Magento\Checkout\Model\Session                      $checkoutSession 
     * @param \Magento\Framework\Registry                          $coreRegistry    
     * @param \Magento\Framework\Filter\FilterManager              $filterManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager   = $storeManager;
        $this->_localeDate     = $localeDate;
        $this->_localeCurrency = $localeCurrency;
        $this->_objectManager  = $objectManager;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->coreRegistry    = $coreRegistry;
        $this->filterManager   = $filterManager;
    }

    public function filter($str) {
        $str  = $this->formatCustomVariables($str);
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
    public function getConfig($key, $store = null) {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofformbuilder/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        )
    {$date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

    public function getFormatDate($date, $type = 'full') {
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

    public function getSymbol() {
        $currency = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode());
        $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();

        if (!$symbol) $symbol = '';
        return $symbol;
    }

    public function getMediaUrl() {
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

    public function getFieldPrefix() {
        return 'loffield_';
    }

    public function getCurrentProduct() {
        if ($this->coreRegistry->registry('product')) {
            return $this->coreRegistry->registry('product');
        }
        return false;
    }

    public function getCurrentCategory() {
        if ($this->coreRegistry->registry('current_category')) {
            return $this->coreRegistry->registry('current_category');
        }
        return false;
    }

    /**
     * Quote object getter
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote() {
        $quote = $this->checkoutSession->getQuote();
        return $quote;
    }

    public function getCustomer($customerId = '') {
        $customer = $this->customerSession->getCustomer();
        return $customer;
    }

    public function formatCustomVariables($str) {
        $customer = $this->getCustomer();
        $quote    = $this->getQuote();
        $category = $this->getCurrentCategory();
        $store    = $this->_storeManager->getStore();
        $product  = $this->getCurrentProduct();
        if ($this->_moduleManager->isEnabled('Lof_HidePrice')) {
            if (!$product) {
                $product  = 'product_hideprice';
            }
        }
        $data = [
            "customer"    => $customer,
            "quote"       => $quote,
            "product"     => $product,
            "category"    => $category,
            "store"       => $store
        ];
        $result = $this->filterManager->template($str, ['variables' => $data]);
        return $result;
    }

    public function getEmailsFromData($submitted_data = []) {
        $emails = [];
        $pattern = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i';
        if ($submitted_data) {
            foreach ($submitted_data as $key => $val) {
                preg_match_all($pattern, $val, $matches);
                if ($matches && isset($matches[0]) && $matches[0]) {
                    if (is_array($matches[0])) {
                        $emails = array_merge($emails, $matches[0]);
                    } else {
                        $emails[] = $matches[0];
                    }
                }
            }
        }
        if ($emails) {
            $tmp_emails = [];
            foreach ($emails as $val) {
                if (!in_array($val, $tmp_emails)) {
                    $tmp_emails[] = $val;
                }
            }
            $emails = $tmp_emails;
        }
        return $emails;
    }

    public function writeLogData($e) {
        if($this->getConfig("general_settings/enable_debug")) {
            if(!$this->_logger) {
                $this->_logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
            }
            $this->_logger->addDebug($e);
        }
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('formbuilder');
            $this->getDataPersistor()->clear('formbuilder');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }
    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
    public function getFieldId($field) {
        $cid = isset($field['cid'])?$field['cid']:'';
        $field_id    = isset($field['field_id'])?$field['field_id']:'';
        $field_id    = trim($field_id);
        $field_id    = str_replace(" ","-", $field_id);

        if($field_id) {
            $cid = $field_id;
        }
        return $cid;
    }

    public function FormatDateFormBuilder($getDate){
         $formatDate = $this->scopeConfig->getValue('lofformbuilder/general_settings/dateformat',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return date($formatDate,strtotime($getDate));
    }
}