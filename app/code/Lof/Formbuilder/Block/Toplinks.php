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

namespace Lof\Formbuilder\Block;

class Toplinks extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Lof\Formbuilder\Model\Modelcategory
	 */
	private $_formCategory;

	/**
	 * @var \Lof\Formbuilder\Model\Model
	 */
	private $_form;

    /**
     * @var \Lof\Formbuilder\Helper\Data
     */
	protected $_helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
	protected $customerSession;

    /**
     * Toplinks constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\Formbuilder\Model\Modelcategory $modelCategory
     * @param \Lof\Formbuilder\Model\Form $form
     * @param \Lof\Formbuilder\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Lof\Formbuilder\Model\Modelcategory $modelCategory,
		\Lof\Formbuilder\Model\Form $form,
		\Lof\Formbuilder\Helper\Data $helper,
		\Magento\Customer\Model\Session $customerSession,
		array $data = [])
    {
		parent::__construct($context, $data);
		$this->_formCategory   = $modelCategory;
		$this->_form           = $form;
		$this->_helper         = $helper;
		$this->customerSession = $customerSession;
	}

	/**
     * Render block HTML
     *
     * @return string
     */
	protected function _toHtml()
	{
		$store = $this->_storeManager->getStore();
		if (!$this->_helper->getConfig('general_settings/enable')){
            return '';
        }
		$collection = $this->_form->getCollection();
		$collection->addFieldToFilter("status", 1)->addFieldToFilter("show_toplink", 1);
		$link  = '';
		$route = $this->_helper->getConfig('general_settings/route');

		if ($route!=''){
            $route = $route . '/';
        }
		if ($collection->getSize()) {
			$customerGroupid = $this->customerSession->getCustomerGroupId();
			foreach ($collection as $item) {
				$groups = $item->getData('customergroups');
				$groups = is_array($groups)?$groups:explode(",", $groups);
				$stores = $item->getStores();
				$stores = is_array($stores)?$stores:explode(",",$stores);
				$form_id=$item['form_id'];
				if (in_array($customerGroupid, $groups) && (in_array(0, $stores) || in_array($store->getId(), $stores))) {
					$link .= '<li><a href="' . $this->getUrl($route . $item->getData('identifier')) . '"> ' . $this->escapeHtml($item->getTitle()) . ' </a></li>';
//					$link .= '<li><a href="' . $this->getUrl($route .'form/view/'.'form_id/'.$form_id) . '"> ' . $this->escapeHtml($item->getTitle()) . ' </a></li>';
				}
			}
		}
		return $link;
	}

	public function addCustomFormLinks(){
		$parentBlock = $this->getParentBlock();
		if ($parentBlock) {
        	//get Form Collection
			$collection = $this->_form->getCollection();
			$collection->addFieldToFilter("status", 1)
			->addFieldToFilter("show_toplink", 1);
			$link = '';
			if ($collection->getSize()) {
				foreach($collection as $item) {
					$link .= '<a href="' . $item->getFormLink() . '"> ' . $this->escapeHtml($item->getTitle()) . ' </a>';
				}
			}
		}
		return $this;
	}
}
