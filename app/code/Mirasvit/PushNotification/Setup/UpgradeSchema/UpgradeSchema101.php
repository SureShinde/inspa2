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



namespace Mirasvit\PushNotification\Setup\UpgradeSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Data\NotificationInterface;

class UpgradeSchema101 implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable(NotificationInterface::TABLE_NAME),
            'store_ids', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => NotificationInterface::STORE_IDS,
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable(MessageInterface::TABLE_NAME),
            'store_id', [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => false,
                'default'  => 0,
                'comment'  => MessageInterface::STORE_ID,
            ]
        );
    }
}