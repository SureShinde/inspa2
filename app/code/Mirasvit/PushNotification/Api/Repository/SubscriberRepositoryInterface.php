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



namespace Mirasvit\PushNotification\Api\Repository;

use Mirasvit\PushNotification\Api\Data\SubscriberInterface;

interface SubscriberRepositoryInterface
{
    /**
     * @return \Mirasvit\PushNotification\Model\ResourceModel\Subscriber\Collection|SubscriberInterface[]
     */
    public function getCollection();

    /**
     * @return SubscriberInterface
     */
    public function create();

    /**
     * @param SubscriberInterface $subscriber
     * @return SubscriberInterface
     */
    public function save(SubscriberInterface $subscriber);

    /**
     * @param int $id
     * @return SubscriberInterface|false
     */
    public function get($id);

    /**
     * @param string $endpoint
     * @return SubscriberInterface|false
     */
    public function getByEndpoint($endpoint);

    /**
     * @param SubscriberInterface $subscriber
     * @return bool
     */
    public function delete(SubscriberInterface $subscriber);
}
