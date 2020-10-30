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

namespace Lof\Formbuilder\Controller\Adminhtml\Message;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class Ajaxblock extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var DataPersistorInterface
     */
    private $transportBuilder;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    protected $_helper;

    /**
     * @var
     */
    protected $message;

    /**
     * Ajaxblock constructor.
     * @param Action\Context $context
     * @param \Lof\Formbuilder\Model\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Lof\Formbuilder\Helper\Data $helper
     */
    public function __construct(
        Action\Context $context,
        \Lof\Formbuilder\Model\TransportBuilder $transportBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Lof\Formbuilder\Helper\Data $helper
        ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
        $this->_helper           = $helper;
        $this->transportBuilder  = $transportBuilder;
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
            $model = $this->_objectManager->create('Lof\Formbuilder\Model\Reply');
            $email_from = $this->getRequest()->getParam('email_from');
            $email_to = $this->getRequest()->getParam('email_to');
            $form_id = $this->getRequest()->getParam('form_id');
            $form_name = $this->getRequest()->getParam('form_name');

            if ($email_from) {
                $model->loadByEmailFrom($email_from);
            }

            if ($email_to) {
                $model->loadByEmailTo($email_to);
            }
            $data['subject'] = strip_tags($data['subject']);
            $data['subject'] = trim($data['subject']);
            // init model and set data
            $model->setData($data);
            $this->sendEmail($data);
            // try to save it
            try {
                // save the data
                if ($form_id && !$form_name) {
                    $form_model = $this->_objectManager->create('Lof\Formbuilder\Model\Form')->load($form_id);
                    $form_name = $form_model->getTitle();
                    $model->setFormName($form_name);
                }
                $model->save();
                $reply_id = $model->getId();
                $success_model = $model->load($reply_id);

                $responseData['status'] = true;
                $responseData['success'] = __('You saved the reply message.');
                $responseData['error'] = "";
                $responseData['data'] = $success_model->getData();

                $responseData['data']['created_time'] = $this->_helper->FormatDateFormBuilder($responseData['data']['created_time']);
            } catch (\Exception $e) {
                $responseData['error'] = __('Have problem when save the reply message.');
            }
        }

        $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                    );
    }

    public function sendEmail($data) {
        $arg = array();
        $arg = $data;
        // SEND EMAIL
        $this->inlineTranslation->suspend();
        try {

            $this->transportBuilder
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]);

                $this->transportBuilder->setTemplateVars($arg);
                $this->transportBuilder->setTemplateData(
                [
                    'template_subject' => ($data['subject']),
                    'template_text' => ($data['message']),
                ]
                );
                 $this->transportBuilder->setFrom($this->_helper->getConfig('email_settings/sender_email_identity'));
                
                 $this->transportBuilder->addTo($data['email_to']);
                
            $this->prefixSubject = '';
          
            $transport = $this->transportBuilder->getTransport();
            
            
            try {
                $transport->sendMessage();

                $this->inlineTranslation->resume();
                 $this->messageManager->addSuccess(__('Email was successfully sent.'));
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
        }
    }
}
