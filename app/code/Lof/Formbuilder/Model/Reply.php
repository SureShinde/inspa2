<?php /**
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

namespace Lof\Formbuilder\Model;

class Reply extends \Magento\Framework\Model\AbstractModel
{
    /**#@+
     * Form's Statuses
     */
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    /**
     * @param \Magento\Framework\Model\Context                             $context            
     * @param \Magento\Framework\Registry                                  $registry           
     * @param \Lof\Formbuilder\Model\ResourceModel\Reply|null              $resource           
     * @param \Lof\Formbuilder\Model\ResourceModel\Reply\Collection|null   $resourceCollection 
     * @param array                                                        $data               
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Formbuilder\Model\ResourceModel\Reply $resource = null,
        \Lof\Formbuilder\Model\ResourceModel\Reply\Collection $resourceCollection = null,
        array $data = []
        ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Lof\Formbuilder\Model\ResourceModel\Reply');
    }

    public function loadListByMessageId($message_id = 0){
        return $this->getCollection()
                            ->addFieldToFilter('message_id', (int)$message_id);
    }

    public function loadListByFormId($form_id = 0){
        return $this->getCollection()
                            ->addFieldToFilter('form_id', (int)$form_id);
    }

    public function loadByEmailTo($email_to_address){
        return $this->getResource()->load($this, $email_to_address, 'email_to');
    }

    public function loadByEmailFrom($email_from_address){
        return $this->getResource()->load($this, $email_from_address, 'email_from');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Sent'), self::STATUS_DISABLED => __('Un Sent')];
    }
}