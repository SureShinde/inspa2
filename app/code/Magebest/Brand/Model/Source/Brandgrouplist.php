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
namespace Magebest\Brand\Model\Source;

class Brandgrouplist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magebest\Brand\Model\Group
     */
    protected  $_group;
    
    /**
     * 
     * @param \Magebest\Brand\Model\Group $group
     */
    public function __construct(
        \Magebest\Brand\Model\Group $group
        ) {
        $this->_group = $group;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_group->getCollection()
        ->addFieldToFilter('status', '1');
        $groupList = array();
        foreach ($groups as $group) {
            $groupList[] = array('label' => $group->getName(),
                'value' => $group->getId());
        }
        return $groupList;
    }
}
