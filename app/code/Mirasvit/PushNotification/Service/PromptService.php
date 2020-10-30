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

use Mirasvit\PushNotification\Api\Data\PromptInterface;
use Mirasvit\PushNotification\Api\Repository\PromptRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\PromptServiceInterface;

class PromptService implements PromptServiceInterface
{
    /**
     * @var PromptRepositoryInterface
     */
    private $promptRepository;

    public function __construct(
        PromptRepositoryInterface $promptRepository
    ) {
        $this->promptRepository = $promptRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrompt()
    {
        $collection = $this->promptRepository->getCollection();
        $collection->addFieldToFilter(PromptInterface::IS_ACTIVE, true);

        return $collection->getFirstItem();
    }
}