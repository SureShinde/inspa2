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

namespace Lof\Formbuilder\Controller\Form;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $_formFactory;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Lof\Formbuilder\Model\FormFactory         $formFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\Formbuilder\Model\FormFactory $formFactory)
    {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_coreRegistry        = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $form_id = $this->getRequest()->getParam("form_id");
        $form = $this->_coreRegistry->registry('current_form');
        if(!$form && $form_id) {
            $form = $this->_formFactory->create()->load((int)$form_id);
            $this->_coreRegistry->register("current_form", $form);
        }

        $page->addHandle(['type' => 'FFORMBUILDER_VIEW_'.$form->getFormId()]);
        if (($layoutUpdate = $form->getLayoutUpdateXml()) && trim($layoutUpdate)!='') {
            $page->addUpdate($layoutUpdate);
        }        $page_layout = $form->getPageLayout();
        if ($page_layout) {
            $page->getConfig()->setPageLayout($page_layout);
        }
        return $page;
    }
}