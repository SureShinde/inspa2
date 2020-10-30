<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterSorting
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterSorting\Plugin;

use Magento\CatalogSearch\Block\Result;
use Mageplaza\BetterSorting\Helper\Data as HelperData;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;

/**
 * Class SearchResult
 * @package Mageplaza\BetterSorting\Plugin
 */
class SearchResult
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * SearchResult constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Result $subject
     * @param $result
     *
     * @return $this
     */
    public function afterSetListOrders(Result $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $availableOrders = $subject->getListBlock()->getToolbarBlock()->getAvailableOrders();
        $relevanceLabel = $this->helperData->getSortOptionLabel(SortingOptions::RELEVANCE);
        $availableOrders['relevance'] = $relevanceLabel;
        $defaultSortBy = $this->helperData->getSearchDefaultSort();
        $defaultDir = strtolower($this->helperData->getSortOptionDirection($defaultSortBy));
        $subject->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection($defaultDir)
            ->setDefaultSortBy($defaultSortBy);

        return $result;
    }
}
