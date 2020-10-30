<?php

namespace Devbatch\Zenoti\Observer;

use Magento\Framework\Event\ObserverInterface;


class CheckCheckoutLoginPersistentObserver implements ObserverInterface
{

    public function __construct(

        \Magento\Framework\App\Http\Context $context

    )
    {


        $this->_context = $context;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        var_dump($observer);
        die();
        $actionName = $observer->getEvent()->getRequest()->getFullActionName();
        var_dump($actionName);
        die();
        $openActions = array(
            'cms_index_index',
            'onestepcheckout_index_index'
        );
        if (in_array($actionName,$openActions)) {

        }

    }
}