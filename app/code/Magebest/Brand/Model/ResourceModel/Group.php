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
namespace Magebest\Brand\Model\ResourceModel;

class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Store model
	 *
	 * @var \Magento\Store\Model\Store
	 */
	protected $_store = null;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $_date;

	/**
	 * Store manager
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\Stdlib\Datetime
	 */
	protected $dateTime;

	/**
	 * @var \Magebest\Brand\Model\ResourceModel\Group\Collection
	 */
	protected $collection;

	/**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		$connectionName = null
		) {
		parent::__construct($context, $connectionName);
		$this->_date = $date;
		$this->_storeManager = $storeManager;
		$this->dateTime = $dateTime;
	}

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
    	$this->_init('magebest_brand_group','group_id');
    }

    /**
     * Process group data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('magebest_brand_group'),
            'url_key'
            )
        ->where(
            'url_key = ?',
            $object->getUrlKey()
            )
        ->where(
            'group_id != ?',
            $object->getId()
            );
        $result = $connection->fetchCol($select);
        if(count($result)>0)
        {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('URL key already exists.'.count($result))
                );  
        }

        return parent::_beforeSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field))
        {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

}