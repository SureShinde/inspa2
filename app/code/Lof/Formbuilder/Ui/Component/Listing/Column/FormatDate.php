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

namespace Lof\Formbuilder\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Lof\Formbuilder\Block\Adminhtml\Form\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class FormatDate extends Column
{

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var formatDate */
    protected $_formatDate;

    /**
     * @var string
     */
    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder         $actionUrlBuilder
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        \Lof\Formbuilder\Helper\Data $formatDate,
        array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->_formatDate = $formatDate;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if ($name=='creation_time'){
                    $getTime = $item[$name];
                    if (isset($item['creation_time'])) {
                        $item['creation_time'] = $this->_formatDate->FormatDateFormBuilder($getTime);
                    }
                }
                if ($name=='update_time'){
                    $getTime = $item[$name];
                    if (isset($item['update_time'])) {
                        $item['update_time'] = $this->_formatDate->FormatDateFormBuilder($getTime);
                    }
                }
                if ($name=='created_time'){
                    $getTime = $item[$name];
                    if (isset($item['created_time'])) {
                        $item['created_time'] = $this->_formatDate->FormatDateFormBuilder($getTime);
                    }
                }
                if ($name=='updated_time'){
                    $getTime = $item[$name];
                    if (isset($item['updated_time'])) {
                        $item['updated_time'] = $this->_formatDate->FormatDateFormBuilder($getTime);
                    }
                }
            }
        }
        return $dataSource;
    }
}
