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



namespace Mirasvit\PushNotification\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;

class Template implements OptionSourceInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    public function __construct(
        TemplateRepositoryInterface $templateRepository
    ) {
        $this->templateRepository = $templateRepository;
    }

    /**
     * {@inheritdoc}
     **/
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->templateRepository->getCollection() as $template) {
            $result[] = [
                'value' => $template->getId(),
                'label' => $template->getName(),
            ];
        }

        return $result;
    }
}
