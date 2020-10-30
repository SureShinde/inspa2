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



namespace Mirasvit\PushNotification\Ui\Template\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\PushNotification\Model\Config;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        TemplateRepositoryInterface $indexRepository,
        Config $config,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->templateRepository = $indexRepository;
        $this->config = $config;
        $this->collection = $this->templateRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->templateRepository->getCollection() as $template) {
            $templateData = $template->getData();

            foreach ([TemplateInterface::ICON, TemplateInterface::IMAGE] as $key) {
                if (isset($templateData[$key])) {
                    unset($templateData[$key]);
                    $templateData[$key][0]['name'] = $template->getData($key);
                    $templateData[$key][0]['url'] = $this->config->getMediaUrl($template->getData($key));
                    $templateData[$key][0]['size'] = filesize($this->config->getMediaPath($template->getData($key)));
                }
            }

            $result[$template->getId()] = $templateData;
        }

        return $result;
    }
}
