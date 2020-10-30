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
 * Class StockEnd
 * Source model applied for "Show Out of Stock at the End" field
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Source
 */
class StockEnd extends OptionArray
{
    const QTY_BASE  = 'qty_base';
    const NO        = 'no';
    const QTY_LABEL = 'qty_label';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::QTY_BASE  => __('Base on qty (<1)'),
            self::NO        => __('No'),
            self::QTY_LABEL => __('Base on stock label'),
        ];
    }
}
