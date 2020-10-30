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

namespace Lof\Formbuilder\Block\Adminhtml\Form\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Form Information'));


        $this->addTab(
                'main_section',
                [
                    'label' => __('Form Information'),
                    'content' => $this->getLayout()->createBlock('Lof\Formbuilder\Block\Adminhtml\Form\Edit\Tab\Main')->toHtml()
                ]
            );

        $this->addTab(
                'content_section',
                [
                    'label' => __('Form Content'),
                    'content' => $this->getLayout()->createBlock('Lof\Formbuilder\Block\Adminhtml\Form\Edit\Tab\Content')->toHtml()
                ]
            );

        $this->addTab(
                'creator_section',
                [
                    'label' => __('Form Creator'),
                    'content' => $this->getLayout()->createBlock('Lof\Formbuilder\Block\Adminhtml\Form\Edit\Tab\Creator')->toHtml()
                ]
            );

        $this->addTab(
                'meta_section',
                [
                    'label' => __('SEO'),
                    'content' => $this->getLayout()->createBlock('Lof\Formbuilder\Block\Adminhtml\Form\Edit\Tab\Meta')->toHtml()
                ]
            );

        $this->addTab(
                'design_section',
                [
                    'label' => __('Design'),
                    'content' => $this->getLayout()->createBlock('Lof\Formbuilder\Block\Adminhtml\Form\Edit\Tab\Design')->toHtml()
                ]
            );

        $this->addTab(
                'messages',
                [
                    'label' => __('Messages'),
                    'url' => $this->getUrl('formbuilder/*/messages', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
    }
}
