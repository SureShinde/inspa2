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

namespace Mageplaza\BetterSorting\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mageplaza\BetterSorting\Helper\Data;

/**
 * Class InstallSchema
 *
 * @package Mageplaza\BetterSorting\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * InstallSchema constructor.
     *
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings("Unused")
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $allViews = $this->helperData->getViewsArray();
        foreach ($allViews as $view) {
            if (!$installer->tableExists($view)) {
                $sql = $this->getViewSql($setup, $view);
                $installer->getConnection()->query($sql);
            }
        }
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param $view
     *
     * @return string
     */
    public function getViewSql(SchemaSetupInterface $setup, $view)
    {
        $viewSql = '';
        $installer = $setup;
        $installer->startSetup();

        $salesBestseller = $installer->getTable('sales_bestsellers_aggregated_daily');
        $productLink = $installer->getTable('catalog_product_super_link');
        $inventoryStockItem = $installer->getTable('cataloginventory_stock_item');
        $productEntity = $installer->getTable('catalog_product_entity');
        $mostViewed = $installer->getTable('report_viewed_product_aggregated_daily');
        $productDateTime = $installer->getTable('catalog_product_entity_datetime');
        $catalogRule = $installer->getTable('catalogrule_product_price');
        $wishList = $installer->getTable('wishlist_item');

        $edition = $this->helperData->getMagentoEdition();
        $productIdCol = ($edition === 'Community') ? 'entity_id' : 'row_id';

        switch ($view) {
            case Data::BESTSELLER_CONFIG_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT AVG(bestseller.product_id), bestseller.store_id,
                            FROM_UNIXTIME(AVG(unix_timestamp(bestseller.period))) AS period,
                            SUM(bestseller.qty_ordered) AS qty_ordered, link.parent_id
                    FROM {$salesBestseller} AS bestseller
                    JOIN {$productLink} AS link  ON link.product_id = bestseller.product_id
                    GROUP BY link.parent_id, bestseller.store_id";
                break;
            case Data::BESTSELLER_SIMPLE_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT bestseller.product_id, bestseller.store_id, 
                            FROM_UNIXTIME(AVG(unix_timestamp(bestseller.period))) AS period, 
                            SUM(bestseller.qty_ordered) AS qty_ordered
                    FROM {$salesBestseller} AS bestseller
                    LEFT OUTER JOIN {$productLink} AS link ON bestseller.product_id = link.product_id
                    JOIN {$productEntity} AS entity ON entity.entity_id = bestseller.product_id
                    WHERE link.parent_id IS NULL AND entity.type_id = 'simple' 
                    GROUP BY bestseller.product_id, bestseller.store_id";
                break;
            case Data::STOCK_CONFIG_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT AVG(stock.product_id), SUM(stock.qty) AS config_qty, link.parent_id
                    FROM {$inventoryStockItem} AS stock 
                    LEFT JOIN {$productLink} AS link ON link.product_id = stock.product_id
                    GROUP BY link.parent_id ";
                break;
            case Data::STOCK_SIMPLE_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT stock.product_id, stock.qty AS simple_qty
                    FROM {$inventoryStockItem} AS stock
                    LEFT OUTER JOIN {$productLink} AS link ON stock.product_id = link.product_id
                    JOIN {$productEntity} AS entity ON entity.entity_id = stock.product_id
                    WHERE link.parent_id IS NULL AND entity.type_id = 'simple'";
                break;
            case Data::MOST_VIEWED_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT product_id,
                    FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(period))) AS period, store_id, SUM(views_num) AS views_num
                    FROM {$mostViewed}
                    GROUP BY product_id, store_id";
                break;
            case Data::NEW_ARRIVAL_DEFAULT_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT {$productIdCol} as product_id, store_id, MAX(VALUE) AS new_to  
                    FROM {$productDateTime}
                    WHERE store_id = 0
                    GROUP BY product_id, store_id";
                break;
            case Data::NEW_ARRIVAL_STORE_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT {$productIdCol} as product_id, store_id, MAX(VALUE) AS new_to  
                    FROM {$productDateTime}
                    WHERE store_id != 0
                    GROUP BY product_id, store_id";
                break;
            case Data::DISCOUNT_CONFIG_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT AVG(catalogrule.product_id), catalogrule.customer_group_id, catalogrule.website_id, catalogrule.rule_price, link.parent_id
                    FROM {$catalogRule} AS catalogrule
                    JOIN {$productLink} AS link ON catalogrule.product_id = link.product_id
                    GROUP BY link.parent_id, catalogrule.customer_group_id, catalogrule.website_id, catalogrule.rule_price";
                break;
            case Data::DISCOUNT_SIMPLE_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT catalogrule.product_id, catalogrule.customer_group_id, catalogrule.website_id, catalogrule.rule_price
                    FROM {$catalogRule} AS catalogrule
                    LEFT OUTER JOIN {$productLink} AS link ON catalogrule.product_id = link.product_id
                    JOIN {$productEntity} AS entity ON entity.entity_id = catalogrule.product_id
                    WHERE link.parent_id IS NULL AND entity.type_id = 'simple' 
                    GROUP BY catalogrule.product_id, catalogrule.customer_group_id, catalogrule.website_id, catalogrule.rule_price";
                break;
            case Data::WISH_LIST_VIEW:
                $viewSql = "CREATE SQL SECURITY INVOKER VIEW {$view} AS
                    SELECT product_id, store_id, SUM(qty) AS qty 
                    FROM {$wishList}
                    GROUP BY product_id, store_id";
                break;
        }

        return $viewSql;
    }
}
