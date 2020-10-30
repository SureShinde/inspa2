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

namespace Lof\Formbuilder\Block\Widget;

class Form extends \Lof\Formbuilder\Block\Form implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Lof\Formbuilder\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\Formbuilder\Model\Menu
     */
    protected $_form;

    /**
     * Store manager
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepository;
    protected $httpContext;

    /**
     * Form constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\Formbuilder\Helper\Data $helper
     * @param \Lof\Formbuilder\Model\Form $form
     * @param \Magento\Framework\Url $url
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
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
        $this->_helper = $helper;
        $this->assetRepository = $context->getAssetRepository();
        $my_template = "widget/form.phtml";
        if ($this->hasData("block_template") && $this->getData("block_template")) {
            $my_template = $this->getData("block_template");
        }
        $this->setTemplate($my_template);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => false,
            'cache_tags' => ['lofformbuilderwidget']
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


    public function _toHtml() {
        $store = $this->_storeManager->getStore();
        $html = $form = '';
        if ($formId = $this->getData('formid')) {
            $form = $this->_form->setStore($store)->load((int)$formId);
        } elseif($formIdentifier = $this->getData('identifier')){
            $form = $this->_form->setStore($store)->loadByAlias($formIdentifier);
        }
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
            $this->setCurrentForm($form);
        }
        return parent::_toHtml();
    }
    public function getFormUrl($url_key) {
        return $this->url->getUrl($url_key);
    }
}
