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

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\PushNotification\Api\Data\TemplateInterface;
use Mirasvit\PushNotification\Controller\Adminhtml\Template;

class Save extends Template
{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(TemplateInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->filterPostData($this->getRequest()->getParams());

        if ($data) {
            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->addData($data);

            try {
                $this->templateRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the template.'));

                if ($this->getRequest()->isAjax()) {
                    return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                        'success'  => true,
                        'template' => $model->getData(),
                    ]);
                } else {
                    if ($this->getRequest()->getParam('back') == 'edit') {
                        return $resultRedirect->setPath('*/*/edit', [TemplateInterface::ID => $model->getId()]);
                    }

                    return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [TemplateInterface::ID => $this->getRequest()->getParam(TemplateInterface::ID)]
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
        $data = $rawData;
        foreach ([TemplateInterface::IMAGE, TemplateInterface::ICON] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                if (!empty($data[$key]['delete'])) {
                    $data[$key] = null;
                } else {
                    if (isset($data[$key][0]['name']) && isset($data[$key][0]['tmp_name'])) {
                        $data[$key] = $data[$key][0]['name'];
                    } else {
                        unset($data[$key]);
                    }
                }
            }
        }
        return $data;
    }
}
