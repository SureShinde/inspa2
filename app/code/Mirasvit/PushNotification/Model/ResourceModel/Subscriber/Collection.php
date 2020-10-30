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



namespace Mirasvit\PushNotification\Model\ResourceModel\Subscriber;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\PushNotification\Model\Subscriber::class,
            \Mirasvit\PushNotification\Model\ResourceModel\Subscriber::class
        );
    }

    /**
     * @param object|array|int $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof Store) {
            $ids = [$store->getId()];
        } elseif (!is_array($store)) {
            $ids = [$store];
        } else {
            $ids = $store;
        }

        $ids = array_filter($ids);

        if (!count($ids) || in_array(0, $ids)) {
            return $this;
        }

        $this->getSelect()->where('store_id IN(?)', $ids);

        return $this;
    }
}
