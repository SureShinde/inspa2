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



namespace Mirasvit\PushNotification\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\PushNotification\Api\Data\SubscriberInterface;

class Subscriber extends AbstractModel implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\PushNotification\Model\ResourceModel\Subscriber');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return $this->getData(self::ENDPOINT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndpoint($value)
    {
        return $this->setData(self::ENDPOINT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrowserName()
    {
        return $this->getData(self::BROWSER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrowserName($value)
    {
        return $this->setData(self::BROWSER_NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceType()
    {
        return $this->getData(self::DEVICE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeviceType($value)
    {
        return $this->setData(self::DEVICE_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperationSystem()
    {
        return $this->getData(self::OPERATION_SYSTEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setOperationSystem($value)
    {
        return $this->setData(self::OPERATION_SYSTEM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * {@inheritdoc}
     */
    public function setIp($value)
    {
        return $this->setData(self::IP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry($value)
    {
        return $this->setData(self::COUNTRY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFingerprintSerialized()
    {
        return $this->getData(self::FINGERPRINT_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setFingerprintSerialized($value)
    {
        return $this->setData(self::FINGERPRINT_SERIALIZED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }
}
