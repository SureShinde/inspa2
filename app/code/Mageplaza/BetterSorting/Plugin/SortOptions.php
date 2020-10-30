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

use Magento\Catalog\Model\Config;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterSorting\Helper\Data as HelperData;
use Mageplaza\BetterSorting\Model\System\Config\Source\ApplyPages;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;

/**
 * Class SortOptions
 *
 * @package Mageplaza\BetterSorting\Plugin
 */
class SortOptions
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
     * SortOptions constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param SortingOptions $sortingOptions
     * @param Request $request
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        HelperData $helperData,
        SortingOptions $sortingOptions,
        Request $request
    ) {
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->sortingOptions = $sortingOptions;
        $this->request = $request;
    }

    /**
     * @param Config $subject
     * @param $result
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetAttributeUsedForSortByArray(Config $subject, $result)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if (!$this->helperData->isEnabled($storeId)) {
            return $result;
        }
        $newCatalogOptions = [];
        $catalogSearchOptions = [];
        $enableSortOptions = $this->helperData->getEnabledSortOptions($storeId);
        foreach ($enableSortOptions as $option) {
            if ($option !== SortingOptions::RELEVANCE || $option !== SortingOptions::POSITION) {
                $applyPages = $this->helperData->getSortOptionApply($option, $storeId);
                if (in_array(ApplyPages::CATEGORY_PAGE, $applyPages, true)) {
                    $newCatalogOptions[$option] = $this->helperData->getSortOptionLabel($option, $storeId);
                }
                if (in_array(ApplyPages::SEARCH_PAGE, $applyPages, true)) {
                    $catalogSearchOptions[$option] = $this->helperData->getSortOptionLabel($option, $storeId);
                }
            }
        }
        if ($this->request->getModuleName() === 'catalog') {
            $catalogOptions = $this->helperData->addSinglePageSortOption(
                SortingOptions::POSITION,
                $enableSortOptions,
                $newCatalogOptions,
                $storeId
            );
            if (isset($catalogOptions[SortingOptions::RELEVANCE])) {
                unset($catalogOptions[SortingOptions::RELEVANCE]);
            }

            return $catalogOptions;
        }

        return $catalogSearchOptions;
    }
}
