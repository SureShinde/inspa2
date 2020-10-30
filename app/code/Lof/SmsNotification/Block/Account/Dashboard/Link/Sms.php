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
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmsNotification\Block\Account\Dashboard\Link;

class Sms extends \Magento\Framework\View\Element\Html\Link\Current
{

    /**
     * @var \Lof\SmsNotification\Helper\Data
     */
    protected $ticketData;

    protected $_unreadMessageCollection;

     protected $_messageFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\App\DefaultPathInterface      $defaultPath     
     * @param \Lof\SmsNotification\Helper\Data                    $ticketData     
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Lof\SmsNotification\Helper\Data $ticketData,
        \Lof\SmsNotification\Model\PhoneFactory $phoneFactory,
        array $data = []
        ) {
        parent::__construct($context, $defaultPath);
        $this->ticketData     = $ticketData;
        $this->_phoneFactory = $phoneFactory;
    }
   
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $html        = '';
         $message ='';
        $highlight   = '';
        //var_dump($this->getHref()); die();
        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

      
        $html = '<li class="lof_helpdesk nav item' . $highlight . ' lrw-nav-item"><a href="' . $this->escapeHtml($this->getHref()) . '"';
        $html .= $this->getTitle()
        ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
        : '';
        $html .= $this->getAttributesHtml() . '>';

        if ($this->getIsHighlighted()) {
            $html .= '<strong>';
        }

        $html .= '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . $message. '</span>';

        if ($this->getIsHighlighted()) {
            $html .= '</strong>';
        }
        $html .= '</a></li>';
        

        return $html;
    }
}