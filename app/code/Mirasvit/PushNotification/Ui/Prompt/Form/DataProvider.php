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



namespace Mirasvit\PushNotification\Ui\Prompt\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Api\Repository\NotificationRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\PromptRepositoryInterface;
use Mirasvit\PushNotification\Model\Config;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $promptRepository;

    public function __construct(
        PromptRepositoryInterface $indexRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->promptRepository = $indexRepository;
        $this->collection = $this->promptRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->promptRepository->getCollection() as $prompt) {
            $promptData = $prompt->getData();

            $result[$prompt->getId()] = $promptData;
        }

        return $result;
    }
}
