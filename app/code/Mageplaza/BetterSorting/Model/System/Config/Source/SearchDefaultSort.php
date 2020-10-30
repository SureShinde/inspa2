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
 * @category  Mageplaza
 * @package   Mageplaza_BetterSorting
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterSorting\Model\System\Config\Source;

use Magento\Framework\App\RequestInterface;
use Mageplaza\BetterSorting\Helper\Data as HelperData;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;

/**
 * Class SearchDefaultSort
 * Source model applied for "Default Sort by on Search Page" field
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Source
 */
class SearchDefaultSort extends OptionArray
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SortingOptions
     */
    protected $sortingOptions;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * SearchDefaultSort constructor.
     *
     * @param RequestInterface $request
     * @param BetterSortingOptions $sortingOptions
     * @param HelperData $helperData
     */
    public function __construct(
        RequestInterface $request,
        SortingOptions $sortingOptions,
        HelperData $helperData
    ) {
        $this->request = $request;
        $this->sortingOptions = $sortingOptions;
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        $storeId = $this->request->getParam('store', 1);
        $enabledSortOptions = $this->helperData->getEnabledSortOptions($storeId);
        $optionsArray = [];
        foreach ($enabledSortOptions as $sortOption) {
            $value = ucfirst(str_replace('_', ' ', $sortOption));
            $optionsArray[$sortOption] = $value;
        }
        if (isset($optionsArray[SortingOptions::POSITION])) {
            unset($optionsArray[SortingOptions::POSITION]);
        }
        if (isset($optionsArray[SortingOptions::PRODUCT_NAME])) {
            $optionsArray[SortingOptions::PRODUCT_NAME] = __('Product Name');
        }

        return $optionsArray;
    }
}
