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

namespace Lof\Formbuilder\Controller\Form;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    const FILE_TYPES = 'jpg,JPG,jpeg,JPEG,gif,GIF,png,PNG,doc,DOC,docx,DOCX,pdf,PDF,zip,ZIP,tar,TAR,rar,RAR,tgz,TGZ,7zip,7ZIP,gz,GZ';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Lof\Formbuilder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\Formbuilder\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Lof\Formbuilder\Model\Form
     */
    protected $form;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $resource;

    /**
     * @param Context                                              $context
     * @param \Magento\Store\Model\StoreManager                    $storeManager
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Lof\Formbuilder\Helper\Data                         $helper
     * @param \Magento\Framework\Controller\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Translate\Inline\StateInterface   $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder    $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig
     * @param \Lof\Formbuilder\Model\Form                          $form
     * @param \Magento\Framework\View\LayoutInterface              $layout
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param DataPersistorInterface                               $dataPersistor
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Formbuilder\Helper\Data $helper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lof\Formbuilder\Model\Form $form,
        \Lof\Formbuilder\Helper\Fields $formFieldHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        DataPersistorInterface $dataPersistor
    ) {
        $this->storeManager        = $storeManager;
        $this->resultPageFactory    = $resultPageFactory;
        $this->helper              = $helper;
        $this->_helper              = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry        = $registry;
        $this->inlineTranslation    = $inlineTranslation;
        $this->form                = $form;
        $this->formFieldHelper     = $formFieldHelper;
        $this->transportBuilder    = $transportBuilder;
        $this->scopeConfig          = $scopeConfig;
        $this->_layout              = $layout;
        $this->_customerSession     = $customerSession;
        $this->httpRequest          = $httpRequest;
        $this->remoteAddress       = $remoteAddress;
        $this->moduleManager       = $moduleManager;
        $this->resource            = $resource;
        $this->mediaDirectory       = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    protected function parse_size($size) {
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        return round($size);
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $store = $this->storeManager->getStore();
            $resultRedirect = $this->resultRedirectFactory->create();
            $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $error = false;
            $data = $this->getRequest()->getParams();
            if (empty($data)) {
                return $this->_redirect($store->getBaseUrl());
            }
            $current_url = isset($data['current_url'])?$data['current_url']:'';

            $enable_blacklist = $this->helper->getConfig('general_settings/enable_blacklist');
            //check if enabled config blacklist, then check if ip in blacklist, then redirect it to home, else continue action
            if ($enable_blacklist) {
                $client_ip = $this->remoteAddress->getRemoteAddress();
                $blacklist_model = $this->_objectManager->create('Lof\Formbuilder\Model\Blacklist');
                if ($client_ip) {
                    $blacklist_model->loadByIp($client_ip);
                    if ((0 < $blacklist_model->getId()) && $blacklist_model->getStatus()) {

                        $responseData = [];
                        $responseData['message'] =
                            __('Your IP was blocked in our blacklist. So, we will not allow submit the form.');
                        $responseData['status'] = false;
                        $this->messageManager->addError(
                            __('Your IP was blocked in our blacklist. So, we will not allow submit the form.')
                        );
                        $this->getResponse()->representJson(
                            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                        );
                        $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                        return ;
                    }
                }
            }

            if (count($data)>2) {
                $form = $this->form->load($data['formId']);
                $fields = $form->getFields();
                $successMessage = $this->helper->filter($form->getData('success_message'));

                // reCaptcha
                if (isset($_POST['g-recaptcha-response']) && ((int)$_POST['g-recaptcha-response']) === 0) {

                    if ($this->getRequest()->isAjax()) {
                        $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                        return;
                    }

                    $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                    $this->_redirect($data['return_url']);
                    $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                    return;
                }
                if (isset($_POST['g-recaptcha-response'])) {
                    $captcha = $_POST['g-recaptcha-response'];
                    $secretKey = $this->helper->getConfig('general_settings/captcha_privatekey');
                    $ip = $this->remoteAddress->getRemoteAddress();
                    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=" . $ip);
                    $responseKeys = json_decode($response,true);
                    if (intval($responseKeys["success"]) !== 1) {
                        if ($this->getRequest()->isAjax()) {
                            $responseData = [];
                            $responseData['message'] =__('Please check reCaptcha and try again.');
                            $responseData['status'] = false;
                            $this->getResponse()->representJson(
                                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                            );
                            $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                            return;
                        }

                        $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                        $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                        return;
                    }
                }


                // UPLOAD FILE
                $fieldPrefix = $this->helper->getFieldPrefix();
                if (!empty($fields)){
                    foreach ($fields as $key => $field) {
                        $cid        = $this->helper->getFieldId($field);
                        $field_name = $fieldPrefix . $cid . $form->getId();
                        $image      = $this->httpRequest->getFiles($field_name);
                        if (isset($image['error']) && $image['error'] == 0) {

                            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                                ->getDirectoryRead(DirectoryList::MEDIA);
                            $mediaFolder = 'lof/formbuilder/files';
                            $savePath = $mediaDirectory->getAbsolutePath($mediaFolder);
                            if (empty($field)) {
                                continue;
                            }
                            if ($field['field_type'] == 'file_upload') {
                                $fieldTypes = '';
                                if (isset($field['image_type'])) {
                                    $fieldTypes = $field['image_type'];
                                }
                                if (!$fieldTypes) {
                                    $fieldTypes = self::FILE_TYPES;
                                }
                                $fieldTypes = str_replace(" ", "", $fieldTypes);
                                if (!is_array($fieldTypes)) {
                                    $fieldTypes = explode(',', $fieldTypes);
                                }
                                $file = '';
                                $file_exists = false;
                                if ($_FILES && isset($_FILES[$field_name])) {
                                    if (file_exists($_FILES[$field_name]['tmp_name'])) {
                                        $file_exists = true;
                                    }
                                }
                                if (!isset($field['required']) || (isset($field['required']) && !$field['required'])) {
                                    if ($_FILES && isset($_FILES[$field_name])) {
                                        if (file_exists($_FILES[$field_name]['tmp_name'])) {
                                            $file_exists = true;
                                        }
                                    }
                                }
                                if (!$file_exists && (!isset($field['required']) || (isset($field['required']) && !$field['required']))) {
                                    $file = '';
                                } else {
                                    $uploader = $this->_objectManager->create(
                                        'Magento\Framework\File\Uploader',
                                        array('fileId' => $field_name)
                                    );
                                    $uploader->setAllowedExtensions($fieldTypes);
                                    $uploader->setAllowRenameFiles(true);
                                    $uploader->setFilesDispersion(false);
                                    $file = $uploader->save($savePath);
                                }
                                if ($file && empty($file)) {
                                    continue;
                                }

                                if ($file && !empty($file)) {
                                    try {
                                        $field_label = isset($field['label'])?$field['label']:'';

                                        $image_maximum_size = $this->parse_size(@ini_get('upload_max_filesize'));
                                        if ($image_maximum_size <= 0) {
                                            $image_maximum_size = 2;
                                        }
                                        if ($field && isset($field['image_maximum_size']) && $field['image_maximum_size']) {
                                            $image_maximum_size = $field['image_maximum_size'];
                                        }

                                        if (isset($field['image_maximum_size']) && ($image_maximum_size * 1024 * 1024) < $file['size']) {
                                            $this->messageManager->addError(__($field_label . ' - The file is too big.'));
                                            $this->mediaDirectory->delete('lof/formbuilder/files/' . $file['file']);
                                            $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                                            return;
                                        }

                                        $imgExtens                       = array("gif", "jpeg", "jpg", "png");
                                        $temp                            = explode(".", $file['file']);
                                        $extension                       = end($temp);
                                        $data[$field_name]               = $field_name;
                                        $data[$field_name . '_filename'] = $file['file'];
                                        $data[$field_name . '_fileurl']  = $mediaUrl . $mediaFolder . '/' . $file['file'];
                                        $data[$field_name . '_filesize'] = $file['size'];
                                        if (in_array($extension, $imgExtens)) {
                                            $data[$field_name . '_isimage'] = true;
                                        }
                                    } catch (Exception $e) {
                                        if ($this->getRequest()->isAjax()) {
                                            $responseData = [];
                                            $responseData['message'] =__($field_label) . ' - ' . $e->getMessage();
                                            $responseData['status'] = false;
                                            $this->getResponse()->representJson(
                                                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                            );
                                        }
                                        $this->messageManager->addError(__($field_label) . ' - ' . $e->getMessage());
                                        $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                                        $this->_redirect($data['return_url']);
                                        return;
                                    }
                                }
                            }
                        }
                    }
                }

                //Build email data object
                $customform_data = $form->getCustomFormFields($data);
                $creationTime = date('Y-m-d H:i:s');
                $message_html = $this->_layout->createBlock('\Magento\Framework\View\Element\Template')
                    ->setTemplate("Lof_Formbuilder::email/items.phtml")
                    ->setCustomFormData($customform_data)
                    ->setCreationTime($creationTime)
                    ->toHtml();

                $show_all_fields = $this->helper->getConfig('email_settings/show_all_fields');
                if(!$show_all_fields) {
                    $data['message'] = $this->_layout->createBlock('\Magento\Framework\View\Element\Template')
                        ->setTemplate("Lof_Formbuilder::email/items_check_enable.phtml")
                        ->setCustomFormData($customform_data)
                        ->setCreationTime($creationTime)
                        ->toHtml();
                } else {
                    $data['message'] = $message_html;
                }

                /* Format form data to save in message params */
                $form_submit_data = [];
                if ($customform_data) {
                    foreach ($customform_data as $key => $val) {
                        if (isset($form_submit_data[$val['label']])) {
                            $val['label'] .= " " . $key;
                        }
                        $form_submit_data[$val['label']] = $val['value'];
                    }
                    $this->_eventManager->dispatch('formbuilder_init_post_data', ['form_data' => $customform_data]);
                }
                //if enable check blacklist, then check all emails in form was in blacklist or not, if have one email in black list, then redirect it to homepage, else continue action
                if ($form_submit_data && $enable_blacklist) {
                    $emails = $this->helper->getEmailsFromData($form_submit_data);
                    if ($emails) {
                        $blacklist_collection = $this->_objectManager->create('Lof\Formbuilder\Model\Blacklist')->getCollection()->addFieldToFilter("status", 1);
                        $blacklist_collection->addEmailsToFilter($emails);

                        if ($blacklist_collection->getSize()) {
                            $responseData = [];
                            $responseData['message'] = __('One or more emails in form were blocked in our blacklist. So, we will not allow submit the form.');
                            $responseData['status'] = false;
                            $this->messageManager->addError(__('One or more emails in form were blocked in our blacklist. So, we will not allow submit the form.'));
                            $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                            $this->getResponse()->representJson(
                                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                            );

                            return;
                        }
                    }
                }

                $storeScope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $uCode        = $this->helper->getConfig('email_settings/sender_email_identity');
                $sender_email = $this->scopeConfig->getValue('trans_email/ident_' . $uCode . '/name', $storeScope);

                //Save message
                $message_data                    = [];
                $message_data['form_id']         = $form->getFormId();
                $message_data['ip_address']      = $this->remoteAddress->getRemoteAddress();
                $message_data['ip_address_long'] = $this->remoteAddress->getRemoteAddress(true);
                $message_data['customer_id']     = $this->_customerSession->getCustomerId();

                $params                          = [];
                $params['brower']                = $this->getRequest()->getServer('HTTP_USER_AGENT');
                $params['http_host']             = $this->getRequest()->getHttpHost();
                $params['submit_data']           = $form_submit_data;
                $params['current_url']           = $current_url;
                $message_data['params']          = serialize($params);
                $message_data['message']         = $message_html;
                $message_data['creation_time']   = $creationTime;
                $message_data['email_from']      = $sender_email;
                $message_data['form_data']       = json_encode($customform_data);

                $message = $this->_objectManager->create('Lof\Formbuilder\Model\Message');
                $message->setData($message_data);
                $message->save();
                if ($this->moduleManager->isEnabled('Lof_HidePrice') && isset($data['hideprice_id']) && $data['hideprice_id']) {
                    $connection = $this->resource->getConnection();
                    $table      = $this->resource->getTableName('lof_hideprice_hideprice_message');
                    $_data      = [];
                    $_data[] = [
                        'hideprice_id' => $data['hideprice_id'],
                        'entity_id'    => $data['entity_id'],
                        'message_id'   => $message->getMessageId()
                    ];
                    $connection->insertMultiple($table, $_data);
                }

                if($message->getMessageId()) {
                    $this->_eventManager->dispatch('formbuilder_saved_message', ['message' => $message]);
                }
                // SEND EMAIL
                $this->inlineTranslation->suspend();
                // Update Data Field Value if array will convert to string
                $field_prefix = $this->formFieldHelper->getFieldPrefix();
                foreach($data as $data_key => $data_value){
                    if(false !== strpos($data_key, $field_prefix)){
                        if(is_array($data_value)){
                            $data[$data_key] = implode(", ", $data_value);
                        }  
                    }
                }
                $emails = $form->getData('email_receive');
                $reply_emails = "";
                $reply_email_arr = [];
                if ($fields) {
                    foreach ($fields as $fitem) {
                        $field_type  = isset($fitem['field_type'])?$fitem['field_type']:"";
                        $cid = $this->helper->getFieldId($fitem);
                        $field_id    = $fieldPrefix . $cid . $form->getId();
                        if ($field_type == "email" && isset($data[$field_id]) && $data[$field_id] && filter_var($data[$field_id], FILTER_VALIDATE_EMAIL)) {
                            $reply_email_arr[] = trim($data[$field_id]);
                        }
                    }
                    $reply_emails = implode(",", $reply_email_arr);
                }
                if (trim($emails)!='') {
                    $fromAddress = $this->_helper->getConfig('email_setting/sender_email_identity');
                    $fromAddress = $fromAddress === null ? 'general' : $fromAddress;
                    //If form have custom sender, will get email and name on the submitted form data
                    $sender_email_field = $form->getData('sender_email_field');
                    $sender_name_field = $form->getData('sender_name_field');
                    if($sender_email_field) {
                        $sender_email_field = $this->formFieldHelper->getFieldPrefix() . $sender_email_field . $form->getId();
                        if (isset($data[$sender_email_field]) && $data[$sender_email_field] && filter_var($data[$sender_email_field], FILTER_VALIDATE_EMAIL)) {
                            $fromAddress = ['email' => $data[$sender_email_field]];

                            $sender_name_field = $this->formFieldHelper->getFieldPrefix() . $sender_name_field . $form->getId();
                            if (isset($data[$sender_name_field]) && $data[$sender_name_field]){
                                $fromAddress['name'] = $data[$sender_name_field];
                            }

                        }
                    }

                    $emails = explode(',', $emails);
                    $this->_eventManager->dispatch('formbuilder_init_email_data', ['data' => $data, 'form'=>$form, 'from_address'=>$fromAddress, 'emails' => $emails]);

                    foreach ($emails as $k => $v) {
                        try {
                            $postObject = new \Magento\Framework\DataObject();
                            $data['form_id'] = $form->getId();
                            $data['title'] = $form->getData('title');
                            $data['current_url'] = $current_url;
                            $postObject->setData($data);
                            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                            $v = trim($v);
                            $transportBuilder = $this->transportBuilder
                                ->setTemplateIdentifier($form->getData('email_template'))
                                ->setTemplateOptions(
                                    [
                                        'area'  => 'frontend',
                                        'store' => $store->getId()
                                    ])
                                ->setTemplateVars(['data' => $postObject])
                                ->setFrom($fromAddress)
                                ->addTo($v);
                            if(is_array($fromAddress) && isset($fromAddress['email']) && $fromAddress['email']) {
                                $transportBuilder->setReplyTo($fromAddress['email']);
                            } else {
                                if($reply_email_arr) {
                                    $transportBuilder->setReplyTo($reply_email_arr[0]);
                                }
                            }

                            $transport = $transportBuilder->getTransport();

                            try  {
                                $transport->sendMessage();
                                $this->inlineTranslation->resume();
                            } catch(\Exception $e) {
                                $error = true;
                                $this->helper->writeLogData($e);
                                if ($this->getRequest()->isAjax()) {
                                    $responseData = [];
                                    $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                    $responseData['status'] = false;
                                    $this->getResponse()->representJson(
                                        $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                    );
                                }
                                $this->messageManager->addError(
                                    __('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.')
                                );
                                $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                            }
                        } catch (\Exception $e) {
                            $this->inlineTranslation->resume();
                            $this->helper->writeLogData($e);
                            if ($this->getRequest()->isAjax()) {
                                $responseData = [];
                                $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                $responseData['status'] = false;
                                $this->getResponse()->representJson(
                                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                );
                            }
                            $this->messageManager->addError(
                                __('Errors when send emails. We can\'t process your request right now. Sorry, that\'s all we know.')
                            );
                            $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                            return;
                        }
                    }
                }

                // SEND THANKYOU EMAIL
                $this->inlineTranslation->suspend();
                $field = $form->getData('thankyou_field');
                $send_thanks_email = $this->helper->getConfig('email_settings/send_thanks_email');
                $thanks_email_all = $this->helper->getConfig('email_settings/thanks_email_all');

                if ($send_thanks_email && $thanks_email_all && $fields) { //Send thanks you email to all email fields
                    $tmp_data = array();
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    foreach($fields as $fitem){
                        $field_type  = isset($fitem['field_type'])?$fitem['field_type']:"";
                        $cid = $this->helper->getFieldId($fitem);
                        $field_id    = $fieldPrefix . $cid . $form->getId();
                        if  ($field_type == "email" && isset($data[$field_id]) && $data[$field_id] && filter_var($data[$field_id], FILTER_VALIDATE_EMAIL) && $form->getData('thankyou_email_template')) {
                            try {
                                $postObject       = new \Magento\Framework\DataObject();
                                $data['form_id']  = $form->getFormId();
                                $data['title']    = $form->getData('title');
                                $data[$field_id] = trim($data[$field_id]);
                                $postObject->setData($data);
                                $transport = $this->transportBuilder
                                    ->setTemplateIdentifier($form->getData('thankyou_email_template'))
                                    ->setTemplateOptions(
                                        [
                                            'area'  => 'frontend',
                                            'store' => $store->getId()
                                        ])
                                    ->setTemplateVars(['data' => $postObject])
                                    ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                                    ->addTo($data[$field_id])
                                    ->getTransport();
                                try  {
                                    $transport->sendMessage();
                                    $this->inlineTranslation->resume();
                                } catch (\Exception $e) {
                                    $error = true;
                                    $this->helper->writeLogData($e);
                                    if ($this->getRequest()->isAjax()) {
                                        $responseData = [];
                                        $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                        $responseData['status'] = false;
                                        $this->getResponse()->representJson(
                                            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                        );
                                    }
                                    $this->messageManager->addError(
                                        __('An error when send thanks you email. We can\'t process your request right now. Sorry, that\'s all we know.')
                                    );
                                    $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                                }
                            } catch (\Exception $e) {
                                $this->helper->writeLogData($e);
                                $this->inlineTranslation->resume();
                                if ($this->getRequest()->isAjax()) {
                                    $responseData = [];
                                    $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                    $responseData['status'] = false;
                                    $this->getResponse()->representJson(
                                        $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                    );
                                }
                                $this->messageManager->addError(
                                    __('Errors when send thanks you emails. We can\'t process your request right now. Sorry, that\'s all we know.')
                                );
                                $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                                return;
                            }
                        }
                    }
                } elseif ($field && $send_thanks_email) { //Send thanks you email to selected email field

                    $field = $this->formFieldHelper->getFieldPrefix() . $field . $form->getId();
                    if (isset($data[$field]) && $data[$field] && filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        try {
                            $postObject       = new \Magento\Framework\DataObject();
                            $data['form_id']  = $form->getId();
                            $data['title']    = $form->getData('title');
                            $data[$field]    = trim($data[$field]);
                            $postObject->setData($data);
                            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                            $transport = $this->transportBuilder
                                ->setTemplateIdentifier($form->getData('thankyou_email_template'))
                                ->setTemplateOptions(
                                    [
                                        'area'  => 'frontend',
                                        'store' => $store->getId()
                                    ])
                                ->setTemplateVars(['data' => $postObject])
                                ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                                ->addTo($data[$field])
                                ->getTransport();
                            try  {
                                $transport->sendMessage();
                                $this->inlineTranslation->resume();
                            } catch (\Exception $e) {
                                $error = true;
                                $this->helper->writeLogData($e);
                                if ($this->getRequest()->isAjax()) {
                                    $responseData = [];
                                    $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                    $responseData['status'] = false;
                                    $this->getResponse()->representJson(
                                        $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                    );
                                }
                                $this->messageManager->addError(
                                    __('An error when send a thanks you email. We can\'t process your request right now. Sorry, that\'s all we know.')
                                );
                                $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                            }
                        } catch (\Exception $e) {
                            $this->helper->writeLogData($e);
                            $this->inlineTranslation->resume();
                            if ($this->getRequest()->isAjax()) {
                                $responseData = [];
                                $responseData['message'] =__('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.');
                                $responseData['status'] = false;
                                $this->getResponse()->representJson(
                                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                                );
                            }
                            $this->messageManager->addError(
                                __('An error when send a thanks you emails. We can\'t process your request right now. Sorry, that\'s all we know.')
                            );
                            $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
                            return;
                        }
                    }
                }
                $status = false;
                $responseData = [];
                if (!$error) {
                    $status = true;
                    $success_message = $form->getData('success_message');
                    if ($success_message) {
                        $successMessage1 = $this->helper->filter($success_message);
                        $this->messageManager->addSuccess($successMessage1);
                        $responseData['message'] = $successMessage1;
                    }
                }
                $responseData['error'] = $error;
                $responseData['status'] = $status;
                $this->getDataPersistor()->clear('formbuilder');
                $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                );
                return;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->getDataPersistor()->set('formbuilder', $this->getRequest()->getParams());
        }
    }

    public function getMessage($message){
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($message)
        );
    }

    /**
     * Set back redirect url to response
     *
     * @param null|string $backUrl
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($backUrl) {
            $resultRedirect->setUrl($backUrl);
        }
        return $resultRedirect;
    }
}