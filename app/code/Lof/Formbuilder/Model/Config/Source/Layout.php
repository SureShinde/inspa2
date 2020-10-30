<?php /**
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

namespace Lof\Formbuilder\Model\Config\Source;
 
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
        ['value' => 'list', 'label' => __('List')],
        ['value' => 'grid', 'label' => __('Grid')],
        ['value' => 'slide1', 'label' => __('Slide 1')],
        ];
    }
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
        'list'        => __('List'),
        'grid'         => __('Grid'),
        'slide1'       => __('Slide 1'),
        ];
    }
}
