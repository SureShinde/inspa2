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
 * Class DiscountBase
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Source
 */
class DiscountBase extends OptionArray
{
    const AMOUNT  = 'amount';
    const PERCENT = 'percent';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::AMOUNT  => __('Amount'),
            self::PERCENT => __('Percent'),
        ];
    }
}
