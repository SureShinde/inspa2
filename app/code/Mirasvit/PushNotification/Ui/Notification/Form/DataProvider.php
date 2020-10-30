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



namespace Mirasvit\PushNotification\Ui\Notification\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\PushNotification\Model\Config;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Modifier\Template
     */
    private $templateModifier;

    public function __construct(
        NotificationRepositoryInterface $indexRepository,
        Config $config,
        Registry $registry,
        RequestInterface $request,
        Modifier\Template $templateModifier,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->notificationRepository = $indexRepository;
        $this->config = $config;
        $this->registry = $registry;
        $this->request = $request;
        $this->collection = $this->notificationRepository->getCollection();
        $this->templateModifier = $templateModifier;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $notificationId = $this->request->getParam($this->getRequestFieldName());
        $notification = $this->notificationRepository->get($notificationId);

        $meta = parent::getMeta();
        $meta = $this->templateModifier->modifyMeta($meta);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->notificationRepository->getCollection() as $notification) {
            $notificationData = $notification->getData();

            $result[$notification->getId()] = $notificationData;
        }

        return $result;
    }
}
