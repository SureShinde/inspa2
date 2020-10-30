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

namespace Lof\Formbuilder\Model\ResourceModel\Modelcategory;


class Collection extends \Lof\Formbuilder\Model\ResourceModel\AbstractCollection
{
     /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
     protected $storeManager;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connection
     * @param null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        $resource = null) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $storeManager, $connection, $resource);
    }

    protected $_idFieldName = 'category_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Formbuilder\Model\Modelcategory', 'Lof\Formbuilder\Model\ResourceModel\Modelcategory');
    }

    /**
     * Returns pairs form_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'title');
    }
    public function addStoreFilter($store, $withAdmin = true){
        return $this;
    }
}
