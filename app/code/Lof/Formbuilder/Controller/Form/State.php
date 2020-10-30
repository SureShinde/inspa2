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

class State extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Lof\Formbuilder\Model\Model
     */
    protected $_model;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
     /**
     * @var \Magento\Directory\Helper\Data 
     */
    protected $_helper;


    /**
     * @param Context                                    $context           
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory 
     * @param \Magento\Framework\Registry                $registry          
     * @param \Lof\Formbuilder\Model\Model               $model             
     * @param \Magento\Framework\Escaper                 $escaper           
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\Formbuilder\Model\Model $model,
        \Magento\Framework\Escaper $escaper,
        \Magento\Directory\Helper\Data $helper)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $registry;
        $this->_model            = $model;
        $this->_escaper          = $escaper;
        $this->_helper           = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        header('Content-Type: text/javascript');
        $post = $this->getRequest()->getPost();
        $field_name = $post['field_name'];
        $scopeHelper = $this->_helper;
        $_regionsData = $scopeHelper->getRegionData();
        $countries = $scopeHelper->getCountryCollection()->toOptionArray(false);
        foreach ($countries as $key => $country) {
            if ($country['label'] == $post['country_id']) {
                $code = $country['value'];
            }
        }
        $output = [];
        //$output[$code]['name'] = $post['country_name'];
        $data_return ='';
        if (isset($code)) {
            if (array_key_exists($code, $_regionsData) && isset($code)) {
                foreach ($_regionsData[$code] as $key => $region) {
                    $output[$code]['regions'][$key]['code'] = $region['code'];
                    $output[$code]['regions'][$key]['name'] = $region['name'];
                }
                if ($output) {
                    $data_return .='<select id="'.$field_name.'"class="required-entry" name="'.$field_name.'">';
                    $data_return .='<option value="">-- '.__("Please Select").' --</option>';
                    foreach ($output[$code]['regions'] as $key => $_output) {
                        $data_return .='<option value="'.$_output['name'].'">'.$_output['name'].'</option>';
                    }
                    $data_return .='<select>';
                    $data_return .='<label for="'.$field_name.'">'.__("State / Province / Region").'</label>';
                }
            } else {
                $data_return .='<input class="input-text validate-state" type="text" id="'.$field_name.'"class="required-entry" name="'.$field_name.'" />';
                $data_return .='<label for="'.$field_name.'">'.__("State / Province / Region").'</label>';
            }
        } else {
             $data_return .='<input class="input-text validate-state" type="text" id="'.$field_name.'"class="required-entry" name="'.$field_name.'" />';
                $data_return .='<label for="'.$field_name.'">'.__("State / Province / Region").'</label>';
        }
        echo  json_encode($data_return);

        exit;
    }
}
