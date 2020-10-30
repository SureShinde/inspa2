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

namespace Lof\Formbuilder\Controller\Adminhtml\Form;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Save extends \Lof\Formbuilder\Controller\Adminhtml\Form
{

    protected $_layout;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        \Lof\Formbuilder\Model\Message $message,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->message = $message;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();

        // EXPORT TO CSV
        if ($this->getRequest()->getParam("export_csv") && $this->getRequest()->getParam('form_id')) {
            $id = $this->getRequest()->getParam('form_id');
            $model = $this->_objectManager->create('Lof\Formbuilder\Model\Form')->load($id);
            $messages = $this->message->getCollection();
            $messages->addFieldToFilter("form_id", (int)$id);
            $params = [];
            foreach ($messages as $message) {
                $params[] = json_decode($message->getFormData(), true);
            }
            $name = $model->getTitle() . '-messages';
            $file = 'export/formbuilder/'. $name . '.csv';
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            foreach ($params as $row) {
                foreach ($row as $v) {
                    if (!isset($fields[$v['cid']])) {
                        $fields[$v['cid']] = '';
                        $headers[] = $v['label'];
                    }
                }
            }
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $rowData = $fields;
                foreach($row as $v){
                    $rowData[$v['cid']] = strip_tags($v['value']);
                }
                $stream->writeCsv($rowData);
            }
            $stream->unlock();
            $stream->close();
            $file = [
                'type' => 'filename',
                'value' => $file,
                'rm' => true  // can delete file after use
            ];
            return $this->fileFactory->create($name . '.csv', $file, 'var');
        }
        if ($data) {
            if (!empty($data['design']))
            {
                $id = $this->getRequest()->getParam('form_id');
                $model = $this->_objectManager->create('Lof\Formbuilder\Model\Form')->load($id);
                if (!$model->getId() && $id) {
                    $this->messageManager->addError(__('This form no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }

                // init model and set data

                $model->setData($data);

                // try to save it
                try {
                    // save the data
                    $model->save();
                    // display success message
                    $this->messageManager->addSuccess(__('You saved the form.'));
                    // clear previously saved data from session
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                    if ($this->getRequest()->getParam("duplicate")) {
                        unset($data['form_id']);
                        $data['identifier'] = $data['identifier'].time();

                        $form = $this->_objectManager->create('Lof\Formbuilder\Model\Form');
                        $form->setData($data);
                        try {
                            $form->save();
                            $this->messageManager->addSuccess(__('You duplicated this form.'));
                            return $resultRedirect->setPath('*/*/edit', ['form_id' => $model->getId()]);
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\RuntimeException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e) {
                            $this->messageManager->addException($e, __('Something went wrong while duplicating the form.'));
                        }
                    }

                    if ($this->getRequest()->getParam("new")) {
                        return $resultRedirect->setPath('*/*/new');
                    }

                    // check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('*/*/edit', ['form_id' => $model->getId()]);
                    }
                    // go to grid
                    return $resultRedirect->setPath('*/*/');
                } catch (\Exception $e) {
                    // display error message
                    $this->messageManager->addError($e->getMessage());
                    // save data in session
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/edit', ['form_id' => $this->getRequest()->getParam('form_id')]);
                }
            }
            $this->getMessageManager()->addErrorMessage('Errors: No response fields');
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/');
    }
}
