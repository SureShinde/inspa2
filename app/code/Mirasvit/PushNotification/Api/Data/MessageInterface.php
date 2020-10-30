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

interface MessageInterface
{
    const TABLE_NAME = 'mst_push_notification_message';

    const ID = 'message_id';
    const SUBSCRIBER_ID = SubscriberInterface::ID;
    const ENDPOINT = SubscriberInterface::ENDPOINT;
    const NOTIFICATION_ID = NotificationInterface::ID;
    const TEMPLATE_ID = TemplateInterface::ID;
    const SUBJECT = TemplateInterface::SUBJECT;
    const BODY = TemplateInterface::BODY;
    const URL = TemplateInterface::URL;
    const IMAGE = TemplateInterface::IMAGE;
    const ICON = TemplateInterface::ICON;

    const IS_PUSHED = 'is_pushed';
    const IS_FETCHED = 'is_fetched';
    const IS_CLICKED = 'is_clicked';

    const STORE_ID = 'store_id';

    const CREATED_AT = TemplateInterface::CREATED_AT;
    const UPDATED_AT = TemplateInterface::UPDATED_AT;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getSubscriberId();

    /**
     * @param int $value
     * @return $this
     */
    public function setSubscriberId($value);

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
    public function getNotificationId();

    /**
     * @param int $value
     * @return $this
     */
    public function setNotificationId($value);

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @param int $value
     * @return $this
     */
    public function setTemplateId($value);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $value
     * @return $this
     */
    public function setSubject($value);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $value
     * @return $this
     */
    public function setBody($value);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setImage($value);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $value
     * @return $this
     */
    public function setIcon($value);

    /**
     * @return bool
     */
    public function isPushed();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsPushed($value);

    /**
     * @return bool
     */
    public function isFetched();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsFetched($value);

    /**
     * @return bool
     */
    public function isClicked();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsClicked($value);

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
