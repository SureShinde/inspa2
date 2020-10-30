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

use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Controller\Adminhtml\Template;

class Delete extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->templateRepository->delete($model);

                $this->messageManager->addSuccessMessage(__('The template has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [TemplateInterface::ID => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('This template no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
