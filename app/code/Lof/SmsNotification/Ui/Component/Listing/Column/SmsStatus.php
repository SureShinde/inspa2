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
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmsNotification\Ui\Component\Listing\Column;

class SmsStatus extends \Magento\Ui\Component\Listing\Columns\Column
{


    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context             
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory  
     * @param \Lof\SmsNotification\Helper\Balance\Spend                       $rewardsBalanceSpend 
     * @param array                                                        $components          
     * @param array                                                        $data                
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $fieldName = $this->getData('name');
                if (isset($item['smslog_id'])) {
                    $status =  strtolower($item['status']);
                    $item[$fieldName . '_html'] = '<span class="fue-status fue-status-' . $status . '">' . ucfirst($item['status']) . '</span>';
                }

            }

        }
        return $dataSource;
    }
}
