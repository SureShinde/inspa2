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
namespace Magebest\Brand\Model\ResourceModel\Group;

use \Magebest\Brand\Model\ResourceModel\AbstractCollection;
/**
 * Brand collection
 */
class Collection extends AbstractCollection
{

	/**
     * @var string
     */
	protected $_idFieldName = 'group_id';

	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('Magebest\Brand\Model\Group', 'Magebest\Brand\Model\ResourceModel\Group');
		$this->_map['fields']['group_id'] = 'main_table.group_id';
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
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}