<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Devbatch\Zenoti\Controller\Account;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;

/**
 * Login form page. Accepts POST for backward compatibility reasons.
 */
class Login extends AbstractAccount implements HttpGetActionInterface, HttpPostActionInterface
{
    private $urlInterface;
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlInterface = $urlInterface;
        parent::__construct($context);
    }

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        if ($this->session->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultRedirect = $this->resultRedirectFactory->create();
        $param = $this->getRequest()->getParam('page');

          if($param == 'checkout'){
            $base_url = $this->urlInterface->getUrl('onestepcheckout');
            $url = urlencode($base_url);
            $redirectUrl = 'https://inspa.zenoti.com/webstoreNew/user/signIn?sgn_type=sso&redirect_url=' . $url;
            $resultRedirect->setUrl($redirectUrl);
            return $resultRedirect;
        }
        $base_url = $this->urlInterface->getUrl();
        $url = urlencode($base_url);
        $redirectUrl = 'https://inspa.zenoti.com/webstoreNew/user/signIn?sgn_type=sso&redirect_url=' . $url;
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}