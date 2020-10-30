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
use Mirasvit\PushNotification\Api\Data\TemplateInterface;

class Template extends AbstractModel implements TemplateInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\PushNotification\Model\ResourceModel\Template');
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
