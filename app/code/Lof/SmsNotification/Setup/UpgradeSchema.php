<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\SmsNotification\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'lof_smsnotification_otp'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_smsnotification_otp')
        )->addColumn(
            'otp_id',
            Table::TYPE_INTEGER,
            null,
           ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'OTP ID'
        )->addColumn(
            'digit_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Digit Code'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'phone'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'phone'
        )->addColumn(
            'resend',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => '0'],
            'resend'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        )->setComment(
            'Lof SmsNotification OTP'
        );
        $installer->getConnection()->createTable($table);
         /**
         * Create table 'lof_smsnotification_phone'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_smsnotification_phone')
        )->addColumn(
            'phone_id',
            Table::TYPE_INTEGER,
            null,
           ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Phone ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'customer Id'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'phone'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        )->setComment(
            'Lof SmsNotification Phone'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getTable('lof_smsnotification_phone');
        $installer->getConnection()->addColumn(
            $table,
            'country_code',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'country code'
            ]
        );

        $installer->endSetup();

        $installer->endSetup();
    }
    
}
