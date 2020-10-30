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



namespace Mirasvit\PushNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\PushNotification\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\PushNotification\Controller\Adminhtml\Template;
use Mirasvit\PushNotification\Model\Config\FileProcessor;

class FileUpload extends Template
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FileProcessor $fileProcessor,
        TemplateRepositoryInterface $templateRepository,
        Context $context
    ) {
        $this->fileProcessor = $fileProcessor;

        parent::__construct($templateRepository, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->fileProcessor->save(key($_FILES));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
