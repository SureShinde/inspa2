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

class Uploader extends \Magento\Framework\App\Action\Action
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
            $data = $this->getRequest()->getParams();
            $form_id = $this->getRequest()->getParam("form_id");
            $field_id = $this->getRequest()->getParam("cid");
            $responseData = [];
            if ($form_id && $_FILES && $field_id) {
                $form = $this->form->load($form_id);
                if($form->getId()){
                    
                    if (
                        !isset($_FILES['file']['error']) ||
                        is_array($_FILES['file']['error'])
                    ) {
                        throw new RuntimeException(__('Invalid parameters.'));
                    }
                    switch ($_FILES['file']['error']) {
                        case UPLOAD_ERR_OK:
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            throw new RuntimeException('No file sent.');
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            throw new RuntimeException('Exceeded filesize limit.');
                        default:
                            throw new RuntimeException('Unknown errors.');
                    }
                    if($fields = $form->getFields()) {
                        $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                            ->getDirectoryRead(DirectoryList::MEDIA);
                        $mediaFolder = 'lof/formbuilder/files';
                        $savePath = $mediaDirectory->getAbsolutePath($mediaFolder);
                        if($store->getId()){
                            $saveStorePath = $savePath.DIRECTORY_SEPARATOR.$store->getId();
                            if(!is_dir($saveStorePath)) {
                                $saveStorePath = $this->_mkdir($savePath, $store->getId());
                            }
                            if($saveStorePath) {
                                $savePath = $saveStorePath;
                                $mediaFolder .= "/".$store->getId(); 
                            }
                        }

                        foreach ($fields as $key => $field) {
                            $cid        = $this->helper->getFieldId($field);
                            if($field && $field_id == $cid) {

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
                                $uploader = $this->_objectManager->create(
                                    'Magento\Framework\File\Uploader',
                                    array('fileId' => 'file')
                                    );
                                $uploader->setAllowedExtensions($fieldTypes);
                                $uploader->setAllowRenameFiles(true);
                                $uploader->setFilesDispersion(false);
                                $file = $uploader->save($savePath);

                                if ($file && !empty($file)) {
                                    $image_maximum_size = $this->parse_size(@ini_get('upload_max_filesize'));
                                    if ($image_maximum_size <= 0) {
                                        $image_maximum_size = 2;
                                    }
                                    if ($field && isset($field['image_maximum_size']) && $field['image_maximum_size']) {
                                        $image_maximum_size = $field['image_maximum_size'];
                                    }

                                    if (isset($field['image_maximum_size']) && ($image_maximum_size * 1024 * 1024) < $file['size']) {

                                        $this->mediaDirectory->delete($mediaFolder.'/' . $file['file']);

                                        throw new RuntimeException("The file is too big.");
                                    } else {
                                        $imgExtens                       = array("gif", "jpeg", "jpg", "png");
                                        $temp                            = explode(".", $file['file']);
                                        $extension                       = end($temp);

                                        $responseData['status'] = 'ok';
                                        $responseData['path'] = $mediaFolder."/".$file['file'];
                                        $responseData['filename'] = $file['file'];
                                        $responseData['fileurl'] = $mediaUrl . $mediaFolder . '/' . $file['file'];
                                        $responseData['filesize'] = $file['size'];

                                        if (in_array($extension, $imgExtens)) {
                                            $responseData['isimage'] = true;
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                } else {
                    throw new RuntimeException(__('Invalid form profile.'));
                }

            } else {
                throw new RuntimeException(__('Invalid form Id or field id or empty FILES.'));
            }

            $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                    );

        } catch (\Exception $e) {
            @http_response_code(400);
            $responseData = ['status' => 'error', 'message' => $e->getMessage()];
            $this->messageManager->addError($e->getMessage());
            $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                    );
        }
        return;
    }
    protected function _mkdir($path, $name) {
        $path = $path.DIRECTORY_SEPARATOR.$name;

        if (@mkdir($path)) {
            @chmod($path, 0777);
            return $path;
        }

        return false;
    }

}
