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
use Mirasvit\PushNotification\Api\Data\PromptInterface;

class Prompt extends AbstractModel implements PromptInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\PushNotification\Model\ResourceModel\Prompt');
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
    public function getHeadline()
    {
        return $this->getData(self::HEADLINE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeadline($value)
    {
        return $this->setData(self::HEADLINE, $value);
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
    public function getAcceptText()
    {
        return $this->getData(self::ACCEPT_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAcceptText($value)
    {
        return $this->setData(self::ACCEPT_TEXT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRejectText()
    {
        return $this->getData(self::REJECT_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setRejectText($value)
    {
        return $this->setData(self::REJECT_TEXT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelay()
    {
        return $this->getData(self::DELAY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDelay($value)
    {
        return $this->setData(self::DELAY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($value)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDefault($value)
    {
        return $this->setData(self::IS_DEFAULT, $value);
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
    public function getStoreIds()
    {
        return array_filter(explode(',', $this->getData(self::STORE_IDS)));
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($value)
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
