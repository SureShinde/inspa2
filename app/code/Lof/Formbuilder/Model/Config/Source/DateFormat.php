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
 * @package    Lof_Formbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Formbuilder\Model\Config\Source;

class DateFormat
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'd/m/Y',
                'label' => __('dd/mm/yyyy'),
                ),
            array(
                'value' => 'm/d/Y',
                'label' => __('mm/dd/yyyy'),
                ),
            array(
                'value' => 'm-d-y',
                'label' => __('mm-dd-yy'),
                )
            );
    }
}
