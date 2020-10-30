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

namespace Lof\Formbuilder\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
         //Update for version 1.0.5
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $table = $installer->getTable('lof_formbuilder_form');

            $installer->getConnection()->addColumn(
                $table,
                'thankyou_field',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Thankyou Field'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'thankyou_email_template',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Thankyou Email Template'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'page_layout',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Page Layout'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'page_title',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Meta Title'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'layout_update_xml',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Layout Update XML'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'meta_keywords',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Meta Keywords'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'meta_description',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Meta Description'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'submit_text_color',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Submit Text Color'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'submit_background_color',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Submit Background Color'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'submit_hover_color',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Submit Hover Color'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'input_hover_color',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Input Hover Color'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'custom_template',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Custom Template'
                ]
            );

            $table = $installer->getTable('lof_formbuilder_message');

            $installer->getConnection()->addColumn(
                $table,
                'form_data',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Form Data'
                ]
            );
        }
         //Update for version 1.0.6
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_formbuilder_blacklist')
            )->addColumn(
                'blacklist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Blacklist ID'
            )->addColumn(
                'form_id',
                Table::TYPE_SMALLINT,
                null,
                ['default' => 0],
                'Form ID'
            )->addColumn(
                'message_id',
                Table::TYPE_SMALLINT,
                null,
                ['default' => 0],
                'Message ID'
            )->addColumn(
                'form_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Form Name'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Email'
            )->addColumn(
                'ip',
                Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'IP'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Status'
            )->addColumn(
                'note',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Note'
            )->addColumn(
                'created_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Form Created Time'
            )
            ->addColumn(
                'updated_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Form Modification Time'
            );
            $installer->getConnection()->createTable($table);
        }

         //Update for version 1.0.7
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_formbuilder_reply')
            )->addColumn(
                'reply_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                 'identity' => true,
                 'nullable' => false,
                 'primary'  => true,
                ],
                'Reply ID'
            )->addColumn(
                'message_id',
                Table::TYPE_SMALLINT,
                null,
                ['default' => 0],
                'Message ID'
            )->addColumn(
                'form_id',
                Table::TYPE_SMALLINT,
                null,
                ['default' => 0],
                'Form ID'
            )->addColumn(
                'email_from',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email From'
            )->addColumn(
                'email_to',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email To'
            )->addColumn(
                'subject',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Subject'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Message Content'
            )->addColumn(
                'created_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Form Created Time'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Status'
            )->addColumn(
                'error_message',
                Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'Error Message'
            );
            $installer->getConnection()->createTable($table);
        }
         //Update for version 1.0.7
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $foreignKeys = $this->getForeignKeys($installer);
            $this->dropForeignKeys($installer, $foreignKeys);
            $installer->getConnection()->modifyColumn(
                $installer->getTable('lof_formbuilder_form_customergroup'),
                'customer_group_id',
                [
                    'type' => 'integer',
                    'unsigned' => true,
                    'nullable' => false
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $table = $installer->getTable('lof_formbuilder_form');

            $installer->getConnection()->addColumn(
                $table,
                'sender_email_field',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Sender Email Field'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'sender_name_field',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Sender Name Field'
                ]
            );

        }

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param array $keys
     * @return void
     */
    private function dropForeignKeys(SchemaSetupInterface $setup, array $keys) {
        foreach ($keys as $key) {
            $setup->getConnection()->dropForeignKey($key['TABLE_NAME'], $key['FK_NAME']);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return array
     */
    private function getForeignKeys(SchemaSetupInterface $setup) {
        $foreignKeys = [];
        $keysTree = $setup->getConnection()->getForeignKeysTree();
        foreach ($keysTree as $indexes) {
            foreach ($indexes as $index) {
                if ($index['REF_TABLE_NAME'] == $setup->getTable('customer_group')
                    && $index['REF_COLUMN_NAME'] == 'customer_group_id'
                ) {
                    $foreignKeys[] = $index;
                }
            }
        }
        return $foreignKeys;
    }
}
