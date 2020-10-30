<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-push-notification
 * @version   1.1.18
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\PushNotification\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;
use Mirasvit\PushNotification\Api\Data\PromptInterface;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mirasvit\PushNotification\Api\Data\TemplateInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $table = $connection->newTable(
            $installer->getTable(SubscriberInterface::TABLE_NAME)
        )->addColumn(
            SubscriberInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Subscriber Id'
        )->addColumn(
            SubscriberInterface::ENDPOINT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Endpoint'
        )->addColumn(
            SubscriberInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Customer ID'
        )->addColumn(
            SubscriberInterface::BROWSER_NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Browser Name'
        )->addColumn(
            SubscriberInterface::DEVICE_TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Device Type'
        )->addColumn(
            SubscriberInterface::OPERATION_SYSTEM,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Operation System'
        )->addColumn(
            SubscriberInterface::IP,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'IP'
        )->addColumn(
            SubscriberInterface::COUNTRY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Country'
        )->addColumn(
            SubscriberInterface::STORE_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Store ID'
        )->addColumn(
            SubscriberInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Status'
        )->addColumn(
            SubscriberInterface::FINGERPRINT_SERIALIZED,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Fingerprint'
        )->addColumn(
            SubscriberInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            SubscriberInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName(SubscriberInterface::TABLE_NAME, [SubscriberInterface::ENDPOINT]),
            [SubscriberInterface::ENDPOINT]
        );

        $connection->dropTable($setup->getTable(SubscriberInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(TemplateInterface::TABLE_NAME)
        )->addColumn(
            TemplateInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Id'
        )->addColumn(
            TemplateInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            TemplateInterface::SUBJECT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Subject'
        )->addColumn(
            TemplateInterface::BODY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Body'
        )->addColumn(
            TemplateInterface::URL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'url'
        )->addColumn(
            TemplateInterface::IMAGE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image'
        )->addColumn(
            TemplateInterface::ICON,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Icon'
        )->addColumn(
            TemplateInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            TemplateInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );

        $connection->dropTable($setup->getTable(TemplateInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(MessageInterface::TABLE_NAME)
        )->addColumn(
            MessageInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Id'
        )->addColumn(
            MessageInterface::SUBSCRIBER_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Subscriber ID'
        )->addColumn(
            MessageInterface::ENDPOINT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Endpoint'
        )->addColumn(
            MessageInterface::NOTIFICATION_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Notification ID'
        )->addColumn(
            MessageInterface::TEMPLATE_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Template ID'
        )->addColumn(
            MessageInterface::SUBJECT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Subject'
        )->addColumn(
            MessageInterface::BODY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Body'
        )->addColumn(
            MessageInterface::URL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'url'
        )->addColumn(
            MessageInterface::IMAGE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image'
        )->addColumn(
            MessageInterface::ICON,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Icon'
        )->addColumn(
            MessageInterface::IS_PUSHED,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Pushed'
        )->addColumn(
            MessageInterface::IS_FETCHED,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Fetched'
        )->addColumn(
            MessageInterface::IS_CLICKED,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Clicked'
        )->addColumn(
            MessageInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            MessageInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );

        $connection->dropTable($setup->getTable(MessageInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(NotificationInterface::TABLE_NAME)
        )->addColumn(
            NotificationInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Notification Id'
        )->addColumn(
            NotificationInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            NotificationInterface::TEMPLATE_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Template Id'
        )->addColumn(
            NotificationInterface::STATE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'State'
        )->addColumn(
            NotificationInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            NotificationInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type'
        )->addColumn(
            NotificationInterface::EVENT_IDENTIFIER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Event Identifier'
        )->addColumn(
            NotificationInterface::PUSHED,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Pushed'
        )->addColumn(
            NotificationInterface::FETCHED,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Fetched'
        )->addColumn(
            NotificationInterface::CLICKED,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Clicked'
        )->addColumn(
            MessageInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            MessageInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );

        $connection->dropTable($setup->getTable(NotificationInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(PromptInterface::TABLE_NAME)
        )->addColumn(
            PromptInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Prompt Id'
        )->addColumn(
            PromptInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            PromptInterface::HEADLINE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Headline'
        )->addColumn(
            PromptInterface::BODY,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Body'
        )->addColumn(
            PromptInterface::ACCEPT_TEXT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Accept text'
        )->addColumn(
            PromptInterface::REJECT_TEXT,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Reject text'
        )->addColumn(
            PromptInterface::DELAY,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Delay'
        )->addColumn(
            PromptInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Conditions'
        )->addColumn(
            PromptInterface::IS_DEFAULT,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Default'
        )->addColumn(
            PromptInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            PromptInterface::STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Store Ids'
        )->addColumn(
            MessageInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            MessageInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );

        $connection->dropTable($setup->getTable(PromptInterface::TABLE_NAME));
        $connection->createTable($table);
    }
}
