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
 * @category    Mageplaza
 * @package     Mageplaza_BetterSorting
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterSorting\Plugin;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Wishlist\Model\Wishlist;
use Mageplaza\BetterSorting\Helper\Data as HelperData;
use Mageplaza\BetterSorting\Model\System\Config\Source\BetterSortingOptions as SortingOptions;
use Mageplaza\BetterSorting\Model\System\Config\Source\DiscountBase;
use Mageplaza\BetterSorting\Model\System\Config\Source\StockEnd;
use Zend_Db_Expr;

/**
 * Class ProductList
 * @package Mageplaza\BetterSorting\Plugin
 */
class ProductList
{
    /**
     * @var AbstractCollection
     */
    protected $_collection;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var SortingOptions
     */
    protected $sortingOptions;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * ProductList constructor.
     *
     * @param HelperData $helperData
     * @param SortingOptions $sortingOptions
     * @param DateTime $date
     * @param SessionFactory $sessionFactory
     * @param Wishlist $wishlist
     * @param StoreManager $storeManager
     */
    public function __construct(
        HelperData $helperData,
        SortingOptions $sortingOptions,
        DateTime $date,
        SessionFactory $sessionFactory,
        Wishlist $wishlist,
        StoreManager $storeManager
    ) {
        $this->helperData = $helperData;
        $this->sortingOptions = $sortingOptions;
        $this->date = $date;
        $this->sessionFactory = $sessionFactory;
        $this->wishlist = $wishlist;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Toolbar $subject
     * @param callable $proceed
     * @param Collection $collection
     *
     * @return mixed
     * @throws LocalizedException
     */

    public function aroundSetCollection(Toolbar $subject, callable $proceed, $collection)
    {
        if (!$this->helperData->isEnabled()) {
            return $proceed($collection);
        }

        $currentOrder = $subject->getCurrentOrder();
        $enableSortOptions = $this->helperData->getEnabledSortOptions();
        if (in_array($currentOrder, $enableSortOptions, true)) {
            $direction = $this->helperData->getSortOptionDirection($currentOrder);
            $subject->setDefaultDirection($direction);
        }

        $result = $proceed($collection);

        $defaultSortOptions = $this->sortingOptions->getDefaultSortingOptions();
        $stockEnd = $this->helperData->getShowOutOfStockOption();
        $this->_collection = $subject->getCollection();
        $selectCollection = $this->_collection->getSelect();
        $sortingData = $this->getSortingData($currentOrder);
        $dir = strtoupper($subject->getCurrentDirection());

        if (($stockEnd !== StockEnd::NO) && ($currentOrder !== SortingOptions::STOCK_QUANTITY)) {
            $this->showOutOfStockLast($stockEnd);
        }

        if (in_array($currentOrder, $defaultSortOptions, true)) {
            return $result;
        }

        switch ($currentOrder) {
            case SortingOptions::DISCOUNT:
            case SortingOptions::BESTSELLER:
            case SortingOptions::NEW_ARRIVALS:
            case SortingOptions::STOCK_QUANTITY:
                $this->betterSortConfig($selectCollection, $sortingData, $dir, $currentOrder);
                break;
            case SortingOptions::TOP_RATED:
            case SortingOptions::REVIEWS_COUNT:
                $this->betterSortSimple($selectCollection, $sortingData, $dir, null);
                break;
            case SortingOptions::MOST_VIEWED:
            case SortingOptions::WISH_LIST:
                $this->betterSortSimple($selectCollection, $sortingData, $dir, $currentOrder);
                break;
        }

        return $result;
    }

    /**
     * Re-order the collection so that out-of-stock items are at last
     *
     * @param $stockEnd
     *
     * @throws LocalizedException
     */
    public function showOutOfStockLast($stockEnd)
    {
        $selectCollection = $this->_collection->getSelect();
        if ($stockEnd === StockEnd::QTY_LABEL) {
            $stockItemTable = $this->getTable('cataloginventory_stock_item');
            $cond = $this->joinCondition($stockItemTable, 'product_id', null);
            $selectCollection->joinInner(
                [$stockItemTable],
                $cond,
                ['is_in_stock']
            );
            $selectCollection->order("{$stockItemTable}.is_in_stock DESC");
        }
        if ($stockEnd === StockEnd::QTY_BASE) {
            $configView = $this->getTable(HelperData::STOCK_CONFIG_VIEW);
            $simpleView = $this->getTable(HelperData::STOCK_SIMPLE_VIEW);
            $simpleCond = $this->joinCondition($simpleView, 'product_id', null);
            $configCond = $this->joinCondition($configView, 'parent_id', null);
            $selectCollection->joinLeft(
                [$simpleView],
                $simpleCond,
                []
            )->joinLeft(
                [$configView],
                $configCond,
                []
            );
            $stockStatus = "IF((IFNULL({$simpleView}.simple_qty, {$configView}.config_qty)) < 1, 0, 1)";
            $selectCollection->columns(['stock_status' => new Zend_Db_Expr($stockStatus)]);
            $selectCollection->order('stock_status DESC');
        }
    }

    /**
     * Get sorting data based on selected sorting option
     *
     * @param $currentOrder
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getSortingData($currentOrder)
    {
        $joinTable = 'mp';
        $joinColumn = 'mp';
        $defaultDir = 'ASC';
        $productIdCol = 'product_id';
        $storeId = $this->storeManager->getStore()->getId();
        $customerId = $this->getCustomerId();
        switch ($currentOrder) {
            case SortingOptions::DISCOUNT:
                $joinTable = HelperData::DISCOUNT_CONFIG_VIEW;
                $joinColumn = 'rule_price';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::BESTSELLER:
                $joinTable = HelperData::BESTSELLER_CONFIG_VIEW;
                $joinColumn = 'qty_ordered';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::MOST_VIEWED:
                $joinTable = HelperData::MOST_VIEWED_VIEW;
                $joinColumn = 'views_num';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::TOP_RATED:
                $joinTable = 'review_entity_summary';
                $joinColumn = 'rating_summary';
                $productIdCol = 'entity_pk_value';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::REVIEWS_COUNT:
                $joinTable = 'review_entity_summary';
                $joinColumn = 'reviews_count';
                $productIdCol = 'entity_pk_value';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::NEW_ARRIVALS:
                $joinTable = HelperData::NEW_ARRIVAL_DEFAULT_VIEW;
                $joinColumn = 'new_to';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::STOCK_QUANTITY:
                $joinTable = HelperData::STOCK_CONFIG_VIEW;
                $joinColumn = 'stock_qty';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
            case SortingOptions::WISH_LIST:
                $joinTable = ($customerId === null) ? HelperData::WISH_LIST_VIEW : 'wishlist_item';
                $joinColumn = 'qty';
                $defaultDir = $this->helperData->getSortOptionDirection($currentOrder);
                break;
        }

        return [
            'joinTable'    => $joinTable,
            'joinColumn'   => $joinColumn,
            'defaultDir'   => $defaultDir,
            'productIdCol' => $productIdCol,
            'storeId'      => $storeId,
        ];
    }

    /**
     * @param Select $selectCollection
     * @param $sortingData
     * @param $dir
     * @param $currentOrder
     *
     * @throws LocalizedException
     */
    public function betterSortConfig($selectCollection, $sortingData, $dir, $currentOrder)
    {
        $simpleView = $this->getTable(HelperData::BESTSELLER_SIMPLE_VIEW);
        $configView = $this->getTable($sortingData['joinTable']);
        $storeId = $sortingData['storeId'];
        $productId = $sortingData['productIdCol'];

        $simpleCond = $this->joinCondition($simpleView, 'product_id', $storeId, SortingOptions::BESTSELLER);
        $configCond = $this->joinCondition($configView, 'parent_id', $storeId, SortingOptions::BESTSELLER);
        $sortColumn = "IFNULL({$simpleView}.qty_ordered, {$configView}.qty_ordered)";

        switch ($currentOrder) {
            case SortingOptions::DISCOUNT:
                $simpleView = $this->getTable(HelperData::DISCOUNT_SIMPLE_VIEW);
                $simpleCond = $this->joinCondition($simpleView, $productId, null, $currentOrder);
                $configCond = $this->joinCondition($configView, 'parent_id', null, $currentOrder);
                $rulePrice = "IFNULL({$configView}.rule_price, {$simpleView}.rule_price)";
                $sortColumn = "(`e`.`price` - {$rulePrice})";
                if ($this->helperData->getDiscountBase() === DiscountBase::PERCENT) {
                    $sortColumn = "((`e`.`price` - {$rulePrice}) / `e`.`price` * 100)";
                }
                break;
            case SortingOptions::STOCK_QUANTITY:
                $simpleView = $this->getTable(HelperData::STOCK_SIMPLE_VIEW);
                $simpleCond = $this->joinCondition($simpleView, $productId, null);
                $configCond = $this->joinCondition($configView, 'parent_id', null);
                $sortColumn = "IFNULL({$simpleView}.simple_qty, {$configView}.config_qty)";
                break;
            case SortingOptions::NEW_ARRIVALS:
                $simpleView = $this->getTable(HelperData::NEW_ARRIVAL_STORE_VIEW);
                $simpleCond = $this->joinCondition($simpleView, $productId, $storeId);
                $configCond = $this->joinCondition($configView, $productId, 0);
                $sortColumn = "IFNULL({$simpleView}.new_to,{$configView}.new_to)";
                break;
        }

        $selectCollection->joinLeft(
            [$simpleView],
            $simpleCond,
            []
        )->joinLeft(
            [$configView],
            $configCond,
            []
        );
        $selectCollection->columns([$sortingData['joinColumn'] => new Zend_Db_Expr($sortColumn)]);
        $selectCollection->order($sortingData['joinColumn'] . ' ' . $dir);
    }

    /**
     * @param Select $selectCollection
     * @param $sortingData
     * @param $dir
     * @param $currentOrder
     *
     * @throws LocalizedException
     */
    public function betterSortSimple($selectCollection, $sortingData, $dir, $currentOrder)
    {
        $joinTable = $this->getTable($sortingData['joinTable']);
        $foreignKey = $sortingData['productIdCol'];
        $storeId = $sortingData['storeId'];
        $condition = $this->joinCondition($joinTable, $foreignKey, $storeId, $currentOrder);
        $selectCollection->joinLeft(
            [$joinTable],
            $condition,
            [$sortingData['joinColumn']]
        );
        $selectCollection->order($sortingData['joinColumn'] . ' ' . $dir);
    }

    /**
     * @param $table
     * @param $productIdCol
     * @param $storeId
     * @param null $order
     *
     * @return string
     * @throws LocalizedException
     */
    public function joinCondition($table, $productIdCol, $storeId, $order = null)
    {
        $customerId = $this->getCustomerId();
        $currentDate = $this->date->gmtDate('Y-m-d');
        $customer = $this->sessionFactory->create();
        $customerGroup = $customer->getCustomer()->getGroupId();
        $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
        $timeBase = $this->getTimeBase($order);

        $condition = "e.entity_id = {$table}.{$productIdCol}";
        $connection = $this->getConnection();
        if ($storeId !== null) {
            $condition .= $connection->quoteInto(" AND {$table}.store_id = ?", $storeId);
        }
        if ($order !== null) {
            if ($order === SortingOptions::WISH_LIST && $customerId !== null) {
                $wishListId = $this->wishlist->loadByCustomerId($customerId)->getId();
                $condition .= $connection->quoteInto(" AND {$table}.wishlist_id = ?", $wishListId);
            }
            if ($order === SortingOptions::BESTSELLER || $order === SortingOptions::MOST_VIEWED) {
                if ($timeBase !== null) {
                    $baseDate = date('Y-m-d', strtotime("-{$timeBase} days"));
                    $condition .= $connection->quoteInto(" AND ({$table}.period BETWEEN ?", $baseDate);
                    $condition .= $connection->quoteInto(' AND ?)', $currentDate);
                }
            }
            if ($order === SortingOptions::DISCOUNT) {
                $condition .= $connection->quoteInto(" AND {$table}.website_id = ?", $websiteId);
                $condition .= $connection->quoteInto(" AND {$table}.customer_group_id = ?", $customerGroup);
            }
        }

        return $condition;
    }

    /**
     * @return AdapterInterface
     */
    public function getConnection()
    {
        return $this->_collection->getConnection();
    }

    /**
     * @param $tableName
     *
     * @return string
     */
    public function getTable($tableName)
    {
        return $this->_collection->getResource()->getTable($tableName);
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        $customer = $this->sessionFactory->create();

        return $customer->getCustomer()->getId();
    }

    /**
     * @param $order
     *
     * @return mixed|null
     */
    public function getTimeBase($order)
    {
        if ($order === SortingOptions::BESTSELLER) {
            return $this->helperData->getBestSellerBase();
        }
        if ($order === SortingOptions::MOST_VIEWED) {
            return $this->helperData->getMostViewedBase();
        }

        return null;
    }
}
