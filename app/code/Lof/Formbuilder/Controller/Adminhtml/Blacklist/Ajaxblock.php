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

namespace Lof\Formbuilder\Controller\Adminhtml\Blacklist;
use Lof\Formbuilder\Helper\Data;

class Ajaxblock extends \Lof\Formbuilder\Controller\Adminhtml\Blacklist
{
    protected $_formatDate;
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry,Data $formatDate)
    {
        parent::__construct($context, $coreRegistry);
        $this->_formatDate=$formatDate;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $responseData = [];
        $responseData['error'] = __('Don\'t have data to save.');
        $responseData['status'] = false;
        $responseData['data'] = [];
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->_objectManager->create('Lof\Formbuilder\Model\Blacklist');
            $email = $this->getRequest()->getParam('email');
            $ip = $this->getRequest()->getParam('ip');
            $form_id = $this->getRequest()->getParam('form_id');
            $form_name = $this->getRequest()->getParam('form_name');

            if ($email) {
                $model->loadByEmail($email);
            }
            if ($ip && !$model->getId()) {
                $model->loadByIp($ip);
            }
            if (!$model->getId()) {
                // init model and set data
                $model->setData($data);
                // try to save it
                try {
                    // save the data
                    if ($form_id && !$form_name) {
                        $form_model = $this->_objectManager->create('Lof\Formbuilder\Model\Form')->load($form_id);
                        $form_name = $form_model->getTitle();
                        $model->setFormName($form_name);
                    }
                    $model->save();
                    $id_blacklist = $model->getData()['blacklist_id'];
                    $currentTime = $model->load($id_blacklist)->getData();

                    $responseData['status'] = true;
                    $responseData['success'] = __('You saved the blacklist.');
                    $responseData['error'] = "";
                    $responseData['created_time'] = $currentTime['created_time'];
                    $responseData['data'] = $model->getData();

                    
                } catch (\Exception $e) {
                    $responseData['error'] = __('Have problem when save the blacklist.');
                    //$responseData['error'] .= (string)$e;
                }
            } else {
                $responseData['error'] = __('The ip or email was added to blocklist.');
            }
        }
        if (isset($responseData['data']['created_time'])){
            $formatDate = $this->_formatDate->FormatDateFormBuilder($responseData['data']['created_time']);
            $responseData['data']['created_time']=$formatDate;
        }
        $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                    );
        
    }
}
