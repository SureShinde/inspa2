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
namespace Lof\SmsNotification\Controller\Adminhtml\Smslog;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
class Exportcsv extends \Lof\SmsNotification\Controller\Adminhtml\Smslog
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
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
    }


     public function execute()
    {
         /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();   
        try {
            // init model and delete
            $collection = $this->_objectManager->create('Lof\SmsNotification\Model\Smslog')->getCollection();
            $params = [];
            foreach ($collection as $key => $model) {
                $params[] = $model->getData();
             }
             $name = 'list_sms';
            $file = 'export/smsnotification/'. $name . '.csv';
           
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            $headers = array('To','From','Message','Status','Created At');
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $rowData = $fields;
                foreach($row as $v){
                    $rowData['to'] = $row['to'];
                    $rowData['from'] = $row['from'];
                    $rowData['message'] = strip_tags($row['message']);
                    $rowData['status'] = $row['status'];
                    $rowData['created_at'] = $row['created_at'];
                    
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
             // display success message
            $this->messageManager->addSuccess(__('You export sms to csv success.'));
            return $this->fileFactory->create($name . '.csv', $file, 'var');
            
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // go back to edit form
            return $resultRedirect->setPath('*/*/index');
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a smslog to exportcsv.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

        /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_SmsNotification::smslog_exportcsv');
    }
}
