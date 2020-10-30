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

namespace Mageplaza\BetterSorting\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 *
 * @package Mageplaza\BetterSorting\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpbettersorting';

    const BESTSELLER_CONFIG_VIEW   = 'mp_bettersort_bestseller_configurable';
    const BESTSELLER_SIMPLE_VIEW   = 'mp_bettersort_bestseller_simple';
    const STOCK_CONFIG_VIEW        = 'mp_bettersort_stock_configurable';
    const STOCK_SIMPLE_VIEW        = 'mp_bettersort_stock_simple';
    const MOST_VIEWED_VIEW         = 'mp_bettersort_most_viewed';
    const NEW_ARRIVAL_DEFAULT_VIEW = 'mp_bettersort_new_default';
    const NEW_ARRIVAL_STORE_VIEW   = 'mp_bettersort_new_store';
    const DISCOUNT_CONFIG_VIEW     = 'mp_bettersort_discount_configurable';
    const DISCOUNT_SIMPLE_VIEW     = 'mp_bettersort_discount_simple';
    const WISH_LIST_VIEW           = 'mp_bettersort_wishlist';

    /**
     * @var SortingOptions
     */
    protected $sortingOptions;

    /**
     * @var ProductMetadataInterface
     */
    protected $metaData;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param SortingOptions $sortingOptions
     * @param ProductMetadataInterface $metadata
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        SortingOptions $sortingOptions,
        ProductMetadataInterface $metadata
    ) {
        $this->sortingOptions = $sortingOptions;
        $this->metaData = $metadata;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return string
     */
    public function getMagentoEdition()
    {
        return $this->metaData->getEdition();
    }

    /**
     * Return views array
     * @return array
     */
    public function getViewsArray()
    {
        return [
            self::BESTSELLER_CONFIG_VIEW,
            self::BESTSELLER_SIMPLE_VIEW,
            self::STOCK_CONFIG_VIEW,
            self::STOCK_SIMPLE_VIEW,
            self::MOST_VIEWED_VIEW,
            self::NEW_ARRIVAL_DEFAULT_VIEW,
            self::NEW_ARRIVAL_STORE_VIEW,
            self::DISCOUNT_CONFIG_VIEW,
            self::DISCOUNT_SIMPLE_VIEW,
            self::WISH_LIST_VIEW,
        ];
    }

    /**
     * //////////////////////////////////////////////////////
     * // General Configuration
     * /////////////////////////////////////////////////////
     */
    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getShowOutOfStockOption($storeId = null)
    {
        return $this->getConfigGeneral('out_of_stock_end', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSearchDefaultSort($storeId = null)
    {
        return $this->getConfigGeneral('sort_search_page', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getEnabledSortOptions($storeId = null)
    {
        $sortOrders = [];
        $sortOptions = [];
        $betterSortingOptions = $this->sortingOptions->getBetterSortingOptions();
        foreach ($betterSortingOptions as $betterSortingOption) {
            if ($this->isSortOptionEnabled($betterSortingOption, $storeId)) {
                $thisOptionSortOrder = $this->getSortOptionSortOrder($betterSortingOption, $storeId);
                $sortOrders[$betterSortingOption] = (int) $thisOptionSortOrder;
            }
        }
        // re-arrange sort options by sort order from config
        asort($sortOrders, 1);
        foreach ($sortOrders as $key => $sortOrder) {
            //array_push($sortOptions, $key);
            $sortOptions[] = $key;
        }

        return $sortOptions;
    }

    /**
     * @param  $option
     * @param array $enableSortOptions
     * @param array $newSortOptions
     * @param  $storeId
     *
     * @return array
     */
    public function addSinglePageSortOption($option, array $enableSortOptions, array $newSortOptions, $storeId = null)
    {
        if (in_array($option, $enableSortOptions, true)) {
            $optionLabel = $this->getSortOptionLabel($option, $storeId);
            $newSortOptions[$option] = $optionLabel;
        }

        return $newSortOptions;
    }

    /**
     * //////////////////////////////////////////////////////
     * // Better Sorting Options Configuration
     * /////////////////////////////////////////////////////
     */
    /**
     * @param  $sortOption
     * @param null $storeId
     *
     * @return mixed
     */
    public function isSortOptionEnabled($sortOption, $storeId = null)
    {
        return $this->getConfigGeneral($sortOption . '/' . 'enabled', $storeId);
    }

    /**
     * @param  $sortOption
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSortOptionLabel($sortOption, $storeId = null)
    {
        return $this->getConfigGeneral($sortOption . '/' . 'label', $storeId);
    }

    /**
     * @param  $sortOption
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSortOptionApply($sortOption, $storeId = null)
    {
        $applyPages = explode(',', $this->getConfigGeneral($sortOption . '/' . 'apply', $storeId));

        return $applyPages;
    }

    /**
     * @param  $sortOption
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSortOptionDirection($sortOption, $storeId = null)
    {
        return $this->getConfigGeneral($sortOption . '/' . 'direction', $storeId);
    }

    /**
     * @param  $sortOption
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSortOptionSortOrder($sortOption, $storeId = null)
    {
        return $this->getConfigGeneral($sortOption . '/' . 'sort_order', $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return int|null
     */
    public function getTimeBase($code, $storeId = null)
    {
        $timeBase = (int) $this->getConfigGeneral($code, $storeId);

        return ($timeBase > 0) ? $timeBase : null;
    }

    /**
     * //////////////////////////////////////////////////////
     * // Discount Configuration
     * /////////////////////////////////////////////////////
     */

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDiscountBase($storeId = null)
    {
        return $this->getConfigGeneral('discount/base', $storeId);
    }

    /**
     * //////////////////////////////////////////////////////
     * // Best Seller Configuration
     * /////////////////////////////////////////////////////
     */

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBestSellerBase($storeId = null)
    {
        return $this->getTimeBase('bestseller/time_base', $storeId);
    }

    /**
     * //////////////////////////////////////////////////////
     * // Most View Configuration
     * /////////////////////////////////////////////////////
     */

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getMostViewedBase($storeId = null)
    {
        return $this->getTimeBase('most_viewed/time_base', $storeId);
    }
}
