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
use Mirasvit\PushNotification\Api\Data\MessageInterface;

class Message extends AbstractModel implements MessageInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\PushNotification\Model\ResourceModel\Message');
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
    public function getSubscriberId()
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscriberId($value)
    {
        return $this->setData(self::SUBSCRIBER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationId()
    {
        return $this->getData(self::NOTIFICATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotificationId($value)
    {
        return $this->setData(self::NOTIFICATION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateId($value)
    {
        return $this->setData(self::TEMPLATE_ID, $value);
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
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($value)
    {
        return $this->setData(self::SUBJECT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->getData(self::BODY);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($value)
    {
        return $this->setData(self::BODY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->getData(self::ICON);
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon($value)
    {
        return $this->setData(self::ICON, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isPushed()
    {
        return $this->getData(self::IS_PUSHED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPushed($value)
    {
        return $this->setData(self::IS_PUSHED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isFetched()
    {
        return $this->getData(self::IS_FETCHED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFetched($value)
    {
        return $this->setData(self::IS_FETCHED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isClicked()
    {
        return $this->getData(self::IS_CLICKED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsClicked($value)
    {
        return $this->setData(self::IS_CLICKED, $value);
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
