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



namespace Mirasvit\PushNotification\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\PushNotification\Api\Data\PromptInterface;
use Mirasvit\PushNotification\Api\Service\PromptServiceInterface;
use Mirasvit\PushNotification\Model\Config;

class Js extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PromptServiceInterface
     */
    private $promptService;

    public function __construct(
        Config $config,
        PromptServiceInterface $promptService,
        Template\Context $context
    ) {
        $this->config = $config;
        $this->storeManager = $context->getStoreManager();
        $this->promptService = $promptService;

        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getManifestUrl()
    {
        return $this->getDefaultStore()->getBaseUrl() . 'manifest.json';
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $config = [];

        $prompt = $this->promptService->getPrompt();
        if ($prompt) {
            $config['Magento_Ui/js/core/app']['components']['pushNotificationPrompt'] = [
                'component' => 'Mirasvit_PushNotification/js/prompt',
                'config'    => [
                    'prompt' => [
                        PromptInterface::HEADLINE    => $prompt->getHeadline(),
                        PromptInterface::BODY        => $prompt->getBody(),
                        PromptInterface::ACCEPT_TEXT => $prompt->getAcceptText(),
                        PromptInterface::REJECT_TEXT => $prompt->getRejectText(),
                        PromptInterface::DELAY       => $prompt->getDelay(),
                    ],
                ],
            ];
        }

        return ['*' => $config];
    }

    public function isDebugEnabled()
    {
        return (int)$this->config->isDebugEnabled();
    }

    public function getServiceWorkerUrl()
    {
        $host = '//' . parse_url($this->getDefaultStore()->getBaseUrl(), PHP_URL_HOST);

        return $host . '/service-worker'
            . '?' . http_build_query([
                'debug' => $this->config->isDebugEnabled(),
                'url'   => $this->getDefaultStore()->getBaseUrl()
                    . 'push_notification/action/serviceWorker',
            ]);
    }

    public function getSubscribeUrl()
    {
        return $this->getDefaultStore()->getBaseUrl()
            . 'push_notification/action/subscribe';
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    private function getDefaultStore()
    {
        return $this->storeManager->getStore();
    }
}
