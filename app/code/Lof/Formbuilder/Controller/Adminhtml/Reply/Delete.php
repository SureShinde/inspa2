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

namespace Lof\Formbuilder\Controller\Adminhtml\Reply;

class Delete extends \Lof\Formbuilder\Controller\Adminhtml\Reply
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('reply_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Lof\Formbuilder\Model\Reply');
                $model->load($id);
                $model->delete();
                // display success replies
                $this->messageManager->addSuccess(__('You deleted the replies.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error replies
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/');
            }
        }
        // display error replies
        $this->messageManager->addError(__('We can\'t find a replies to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
     /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Formbuilder::reply_delete');
    }
}
