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

namespace Lof\Formbuilder\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_formFactory;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\ActionFactory       $actionFactory   
     * @param \Magento\Framework\Event\ManagerInterface  $eventManager    
     * @param \Lof\Formbuilder\Model\FormFactory         $formFactory     
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    
     * @param \Lof\Formbuilder\Helper\Data               $data            
     * @param \Magento\Customer\Model\Session            $customerSession 
     * @param \Magento\Framework\Registry                $registry        
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Lof\Formbuilder\Model\FormFactory $formFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\Formbuilder\Helper\Data $data,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->actionFactory    = $actionFactory;
        $this->_eventManager    = $eventManager;
        $this->_formFactory     = $formFactory;
        $this->_storeManager    = $storeManager;
        $this->_helper          = $data;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry    = $registry;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->dispatched) {
            $identifier = trim($request->getPathInfo(), '/');
            $origUrlKey = $identifier;
            $enable = $this->_helper->getConfig('general_settings/enable');
            $route = $this->_helper->getConfig('general_settings/route');
            $route = $route?$route:'formbuilder';
            $submitRoute = !empty($route)?($route.'SubmitForm'):'';
            $identifiers = explode('/', $identifier);
            if(count($identifiers)==2){
                $identifier = $identifiers[0];
            }

            if($submitRoute !='' && $submitRoute == $identifiers[0]){
                $this->dispatched = true;
                $request->setDispatched(true);
                $request->setModuleName('formbuilder')
                ->setControllerName('form')
                ->setActionName('post');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            } else {
                if ($route!='' && $route != $identifier) {
                    return;
                }

                if (count($identifiers)==2) {
                    $identifier = $identifiers[1];
                }
                $form = $this->_formFactory->create();
                $formId = $form->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
                
                if ($formId) {
                    $this->dispatched = true;
                    $request->setDispatched(true);
                    $request->setModuleName('formbuilder')
                    ->setControllerName('form')
                    ->setActionName('view')
                    ->setParam('form_id', $formId);
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);

                } else {
                    return;
                }
            }
            $condition = new \Magento\Framework\DataObject(
                [
                    'identifier' => $identifier,
                    'request' => $request,
                    'continue' => true
                ]);

            $this->_eventManager->dispatch(
                'formbuilder_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );

            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $this->dispatched = true;
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }

            if (!$condition->getContinue()) {
                return null;
            }

            if (!$request->getModuleName()) {
                return null;
            }

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
                );
        }
    }
}
