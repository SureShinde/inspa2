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

namespace Lof\Formbuilder\Model;

class Form extends \Magento\Framework\Model\AbstractModel {

    const CACHE_BLOCK_TAG = 'lof_formbuilder_block';
    const CACHE_PAGE_TAG = 'lof_formbuilder_page';
    const CACHE_MEDIA_TAG = 'lof_formbuilder_media';


    const FORM_ID = 'form_id';

    /**
     * Form cache tag
     */
    const CACHE_TAG = 'formbuilder_form';

    /**#@+
     * Form's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var string
     */
    protected $_cacheTag = 'formbuilder_form';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'formbuilder_form';

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blogHelper;

    /**
     * @var ResourceModel\Form|null
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var
     */
    protected $_store;

    /**
     * @var
     */
    protected $HelperBackend;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * Form constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Form|null $resource
     * @param ResourceModel\Form\Collection|null $resourceCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Lof\Formbuilder\Helper\Data $helper
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriber
     * @param \Magento\Directory\Model\Country $country
     * @param \Magento\Framework\Pricing\Helper\Data $currency
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Backend\Helper\Data $HelperBackend
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Formbuilder\Model\ResourceModel\Form $resource = null,
        \Lof\Formbuilder\Model\ResourceModel\Form\Collection $resourceCollection = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lof\Formbuilder\Helper\Data $helper,
        \Magento\Newsletter\Model\SubscriberFactory $subscriber,
        \Magento\Directory\Model\Country $country,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Backend\Helper\Data $HelperBackend,
        \Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        //$this->_resource = $resource;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_helper = $helper;
        $this->_subscriber = $subscriber;
        $this->_country = $country;
        $this->_currency = $currency;
        $this->_HelperBackend = $HelperBackend;
        $this->_productloader = $productloader;
        $this->filterManager = $filterManager;
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave() {
        $needle = 'form_id="' . $this->getId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that static form content does not reference the form itself.')
        );
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId() {
        return $this->getData(self::FORM_ID);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores() {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses() {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getYesno() {
        return [self::STATUS_ENABLED => __('Yes'), self::STATUS_DISABLED => __('No')];
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId) {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    public function checkCustomerGroup($identifier, $customerGroupId) {
        return $this->_getResource()->checkCustomerGroup($identifier, $customerGroupId);
    }
    public function checkEmailInSubscription($post_data = array()) {
        return true;
    }
    public function subscriptionListEmails($emails = []){
        $status = false;
        if($emails) {
            foreach ($emails as $email) {
                $fresh_model = $this->_subscriber->create()->setId(null);
                $status = $fresh_model->subscribe($email);
            }
        }
        return $status;
    }
    public function getCustomFormFields($post_data = array()) {
        $subscription_all = $this->_helper->getConfig("email_settings/subscription_all");
        if ((0 < $this->getId()) && $post_data) {
            $form_data = array();
            $custom_fields = array();

            $emails = array();
            $is_subscription = false;
            if ($custom_fields = $this->getFields()) {
                $fieldPrefix = $this->_helper->getFieldPrefix();
                foreach ($custom_fields as $i => $field) {
                    $cid = $this->_helper->getFieldId($field);
                    $field_id = $fieldPrefix . $cid . $this->getId();
                    $field_type = $field['field_type'];
                    $field_value = "";

                    //if (isset($post_data[$field_id]) || isset($post_data[$field_id."0"])) {
                        $tmp = $field;
                        $tmp['field_cid'] = $field_id;
                        $field_value = isset($post_data[$field_id]) ? $post_data[$field_id] : "";
                        $field_value = $this->xss_clean($field_value);
                        switch ($field_type) {
                            case 'website':
                                if($field_value){
                                    if((false === strpos($field_value, "http://")) && (false === strpos($field_value, "https://") )){
                                        $field_value = "http://".$field_value;
                                    }
                                    $field_value = '<a href="' . $field_value . '" target="_BLANK">' . $field_value . '</a>';
                                }
                                break;
                            case 'email':
                                if($field_value){
                                    $emails[] = trim($field_value);
                                    $tmp['thanks_email'] = trim($field_value);
                                    $field_value = '<a href="mailto:' . trim($field_value) . '" target="_BLANK">' . $field_value . '</a>';
                                }
                                break;
                            case 'radio':
                                if($field_value){
                                    if ($field_value == "other" && isset($post_data[$field_id . "_other"]) && $post_data[$field_id . "_other"]) {
                                        $field_value = $post_data[$field_id . "_other"];
                                    }
                                    if (strpos($field_value, "{{") !== false) {
                                        $field_value = str_replace(array("{{", "}}"), array('<img src="{{', '}}" alt="img"/>'), $field_value);
                                        $field_value = $this->_helper->filter($field_value);
                                    } else {
                                        $field_value = __($field_value);
                                    }
                                }
                                break;
                            case 'checkboxes':
                                if (is_array($field_value) && $field_value) {
                                    foreach ($field_value as $j => $value) {
                                        if ($value == "other" && isset($post_data[$field_id . "_other"]) && $post_data[$field_id . "_other"]) {
                                            $field_value[$j] = $post_data[$field_id . "_other"];
                                        }

                                        if (strpos($field_value[$j], "{{") !== false) {
                                            $field_value[$j] = str_replace(array("{{", "}}"), array('<img src="{{', '}}" alt="img"/>'), $field_value[$j]);
                                            $field_value[$j] = $this->_helper->filter($field_value[$j]);
                                        } else {
                                            $field_value[$j] = __($field_value[$j]);
                                        }
                                    }
                                }
                                if (is_array($field_value)) {
                                    $field_value = implode(", ", $field_value);
                                }
                                break;
                            case 'address':
                                $street = isset($post_data[$field_id . "_street"]) ? $post_data[$field_id . "_street"] : "";
                                $street2 = isset($post_data[$field_id . "_street2"]) ? $post_data[$field_id . "_street2"] : "";
                                $city = isset($post_data[$field_id . "_city"]) ? $post_data[$field_id . "_city"] : "";
                                $state = isset($post_data[$field_id . "_state"]) ? $post_data[$field_id . "_state"] : "";
                                $zipcode = isset($post_data[$field_id . "_zipcode"]) ? $post_data[$field_id . "_zipcode"] : "";
                                $country = isset($post_data[$field_id . "_country"]) ? $post_data[$field_id . "_country"] : "";
                                $field_value = $this->formatAddress($street, $city, $state, $zipcode, $country, $street2);
                                break;
                            case 'file_upload':
                                if (isset($post_data[$field_id . "_fileurl"])) {
                                    $field_value = '<a href="' . $post_data[$field_id . "_fileurl"] . '" target="_BLANK">';
                                    if (isset($post_data[$field_id . "_isimage"])) {
                                        $field_value .= '<div><img style="width: 150px" src="' . $post_data[$field_id . "_fileurl"] . '"/></div>';
                                    }
                                    $field_value .= $post_data[$field_id . "_filename"] . ' - (' . round($post_data[$field_id . "_filesize"], 2) . 'Kb)';
                                    $field_value .= '</a>';
                                }
                                break;
                            case 'multifile_upload':
                                if ($field_value && is_array($field_value)) {
                                    if (isset($post_data[$field_id . "_fileurl"]) && is_array($post_data[$field_id . "_fileurl"])) {
                                        $tmp_files = [];
                                        foreach ($post_data[$field_id . "_fileurl"] as $j => $value) {
                                            $tmp_field_value = '<a href="' . $value . '" target="_BLANK">';
                                            if (isset($post_data[$field_id . "_isimage"]) 
                                                && isset($post_data[$field_id . "_isimage"][$j]) 
                                                && $post_data[$field_id . "_isimage"][$j]) {

                                                $tmp_field_value .= '<div><img style="width: 150px" src="' . $value . '"/></div>';
                                            }
                                            $tmp_field_value .= $field_value[$j];

                                            if(isset($post_data[$field_id . "_filesize"]) && isset($post_data[$field_id . "_filesize"][$j])) {
                                                $tmp_field_value .= ' - (' . round($post_data[$field_id . "_filesize"][$j], 2) . 'Kb)';
                                            }
                                            
                                            $tmp_field_value .= '</a>';
                                            $tmp_files[] = $tmp_field_value;
                                        }
                                        $field_value = implode("<br/>", $tmp_files);
                                    } else {
                                        $field_value = implode(", ", $field_value);
                                    }
                                }
                                break;
                            case 'model_dropdown':
                                if ($field_value && is_array($field_value)) {
                                    $tmp_models = array();
                                    $k = 1;
                                    foreach ($field_value as $key => $fitem) {
                                        $tmp2 = array();
                                        if (is_array($fitem)) {
                                            foreach ($fitem as $k2 => $fitem2) {
                                                $tmp2[] = $fitem2;
                                            }
                                        } else {
                                            $tmp2 = array($fitem);
                                        }
                                        if ($tmp2 && $fitem) {
                                            $tmp_models[] = $k . ". " . implode(" > ", $tmp2);
                                        }

                                        $k++;
                                    }
                                    $field_value = implode("<br/>", $tmp_models);
                                }
                                break;
                            case 'price':
                                $field_value = $this->_currency->currency($field_value, true, false);
                                break;
                            case 'time':
                                $hours = isset($post_data[$field_id . "_hours"]) ? $post_data[$field_id . "_hours"] : "00";
                                $minutes = isset($post_data[$field_id . "_minutes"]) ? $post_data[$field_id . "_minutes"] : "00";
                                $seconds = isset($post_data[$field_id . "_seconds"]) ? $post_data[$field_id . "_seconds"] : "00";
                                $am_pm = isset($post_data[$field_id . "_am_pm"]) ? $post_data[$field_id . "_am_pm"] : "";
                                $field_value = $hours . ':' . $minutes . ':' . $seconds . ' ' . $am_pm;
                                break;
                            case 'google_map':
                                $location = $field_value;
                                $lat = isset($post_data[$field_id . "_lat"]) ? $post_data[$field_id . "_lat"] : "";
                                $long = isset($post_data[$field_id . "_long"]) ? $post_data[$field_id . "_long"] : "";
                                $rand = isset($post_data[$field_id . "_radius"]) ? $post_data[$field_id . "_radius"] : "";
                                $field_value = $location . "<br/>" . __("Latitude: %1", $lat) . " , " . __("Longtitude: %1", $long);
                                break;
                            case 'subscription':
                                $field_value = isset($post_data[$field_id . '0']) ? $post_data[$field_id . '0'] : "";

                                if (is_array($field_value) && $field_value) {
                                    $field_value = $field_value[0];
                                }
                                if ($field_value == 1) {
                                    $is_subscription = true;
                                }

                                $field_value = "";
                                $tmp['subscription'] = true;
                                if($is_subscription) {
                                    $field_value =  __("Yes");
                                } else {
                                    $field_value =  __("No");
                                }
                                
                                break;
                            case 'rating':
                                $limit = isset($post_data[$field_id . "_limit"]) ? (int)$post_data[$field_id . "_limit"] : 5;
                                $rating_value = (float)$field_value;
                                if ($limit) {
                                    $field_value = '<div class="rating small">';
                                    for ($i = 1; $i <= $limit; $i++) {
                                        $fclass = "";
                                        if ($i <= $rating_value) {
                                            $fclass = 'on';
                                        }
                                        $field_value .= '<span class="star ' . $fclass . '">&nbsp;</span>';
                                    }
                                    $field_value .= '<span class="score">' . __("%1 stars", $rating_value) . '</span>';
                                    $field_value .= '</div>';
                                }
                                break;
                            case 'product_field':
                                if ($field_value) {
                                    $product = $this->getLoadProduct($field_value);
                                    $urlProduct = $this->_HelperBackend->getUrl(
                                        'catalog/product/edit',
                                        [
                                            'id' => $product->getId()
                                        ]
                                    );
                                    $image = $product->getImage();
                                    $urlImage = $this->getBaseMediaUrl() . 'catalog/product' . $image;
                                    $image_alt = $product->getName();
                                    $image_alt = trim($image_alt);
                                    $image_alt = str_replace(array('"',"'"),"", $image_alt);
                                    $tmp['image'] = "<img class='admin__control-thumbnail' src='" . $urlImage . "' width='35px' alt='".$image_alt."'/>";
                                    if ($tmp['label'] == null || empty($tmp['label'])) {
                                        $tmp['label'] = $product->getName();
                                    }
                                    $field_value = '<br/><a href="' . $urlProduct . '" target="_BLANK">' . $product->getName() . '</a>';
                                    $price = $this->formatPrice($product->getFinalPrice());
                                    if($price) {
                                        $field_value .= '<br/>'.__("Price: %1", $price);
                                    }
                                }
                                break;
                            default:
                                if($field_value) {
                                    $field_value = strip_tags($field_value);
                                    $field_value = trim($field_value);
                                    $field_value = is_numeric($field_value)?$field_value:__($field_value);
                                }
                            break;
                        }
                        $tmp['value'] = $field_value;
                        $form_data[] = $tmp;
                   // }
                }
                /*Active Subscription For There Emails*/
                if (($is_subscription || $subscription_all) && $emails) {
                    $this->subscriptionListEmails($emails);
                }
                
                return $form_data;
            }
        }
        return false;
    }

    public function getFields() {
        $fields = json_decode('[' . $this->getData('design') . ']', TRUE);
        if (isset($fields[0]['fields'])) {
            $fields[0] = $fields[0]['fields'];
            $fls = [];
            foreach ($fields[0] as $k => $v) {
                if (isset($v['field_type'])) {
                    $fls[] = $v;
                }
            }
            $fields[0] = $fls;
        }
        if (isset($fields[0])) {

            $tmpFields = [];
            foreach ($fields[0] as &$_field) {
                if (isset($tmpFields[$_field['cid']])) {
                    $_field['cid'] = $_field['cid'] . 'duplicate';
                }
                $tmpFields[$_field['cid']] = $_field;
            }

            return $fields[0];
        }
        return;
    }

    public function formatAddress($street = "", $city = "", $state = "", $zipcode = "", $country = "", $street2 = "")
    {
        $address_format = $this->_helper->getConfig("field_templates/address");
        $data = [
            "street" => $street,
            "street2" => $street2,
            "city" => $city,
            "region" => $state,
            "postcode" => $zipcode,
            "country" => $country
        ];
        $street2 = $street2?(' '.$street2):'';
        if ($address_format == '') return $street .$street2. ', ' . $city . ', ' . $state . ', ' . $zipcode . ', ' . $country;
        $addressText = $this->filterManager->template($address_format, ['variables' => $data]);
        return $addressText;
    }

    public function getLoadProduct($sku)
    {
        $productCollection = $this->_productloader->create()->getCollection();
        $productCollection->addAttributeToSelect('entity_id','name','price', 'image')->addAttributeToFilter('sku',array('eq' => $sku));
        $collection = $productCollection->load();
        if($collection->getSize()){
            $product_id = $collection->getFirstItem()->getEntityId();
            return $this->_productloader->create()->load((int)$product_id);
        } else {
            return false;
        }
    }
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function formatPrice($price)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); // Instance of Pricing Helper
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    public function getFormLink()
    {
        $route = $this->getConfig('general_settings/route');
        return $route;
    }

    public function getDataProducts($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection = $productCollection->addAttributeToSelect('name', 'price', 'image')
            ->addAttributeToFilter('entity_id', array('eq' => $id))->load();
        return $collection->getData();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Formbuilder\Model\ResourceModel\Form');
    }

    public function loadByAlias($alias = "") {
        if($alias) {
            $this->_beforeLoad($alias, 'identifier');
            $this->_getResource()->load($this, $alias, 'identifier');
            $this->_afterLoad();
            $this->setOrigData();
            $this->_hasDataChanges = false;
            $this->updateStoredData();
        }
        return $this;
    }

    /**
     * Synchronize object's stored data with the actual data
     *
     * @return $this
     */
    private function updateStoredData()
    {
        if (isset($this->_data)) {
            $this->storedData = $this->_data;
        } else {
            $this->storedData = [];
        }
        return $this;
    }
    protected function xss_clean($data)
    {
        if(!is_string($data))
            return $data;
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
