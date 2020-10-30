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



namespace Mirasvit\PushNotification\Controller\Adminhtml\Notification;

use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Controller\Adminhtml\Notification;

class Save extends Notification
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(NotificationInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->filterPostData($this->getRequest()->getParams());

        if ($data) {
            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This notification no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (!isset($data['rule'])) {
                $data['rule'] = [];
            }

            $model->addData($data);

            $model->setStoreIds($data[NotificationInterface::STORE_IDS])
                ->setRule($data['rule']);

            try {
                $this->notificationRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the notification.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [NotificationInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [NotificationInterface::ID => $this->getRequest()->getParam(NotificationInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array $rawData
     * @return array
     */
    private function filterPostData(array $rawData)
    {
        if ($rawData[NotificationInterface::SCHEDULE_DATE]) {
            $rawData[NotificationInterface::SCHEDULE_DATE] = date('Y-m-d H:i:s',
                strtotime($rawData[NotificationInterface::SCHEDULE_DATE]));
        }

        return $rawData;
    }
}
