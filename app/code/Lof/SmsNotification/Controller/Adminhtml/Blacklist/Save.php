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

namespace Lof\SmsNotification\Controller\Adminhtml\Blacklist;


class Save extends \Lof\SmsNotification\Controller\Adminhtml\Blacklist
{
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
 
     

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
        ) {

        parent::__construct($context, $coreRegistry); 
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();   
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                $model = $this->_objectManager->create('Lof\SmsNotification\Model\Blacklist');
                $id = $this->getRequest()->getParam('blacklist_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong blacklist is specified.'));
                    }
                }

                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
 
                $session->setPageData($model->getData());
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Blacklist.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('lofsmsnotification/*/edit', ['blacklist_id' => $model->getId()]);
                    return;
                }
                $this->_redirect('lofsmsnotification/*/');
                return;

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('blacklist_id');
                if (!empty($id)) {
                 $this->_redirect('lofsmsnotification/*/edit', ['blacklist_id' => $id]);
                 } else {
                     $this->_redirect('lofsmsnotification/*/new');
                 }
                return;
             } catch (\Exception $e) {
                die($e->getMessage());
                $this->messageManager->addError(
                 __('Something went wrong while saving the rule data. Please review the error log.')
                 );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('lofsmsnotification/*/edit', ['blacklist_id' => $this->getRequest()->getParam('blacklist_id')]);
            }
        }
    return $resultRedirect->setPath('*/*/');
    }
}