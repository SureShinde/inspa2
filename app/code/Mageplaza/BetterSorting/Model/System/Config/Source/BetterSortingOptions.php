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

/**
 * Class BetterSortingOptions
 * Return all available sort options of Better Sorting Module
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Source
 */
class BetterSortingOptions
{
    const DISCOUNT       = 'discount';
    const BESTSELLER     = 'bestseller';
    const MOST_VIEWED    = 'most_viewed';
    const TOP_RATED      = 'top_rated';
    const REVIEWS_COUNT  = 'reviews_count';
    const NEW_ARRIVALS   = 'new_arrivals';
    const STOCK_QUANTITY = 'stock_quantity';
    const WISH_LIST      = 'wish_list';
    const PRODUCT_NAME   = 'name';
    const PRICE          = 'price';
    const POSITION       = 'position';
    const RELEVANCE      = 'relevance';

    /**
     * Return available sorting options for reference
     *
     * @return array
     */
    public function getBetterSortingOptions()
    {
        return [
            self::DISCOUNT,
            self::BESTSELLER,
            self::MOST_VIEWED,
            self::TOP_RATED,
            self::REVIEWS_COUNT,
            self::NEW_ARRIVALS,
            self::STOCK_QUANTITY,
            self::WISH_LIST,
            self::PRODUCT_NAME,
            self::PRICE,
            self::POSITION,
            self::RELEVANCE
        ];
    }

    /**
     * Return default sorting options for reference
     *
     * @return array
     */
    public function getDefaultSortingOptions()
    {
        return [
            self::PRODUCT_NAME,
            self::PRICE,
            self::POSITION,
            self::RELEVANCE
        ];
    }

    /**
     * Return sorting options which only available in one page
     *
     * @return array
     */
    public function getNoApplyPageSortingOptions()
    {
        return [
            self::POSITION,
            self::RELEVANCE
        ];
    }
}
