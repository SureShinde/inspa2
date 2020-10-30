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
namespace Magebest\Brand\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadProductBrand implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
        )
    {
        $this->_resource = $resource;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('magebest_brand_product');
        if($product->getId()) {
            $productIds = $connection->fetchCol(" SELECT brand_id FROM ".$table_name." WHERE product_id = ".$product->getId());
            $product->setData('product_brand', implode($productIds, ','));
        }
        
    }
}
