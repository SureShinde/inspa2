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
namespace Magebest\Brand\Block\Product;
use Magento\Customer\Model\Context as CustomerContext;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_brandCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_brandHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry        
     * @param \Magebest\Brand\Helper\Data                           $brandHelper     
     * @param \Magebest\Brand\Model\Brand                           $brandCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magebest\Brand\Helper\Data $brandHelper,
        \Magebest\Brand\Model\Brand $brandCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->_brandCollection = $brandCollection;
        $this->_brandHelper = $brandHelper;
        $this->_coreRegistry = $registry;
        $this->_resource = $resource;
        parent::__construct($context, $data); 
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getBrandCollection(){
        $product = $this->getProduct();
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('magebest_brand_product');
        $brandIds = $connection->fetchCol(" SELECT brand_id FROM ".$table_name." WHERE product_id = ".$product->getId());
        if($brandIds || count($brandIds) > 0) {
            $collection = $this->_brandCollection->getCollection()
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('brand_id IN (?)', $brandIds);
            return $collection;
        }
        return false;
    }
    // Add by Muhib
    public function getBrandLabel($product){
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('magebest_brand_product');
        $brandIds = $connection->fetchCol(" SELECT brand_id FROM ".$table_name." WHERE product_id = ".$product->getId());
        if($brandIds || count($brandIds) > 0) {
            $collection = $this->_brandCollection->getCollection()
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('brand_id IN (?)', $brandIds);
            return $collection;
        }
        return false;
    }
    // End By Muhib

    public function _toHtml(){
        if(!$this->_brandHelper->getConfig('product_view_page/enable_brand_info')) return;

        return parent::_toHtml();
    }

}