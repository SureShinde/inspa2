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
 * Class ApplyPages
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Source
 */
class ApplyPages extends OptionArray
{
    const CATEGORY_PAGE = 'category_page';
    const SEARCH_PAGE   = 'search_page';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::CATEGORY_PAGE => __('Category Page'),
            self::SEARCH_PAGE   => __('Search Page'),
        ];
    }
}
