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



namespace Mirasvit\PushNotification\Plugin\Framework\App\FrontController;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Mirasvit\PushNotification\Model\Config;

class ServiceWorkerResponsePlugin
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ResponseInterface $response,
        Config $config
    ) {
        $this->response = $response;
        $this->config = $config;
    }

    /**
     * @param FrontControllerInterface $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return $this
     */
    public function aroundDispatch(FrontControllerInterface $subject, \Closure $proceed, RequestInterface $request)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        if (strpos($request->getOriginalPathInfo(), 'service-worker') !== false) {
            $this->response->setHeader('Content-Type', 'application/javascript');
            $this->response->setBody(file_get_contents($this->config->getServiceWorkerPath()));
            $this->response->send();
            exit;
        }

        return $proceed($request);
    }
}
