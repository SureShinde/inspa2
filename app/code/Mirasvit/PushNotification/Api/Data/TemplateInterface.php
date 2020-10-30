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

interface TemplateInterface
{
    const TABLE_NAME = 'mst_push_notification_template';

    const ID = 'template_id';
    const NAME = 'name';
    const SUBJECT = 'subject';
    const BODY = 'body';
    const URL = 'url';
    const IMAGE = 'image';
    const ICON = 'icon';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

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
