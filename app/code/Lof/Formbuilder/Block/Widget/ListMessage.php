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

namespace Lof\Formbuilder\Block\Widget;

class ListMessage extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Lof\Formbuilder\Model\Message
     */
    protected $_message;

    /**
     * ListMessage constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\Formbuilder\Model\Message $message
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Formbuilder\Model\Message $message,
        array $data = []
        ) {
        $this->_message = $message;
        parent::__construct($context, $data);
    }
    
    public function _toHtml(){
        $grid_pagination = $this->getConfig('grid_pagination');
        $formid = $this->getConfig('formid');
        $template = 'Lof_Formbuilder::widget/list.phtml';
        if ($blockTemplate = $this->getConfig('block_template')) {
            $template = $blockTemplate;
        }
        $this->setTemplate($template);
        $item_per_page = (int)$this->getConfig('item_per_page');
        $store = $this->_storeManager->getStore();
        $collection = $this->_message->getCollection();
        $collection->getSelect()->where('main_table.form_id ='. $formid)->order('message_id DESC');
        if ($grid_pagination) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager','my.custom.pager');
            $pager->setLimit($item_per_page)->setCollection($collection);
            $this->setChild('pager', $pager);
        }
        $this->setCollection($collection);
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_postCollection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_postCollection;
    }

    public function getConfig($key, $default = '')
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $default;
    }
}
