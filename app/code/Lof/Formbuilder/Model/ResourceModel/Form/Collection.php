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

namespace Lof\Formbuilder\Model\ResourceModel\Form;

use \Lof\Formbuilder\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'form_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('lof_formbuilder_form_store', 'form_id');
        $this->loadCustomerGroup();
        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Formbuilder\Model\Form', 'Lof\Formbuilder\Model\ResourceModel\Form');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function loadCustomerGroup()
    {
        $connection     = $this->getConnection();
        $select         = $connection->select()->from($this->getTable('lof_formbuilder_form_customergroup'));
        $customerGroups = $connection->fetchAll($select);
        foreach ($this as $item) {
            $groups = [];
            foreach ($customerGroups as $k => $v) {
                if ($v['form_id']==$item->getId()) {
                    $groups[] = $v['customer_group_id'];
                }
            }
            $item->setData('customer_group_ids', $groups);
            $item->setData('customergroups', $groups);
        }
    }

    /**
     * Returns pairs form_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('form_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('lof_formbuilder_form_store', 'form_id');
    }
    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['formbuilder_entity_store' => $this->getTable($tableName)])
                ->where('formbuilder_entity_store.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $entityId = $item->getData($columnName);
                    if (!isset($result[$entityId])) {
                        continue;
                    }
                    if ($result[$entityId] == 0) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData($columnName)];
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store', [$result[$entityId]]);
                    $item->setData('stores', $result[$entityId]);
                    $item->setData('store_id', $result[$entityId]);
                }
            }
        }
    }
}
