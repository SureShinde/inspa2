<?php
/**
 * @Author      Magebest Developers
 * @package     Magebest_Brand
 * @copyright   Copyright (c) 2018 MAGEBEST (https://www.magebest.com)
 * @terms       https://www.magebest.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Magebest\Brand\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Backend system config datetime field renderer
 */
class Info extends \Magento\Config\Block\System\Config\Form\Field
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div style="padding: 10px;border-radius: 5px;text-align: center">
                    <h1>
						<a target="_blank" href="https://www.magebest.com/magento-extensions.html" style="color: #F78F1D">Magebest - Marketplace Extensions</a>
					</h1>
                </div>';
        
        return $html;
    }
}