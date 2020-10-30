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

namespace Lof\Formbuilder\Block\Adminhtml;

class FormMenu extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Prepare button and grid
     *
     * @return \Lof\FollowUpEmail\Block\Adminhtml\Email
     */
    protected function _prepareLayout() {
        $this->buttonList->remove('save');
        $addButtonProps = [
            'id'           => 'save',
            'label'        => __('Save'),
            'class'        => 'add',
            'button_class' => '',
            'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options'      => $this->getEventTypes()
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Email' split button
     *
     * @return array
     */
    protected function getEventTypes() {
        $splitButtonOptions = [];
        return $splitButtonOptions;
    }

    /**
     * Retrieve email create url by specified email type
     *
     * @param string $type
     * @return string
     */
    protected function getEmailCreateUrl($type) {
        return $this->getUrl(
            'loffollowupemail/*/new',
            ['event_type' => $type]
        );
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode() {
        return $this->_storeManager->isSingleStoreMode();
    }
}
