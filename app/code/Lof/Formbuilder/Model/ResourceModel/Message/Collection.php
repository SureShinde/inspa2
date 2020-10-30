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

namespace Lof\Formbuilder\Model\ResourceModel\Message;

use \Lof\Formbuilder\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'message_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        //$this->performAfterLoad('lof_formbuilder_form_store', 'form_id');
        $this->getFormNameAfterLoad();
        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Formbuilder\Model\Message', 'Lof\Formbuilder\Model\ResourceModel\Message');
        $this->_map['fields']['form_name'] = 'form_table.title';
        $this->addFilterToMap('form_name', 'form_table.title');
    }

    protected function getFormNameAfterLoad()
    {
        $items = $this->getColumnValues("message_id");       
        if (count($items)) {
            $connection = $this->getConnection();
             foreach ($this as $item) {
                 $formId = $item->getData('form_id');
                 if(empty($formId)){
                    $item->setData('form_name','');
                 }else{
                    $select = $connection->select()->from(['form' => $this->getTable('lof_formbuilder_form')])->where('form.form_id = (?)', $formId);
                    $result = $connection->fetchRow($select);
                    $item->setData('form_name',$result['title']);
                 }
             }
          }
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
        $this->joinFormRelationTable('lof_formbuilder_form', 'form_id');
        parent::_renderFiltersBefore();
    }
}
