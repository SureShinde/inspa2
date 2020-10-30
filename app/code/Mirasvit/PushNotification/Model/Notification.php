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
use Mirasvit\PushNotification\Api\Data\NotificationInterface;

class Notification extends AbstractModel implements NotificationInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\PushNotification\Model\ResourceModel\Notification');
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
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
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setState($value)
    {
        return $this->setData(self::STATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduleType()
    {
        return $this->getData(self::SCHEDULE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduleType($value)
    {
        return $this->setData(self::SCHEDULE_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduleDate()
    {
        return $this->getData(self::SCHEDULE_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduleDate($value)
    {
        return $this->setData(self::SCHEDULE_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventIdentifier()
    {
        return $this->getData(self::EVENT_IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventIdentifier($value)
    {
        return $this->setData(self::EVENT_IDENTIFIER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPushed()
    {
        return $this->getData(self::PUSHED);
    }

    /**
     * {@inheritdoc}
     */
    public function setPushed($value)
    {
        return $this->setData(self::PUSHED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFetched()
    {
        return $this->getData(self::FETCHED);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetched($value)
    {
        return $this->setData(self::FETCHED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getClicked()
    {
        return $this->getData(self::CLICKED);
    }

    /**
     * {@inheritdoc}
     */
    public function setClicked($value)
    {
        return $this->setData(self::CLICKED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return explode(',', $this->getData(self::STORE_IDS));
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds(array $value)
    {
        return $this->setData(self::STORE_IDS, implode(',', $value));
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
