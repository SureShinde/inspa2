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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmsNotification\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Smsdebug extends \Magento\Framework\Model\AbstractModel
{

	protected $_logger;

    protected $_helper;
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context,
    	\Lof\SmsNotification\Logger\Logger $logger,
        \Lof\SmsNotification\Helper\Data $helper,
    	\Magento\Framework\Registry $registry,
    	ObjectManagerInterface $objectManager,
    	DateTime $coreDate
    ) {
    	$this->_logger = $logger;
    	$this->objectManager = $objectManager;
    	$this->coreDate = $coreDate;
        $this->_helper = $helper;
    	parent::__construct($context,$registry);
    }
    protected function _construct()
    {
        $this->_init('Lof\SmsNotification\Model\ResourceModel\Smsdebug');
    }
    public function messageDebug($message) 
    {
        if($this->_helper->getConfig('general_settings/enable_email_debug') == 1) {
            $this->setData([
                'created_at'        => date('Y-m-d H:i:s'),
                'message'           => $message,
            ]);
            $this->save();
        }
    }

    public function clearDebug() {
        $keep_email = $this->_helper->getConfig('clear/debug');
        if($keep_email > 0) {
            $time = time() - $keep_email*24*60*60;
            $time = date('Y-m-d H:i:s',$time);
             $collection=$this->getCollection()->addFieldToFilter('created_at',['lt' => $time]);
            foreach ($collection as $key => $_collection) {
                $_collection->delete();         
            }
        }
    }
}