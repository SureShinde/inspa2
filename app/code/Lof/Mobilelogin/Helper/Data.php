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
 * @package    Lof_Mobilelogin
 * @copyright  Copyright (c) 2019 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Mobilelogin\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

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
        'lofmobilelogin/'.$key,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $store);
      return $result;
    }
  
}