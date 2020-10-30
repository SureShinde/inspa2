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

namespace Lof\Formbuilder\Helper;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class GenerateFile extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    protected $_logger = null;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $postData = null;



    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager   = $storeManager;
        $this->_objectManager  = $objectManager;
        $this->coreRegistry    = $coreRegistry;
        $this->filterManager   = $filterManager;
        $this->_file = $file;
        $this->_fileFactory = $fileFactory;
        $this->_fileSystem = $fileSystem;
    }

    public function filter($str) {
        $str  = $this->formatCustomVariables($str);
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null) {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofformbuilder/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function getRootDir(){
        if(!isset($this->_root_dir)) {
            $mediaDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::VAR_DIR);
            $this->_root_dir = $mediaDirectory->getAbsolutePath();
        }
        return $this->_root_dir;
    }
    public function getFilePathWithName($file_name, $file_type = "xml" ){
        $fileName = $file_name.".".$file_type;
        $filePath = $this->getRootDir() . $fileName;
        return $filePath;
    }
    public function generateFormFile($form_data, $params, $form){

        $generate_to_file = $form->getData("generate_to_file");
        if($generate_to_file){
            $file_name = $form->getData("identifier")."_".time();
            $file_type = "xml";
            $fileName = $file_name.".".$file_type;
            $file_meta_type = 'application/xml';
            if($file_type = "xml"){
                $file_meta_type = 'application/xml';
            }
            $filePath = $this->getFilePathWithName($file_name, $file_type);
            $file_content = $this->getFileContent($form_data, $params, $form);
            ob_start();
            $this->_fileFactory->create(
                                $fileName,
                                $file_content,
                                DirectoryList::VAR_DIR,
                                $file_meta_type
                            );
            $output = ob_get_contents();
            ob_end_clean();
            return [$fileName, $filePath, $file_content];
        }
        return [];
    }
    public function getFileContent($form_data, $params, $form){
        $file_content = $form->getData("generate_to_file");
        $form_id = $form->getId();
        $available_variables = [
                            "{{brower}}",
                            "{{ip_address}}",
                            "{{http_host}}",
                            "{{current_url}}",
                            "{{customer_id}}",
                            "{{loffield_%1%%2%}}"
                            ];

        foreach($available_variables as $variable) {
            if($variable == "{{loffield_%1%%2%}}" && $form_data) {
                $variable_key = str_replace("%2%", $form_id, $variable);
                foreach($form_data as $field) {

                    $variable_name = str_replace(array("{{","}}"),"", $variable);
                    $cid = isset($field['cid'])?$field['cid']:'';
                    $field_id    = isset($field['field_id'])?$field['field_id']:'';
                    $field_id    = trim($field_id);
                    $field_id    = str_replace(" ","-", $field_id);

                    if($field_id) {
                        $cid = $field_id;
                    }
                    $variable_key_tmp = str_replace("%1%", $cid, $variable_key);
                    $field_cid = isset($field['field_cid'])?$field['field_cid']:("loffield_".$cid.$form_id);
                    
                    $field_value = strip_tags($field['value']);
                    $file_content = str_replace($variable_key_tmp, $field_value, $file_content);
                }
            } else {
                $variable_name = str_replace(array("{{","}}"),"", $variable);
                if(isset($params[$variable_name])){
                    $file_content = str_replace($variable, $params[$variable_name], $file_content);
                }
                
            }
        }
        return $file_content;
    }
}