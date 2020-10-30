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



namespace Mirasvit\PushNotification\Model;

use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    /**
     * @var string
     */
    const FILE_DIR = 'push_notification';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param StoreInterface $store
     * @return string
     */
    public function getGoogleApiKey($store)
    {
        return $this->scopeConfig->getValue(
            'push_notification/authorization/google_api_key',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param StoreInterface $store
     * @return string
     */
    public function getGcmSenderId($store)
    {
        return $this->scopeConfig->getValue(
            'push_notification/authorization/gcm_sender_id',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isDebugEnabled()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getServiceWorkerPath()
    {
        return dirname(dirname(__FILE__)) . '/view/base/web/js/service-worker.js';
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . self::FILE_DIR . '/' . $file;
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        $path = $this->mediaDirectory->getAbsolutePath(Config::FILE_DIR);
        @mkdir($path, 0777, true);

        return $path;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . '/' . $file;
    }
}
