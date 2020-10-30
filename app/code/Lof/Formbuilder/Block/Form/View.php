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

namespace Lof\Formbuilder\Block\Form;

class View extends \Lof\Formbuilder\Block\Form
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_helper;

    /**
     * @var [type]
     */
    protected $_collection;

    /**
     * Store manager
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context               
     * @param \Lof\Formbuilder\Helper\Data                     $helper 
     * @param \Magento\Framework\Registry                      $registry        
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
        parent::__construct($context, $registry, $helper, $form, $url, $request, $customerSession, $data);
        $this->_helper       = $helper;
        $this->assetRepository = $context->getAssetRepository();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $form = $this->getCurrentForm();
        if ($customTemplate = trim($form->getData('custom_template'))) {
            $this->setTemplate($customTemplate);
        }
        return parent::_beforeToHtml();
    }

    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $form = $this->getCurrentForm();
        $page_title = $form->getPageTitle();
        if ($page_title == '') {
            $page_title = $form->getTitle();
        }
        $route = $this->_helper->getConfig('general_settings/route');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => $baseUrl
                ]
                );
            $breadcrumbsBlock->addCrumb(
                'lofformbuilder',
                [
                    'label' => trim($page_title),
                    'title' => trim($page_title),
                    'link'  => ''
                ]
                );
        }
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    protected function _prepareLayout()
    {
        $form = $this->getCurrentForm();
        if($form){
            $page_title = $form->getPageTitle();
            if ($page_title=='') {
                $page_title = $form->getTitle();
            }
            $meta_description = $form->getMetaDescription();
            $meta_keywords = $form->getMetaKeywords();

            $this->_addBreadcrumbs();
            $this->pageConfig->addBodyClass('formbuilder-form-' . $form->getIdentifier());
            if ($page_title) {
                $this->pageConfig->getTitle()->set($page_title);
            }
            if ($meta_keywords) {
                $this->pageConfig->setKeywords($meta_keywords);
            }
            if ($meta_description) {
                $this->pageConfig->setDescription($meta_description);
            }
        }
        return parent::_prepareLayout();
    }
    public function _toHtml() {
        $store = $this->_storeManager->getStore();
        $html = $form = '';
        $form = $this->getCurrentForm();
        if($form) {
            $customergroups = $form->getData('customergroups');
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
            if (!in_array(0, $customergroups) && !$this->_customerSession->isLoggedIn()) {
                return null;
            }
            if (!in_array(0, $form->getStores()) && !in_array($store->getId(), $form->getStores())) {
                return null;
            }
            if (!in_array($customerGroupId, $customergroups)) {
                return null;
            }
            if (!$form->getStatus()) {
                return null;
            }
        }
        return parent::_toHtml();
    }
}
