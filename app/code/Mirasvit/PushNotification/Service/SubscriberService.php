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

//use Detection\MobileDetect;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mirasvit\PushNotification\Api\Repository\SubscriberRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\SubscriberServiceInterface;
use Magento\Customer\Model\Session;

class SubscriberService implements SubscriberServiceInterface
{
    /**
     * @var SubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var FingerprintService
     */
    private $fingerprintService;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        SubscriberRepositoryInterface $subscriberRepository,
        Session $session,
        FingerprintService $fingerprintService,
        StoreManagerInterface $storeManager
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->session = $session;
        $this->fingerprintService = $fingerprintService;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function saveSubscriber($endpoint)
    {
        $subscriber = $this->subscriberRepository->getByEndpoint($endpoint);

        if (!$subscriber) {
            $subscriber = $this->subscriberRepository->create();
        }

        if ($subscriber->getCustomerId()) {
            return;
        }

        $subscriber
            ->setEndpoint($endpoint)
            ->setCustomerId($this->session->getCustomerId())
            ->setBrowserName($this->fingerprintService->getBrowserName())
            ->setDeviceType($this->fingerprintService->getDeviceType())
            ->setOperationSystem($this->fingerprintService->getOS())
            ->setCountry($this->fingerprintService->getIpCountry())
            ->setIp($this->fingerprintService->getIp())
            ->setStatus(SubscriberInterface::STATUS_SUBSCRIBED)
            ->setStoreId($this->storeManager->getStore()->getId());

        $this->subscriberRepository->save($subscriber);
    }
}
