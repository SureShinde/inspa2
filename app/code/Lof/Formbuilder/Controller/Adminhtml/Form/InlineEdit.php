<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Formbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Formbuilder\Controller\Adminhtml\Form;

use Magento\Backend\App\Action\Context;
use Magento\Cms\Api\BlockRepositoryInterface as BlockRepository;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var BlockRepository  */
    protected $blockRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param BlockRepository $blockRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        BlockRepository $blockRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->blockRepository = $blockRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $blockId) {
                    /** @var \Magento\Cms\Model\Block $block */
                    $block = $this->blockRepository->getById($blockId);
                    try {
                        $block->setData(array_merge($block->getData(), $postItems[$blockId]));
                        $this->blockRepository->save($block);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithBlockId(
                            $block,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
