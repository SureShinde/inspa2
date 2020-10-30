<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmsNotification\Model;

use Magento\Framework\ObjectManagerInterface;


class Blacklist extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context,
    	\Magento\Framework\Registry $registry
    ) {
    	parent::__construct($context,$registry);
    }
    protected function _construct()
    {
    	
        $this->_init('Lof\SmsNotification\Model\ResourceModel\Blacklist');
    }
}