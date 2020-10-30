<?php /**
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
 * @package    Lofformbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Formbuilder\Block;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Lof\Formbuilder\Helper\Data
     */
    protected $formHelper;

    /**
     * @var \Lof\Formbuilder\Model\Form
     */
    protected $form = null;

    /**
     * @var \Magento\Framework\Url
     */
    protected $url;

    protected $request;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context  
     * @param \Magento\Framework\Registry                      $registry 
     * @param \Lof\Formbuilder\Helper\Data                     $helper   
     * @param \Lof\Formbuilder\Model\Form                      $form     
     * @param \Magento\Framework\Url                           $url
     * @param \Magento\Framework\App\RequestInterface          $request 
     * @param \Magento\Customer\Model\Session                  $customerSession  
     * @param array                                            $data   
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Formbuilder\Helper\Data $helper,
        \Lof\Formbuilder\Model\Form $form,
        \Magento\Framework\Url $url,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry   = $registry;
        $this->formHelper = $helper;
        $this->_form  = $form;
        $this->url        = $url;
        $this->request    = $request;
        $this->_customerSession = $customerSession;
    }

    protected $_fields = [
        "text"           => "fields/text.phtml",
        "website"        => "fields/website.phtml",
        "radio"          => "fields/radio.phtml",
        "dropdown"       => "fields/dropdown.phtml",
        "paragraph"      => "fields/textarea.phtml",
        "email"          => "fields/email.phtml",
        "date"           => "fields/date.phtml",
        "time"           => "fields/time.phtml",
        "checkboxes"     => "fields/checkboxes.phtml",
        "number"         => "fields/number.phtml",
        "price"          => "fields/price.phtml",
        "section_break"  => "fields/section_break.phtml",
        "address"        => "fields/address.phtml",
        "file_upload"    => "fields/file.phtml",
        "multifile_upload"    => "fields/multi_files.phtml",
        "model_dropdown" => "fields/model_dropdown.phtml",
        "subscription"   => "fields/subscription.phtml",
        "rating"         => "fields/rating.phtml",
        "google_map"     => "fields/google_map.phtml",
        "html"           => "fields/html.phtml",
        "product_field"  => "fields/product_field.phtml"
    ];

    protected $_inline_label_fields = [
        "text"           => "inline_label_fields/text.phtml",
        "website"        => "inline_label_fields/website.phtml",
        "radio"          => "inline_label_fields/radio.phtml",
        "dropdown"       => "inline_label_fields/dropdown.phtml",
        "paragraph"      => "inline_label_fields/textarea.phtml",
        "email"          => "inline_label_fields/email.phtml",
        "date"           => "inline_label_fields/date.phtml",
        "time"           => "inline_label_fields/time.phtml",
        "checkboxes"     => "inline_label_fields/checkboxes.phtml",
        "number"         => "inline_label_fields/number.phtml",
        "price"          => "inline_label_fields/price.phtml",
        "section_break"  => "inline_label_fields/section_break.phtml",
        "address"        => "inline_label_fields/address.phtml",
        "file_upload"    => "inline_label_fields/file.phtml",
        "multifile_upload"    => "inline_label_fields/multi_files.phtml",
        "model_dropdown" => "inline_label_fields/model_dropdown.phtml",
        "subscription"   => "inline_label_fields/subscription.phtml",
        "rating"         => "inline_label_fields/rating.phtml",
        "google_map"     => "inline_label_fields/google_map.phtml",
        "html"           => "inline_label_fields/html.phtml",
        "product_field"  => "inline_label_fields/product_field.phtml"
    ];

     /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => false,
            'cache_tags' => ['lofformbuilder']
            ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [];
    }
    
    public function setCurrentForm($form)
    {
        $this->form = $form;
        return $this;
    }
  
    public function getCurrentForm()
    {
        if (!isset($this->form) || !$this->form ) {
            $store = $this->_storeManager->getStore();
            $form = $this->registry->registry('current_form');
            $form_id = $this->request->getParam("form_id");
            if(!$form && $form_id) {
                $form = $this->_form->setStore($store)->load((int)$form_id);
                if (!$form->getId()) {
                    return null;
                }
                $customergroups = $form->getData('customergroups');
                $customerGroupId = $this->_customerSession->getCustomerGroupId();

                if (!in_array(0, $customergroups) && !$this->_customerSession->isLoggedIn()) {
                    return null;
                }

                if (!in_array($customerGroupId, $customergroups)) {
                    return null;
                }

                if (!$form->getStatus()) {
                    return null;
                }
            }
            $this->form = $form;
        }
        return $this->form;
    }
    
    public function getField($form, $fieldType, $fieldData, $is_inline_label = false)
    {
        if($is_inline_label){
            $fieldArr = $this->_inline_label_fields;
        } else {
            $fieldArr = $this->_fields;
        }
        
        $html = '';
        if (array_key_exists($fieldType, $fieldArr )) {
            $template = $fieldArr[$fieldType];
            if (isset($fieldData['custom_template']) && $fieldData['custom_template']!='') {
                $template = $fieldData['custom_template'];
            }
            $html = $this->getLayout()->createBlock('\Lof\Formbuilder\Block\Field')
            ->setData('field_data',$fieldData)
            ->setForm($form)
            ->setTemplate($template)
            ->toHtml();
        }
        return $html;
    }

    public function getFormAction()
    {
        return $this->getUrl('formbuilder/form/post');
    }

    public function getConfig($key, $default = '')
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        $result = $this->formHelper->getConfig($key);
        if ($result!= null) {
            return $result;
        }
        return $default;
    }

    public function getCurrentUrl()
    {
        $url = $this->url->getCurrentUrl();
        return $url;
    }
}
