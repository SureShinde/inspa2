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
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 **/

namespace Lof\SmsNotification\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'lof_smsnotification_smslog'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smsnotification_smslog'))
            ->addColumn(
                'smslog_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'from',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )
            ->addColumn(
                'to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )
            ->addColumn(
                'status',
                 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )->setComment('Lof SmsNotification Log Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'lof_smsnotification_smsdebug'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smsnotification_smsdebug'))
            ->addColumn(
                'smsdebug_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'from',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )
            ->addColumn(
                'to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->addColumn(
                'send_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->setComment('Lof SmsNotification Log Table');
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_smsnotification_blacklist'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smsnotification_blacklist'))
            ->addColumn(
                'blacklist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'sms',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->setComment('Lof SmsNotification Blacklist Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
