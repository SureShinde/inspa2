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

/**
 *
 *
 * @noinspection PhpUnusedParameterInspection
 */

namespace Mageplaza\BetterSorting\Plugin;

use Magento\Catalog\Model\Config\Source\ListSort;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterSorting\Helper\Data as HelperData;
use Mageplaza\BetterSorting\Model\System\Config\Source\ApplyPages;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;

/**
 * Class ListSortConfig
 *
 * @package Mageplaza\BetterSorting\Plugin
 */
class ListSortConfig
{
    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var SortingOptions
     */
    protected $sortingOptions;

    /**
     * @var Request
     */
    protected $request;

    /**
     * ListSort constructor.
     *
     * @param HelperData $helperData
     * @param SortingOptions $sortingOptions
     * @param Request $request
     */
    public function __construct(
        HelperData $helperData,
        SortingOptions $sortingOptions,
        Request $request
    ) {
        $this->helperData = $helperData;
        $this->sortingOptions = $sortingOptions;
        $this->request = $request;
    }

    /**
     * @param ListSort $subject
     * @param $result
     *
     * @return array
     * @SuppressWarnings("Unused")
     */
    public function afterToOptionArray(ListSort $subject, $result)
    {
        $storeId = $this->request->getParam('store', 1);
        if (!$this->helperData->isEnabled($storeId)) {
            return $result;
        }
        $newListSort = [];
        $enableSortOptions = $this->helperData->getEnabledSortOptions($storeId);
        $singlePageOption = $this->sortingOptions->getNoApplyPageSortingOptions();
        foreach ($enableSortOptions as $option) {
            if (!in_array($option, $singlePageOption, true)) {
                $applyPages = $this->helperData->getSortOptionApply($option, $storeId);
                if (in_array(ApplyPages::CATEGORY_PAGE, $applyPages, true)) {
                    $newListSort[$option] = $this->helperData->getSortOptionLabel($option, $storeId);
                }
            }
        }
        $listSort = $this->helperData->addSinglePageSortOption(
            SortingOptions::POSITION,
            $enableSortOptions,
            $newListSort,
            $storeId
        );

        return $listSort;
    }
}
