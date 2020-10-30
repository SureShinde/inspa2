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



namespace Mirasvit\PushNotification\Api\Data;

interface SubscriberInterface
{
    const TABLE_NAME = 'mst_push_notification_subscriber';

    const STATUS_SUBSCRIBED = 'subscribed';

    const BROWSER_NAME_CHROME = 'chrome';
    const BROWSER_NAME_FIREFOX = 'firefox';
    const BROWSER_NAME_SAFARI = 'safari';
    const BROWSER_NAME_OPERA = 'opera';
    const BROWSER_NAME_UNKNOWN = 'unknown';

    const DEVICE_TYPE_DESKTOP = 'desktop';
    const DEVICE_TYPE_MOBILE = 'mobile';
    const DEVICE_TYPE_TABLET = 'tablet';

    const ID = 'subscriber_id';
    const ENDPOINT = 'endpoint';
    const CUSTOMER_ID = 'customer_id';
    const BROWSER_NAME = 'browser_name';
    const DEVICE_TYPE = 'device_type';
    const OPERATION_SYSTEM = 'operation_system';
    const IP = 'ip';
    const COUNTRY = 'country';
    const STORE_ID = 'store_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const FINGERPRINT_SERIALIZED = 'fingerprint_serialized';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @param string $value
     * @return $this
     */
    public function setEndpoint($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return string
     */
    public function getBrowserName();

    /**
     * @param string $value
     * @return $this
     */
    public function setBrowserName($value);

    /**
     * @return string
     */
    public function getDeviceType();

    /**
     * @param string $value
     * @return $this
     */
    public function setDeviceType($value);

    /**
     * @return string
     */
    public function getOperationSystem();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperationSystem($value);

    /**
     * @return string
     */
    public function getIp();

    /**
     * @param string $value
     * @return $this
     */
    public function setIp($value);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $value
     * @return $this
     */
    public function setCountry($value);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $value
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getFingerprintSerialized();

    /**
     * @param string $value
     * @return $this
     */
    public function setFingerprintSerialized($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value);
}
