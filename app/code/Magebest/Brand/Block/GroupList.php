<?php
/**
 * Magebest
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magebest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magebest.com/LICENSE.txt
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Magebest
 * @package    Magebest_Brand
 * @copyright  Copyright (c) 2014 Magebest (https://www.magebest.com/)
 * @license    https://www.magebest.com/LICENSE.txt
 */
namespace Magebest\Brand\Block;

class GroupList extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magebest\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @var \Magebest\Brand\Model\Brand
     */
    protected $_group;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Magebest\Brand\Helper\Data                           $brandHelper  
     * @param \Magebest\Brand\Model\Group                           $group        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magebest\Brand\Helper\Data $brandHelper,
        \Magebest\Brand\Model\Group $group,
        array $data = []
        ) { 
        $this->_group = $group;
        $this->_coreRegistry = $registry;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        if(!$this->getConfig('general_settings/enable_menu')) return;
        parent::_construct();
    }

    public function getGroupList(){
        $collection = $this->_group->getCollection()
        ->addFieldToFilter('status',1)
        ->addFieldToFilter('shown_in_sidebar',1)
        ->setOrder('position','ASC');
        return $collection;
    }
    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if(!$result){

            return $default;
        }
        return $result;
    }
}