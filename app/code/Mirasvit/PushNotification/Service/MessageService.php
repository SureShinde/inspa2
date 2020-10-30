<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-push-notification
 * @version   1.1.18
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\PushNotification\Service;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Service\MessageServiceInterface;

class MessageService implements MessageServiceInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareMessage(MessageInterface $message, $params)
    {
        $data = [];

        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');

        $data['base_url'] = $storeManager->getStore()->getBaseUrl();

        if (isset($params['order_id']) && $params['order_id']) {
            /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
            $orderRepository = $this->objectManager->get('Magento\Sales\Api\OrderRepositoryInterface');
            $order = $orderRepository->get($params['order_id']);

            $data['order_number'] = (string)$order->getIncrementId();
            $data['order_status'] = (string)$order->getStatusLabel();
            $data['order_id'] = (string)$order->getEntityId();
        }

        if (isset($params['quote_id']) && $params['quote_id']) {
            /** @var \Magento\Quote\Api\CartRepositoryInterface $cartRepository */
            $cartRepository = $this->objectManager->get('Magento\Quote\Api\CartRepositoryInterface');
            $cart = $cartRepository->get($params['quote_id']);

            $data['cart_qty'] = (string)intval($cart->getItemsQty());
        }

        if (isset($params['customer_id']) && $params['customer_id']) {
            /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
            $customerRepository = $this->objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
            $customer = $customerRepository->getById($params['customer_id']);

            $data['customer_email'] = $customer->getEmail();
            $data['customer_firstname'] = $customer->getFirstname();
            $data['customer_lastname'] = $customer->getLastname();
            $data['customer_name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        }

        foreach ($data as $name => $value) {
            $message->setSubject(str_replace("[$name]", $value, $message->getSubject()));
            $message->setBody(str_replace("[$name]", $value, $message->getBody()));
            $message->setUrl(str_replace("[$name]", $value, $message->getUrl()));
        }

        return $message;
    }
}