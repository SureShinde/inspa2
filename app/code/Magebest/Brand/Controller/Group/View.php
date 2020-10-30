<?php
/**
 * Magebest
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magebest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magebest.com/LICENSE.txt
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Magebest
 * @package    Magebest_Brand
 * @copyright  Copyright (c) 2014 Magebest (https://www.magebest.com/)
 * @license    https://www.magebest.com/LICENSE.txt
 */
namespace Magebest\Brand\Controller\Group;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magebest\Brand\Model\Brand
     */
    protected $_groupModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magebest\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @param Context                                             $context              
     * @param \Magento\Store\Model\StoreManager                   $storeManager         
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    
     * @param \Magebest\Brand\Model\Group                              $groupModel           
     * @param \Magento\Framework\Registry                         $coreRegistry         
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory 
     * @param \Magebest\Brand\Helper\Data                              $brandHelper          
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magebest\Brand\Model\Group $groupModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magebest\Brand\Helper\Data $brandHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_groupModel = $groupModel;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_brandHelper = $brandHelper;
    }

    public function _initGroup()
    {
        $groupId = (int)$this->getRequest()->getParam('group_id', false);
        if (!$groupId) {
            return false;
        }
        try{
            $group = $this->_groupModel->load($groupId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_group_brand', $group);
        return $group;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_brandHelper->getConfig('general_settings/enable')){
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $group = $this->_initGroup();
        if ($group) {
            $page = $this->resultPageFactory->create();
            $page->getConfig()->addBodyClass('group-' . $group->getUrlKey());
            return $page;
        }elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}