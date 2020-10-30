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

interface NotificationInterface
{
    const TABLE_NAME = 'mst_push_notification_notification';

    // draft -> pending (no messages) -> schedule (messages are generated) -> sent (messages are sent)
    const STATE_DRAFT     = 'draft';
    const STATE_PENDING   = 'pending';
    const STATE_SCHEDULED = 'scheduled';
    const STATE_SENT      = 'sent';

    const TYPE_MANUAL  = 'manual';
    const TYPE_TRIGGER = 'trigger';

    const ID               = 'notification_id';
    const NAME             = 'name';
    const TEMPLATE_ID      = TemplateInterface::ID;
    const STATE            = 'state';
    const TYPE             = 'type';
    const IS_ACTIVE        = 'is_active';
    const EVENT_IDENTIFIER = 'event_identifier';

    const SCHEDULE_TYPE = 'schedule_type';
    const SCHEDULE_DATE = 'schedule_date';

    const PUSHED  = 'pushed';
    const FETCHED = 'fetched';
    const CLICKED = 'clicked';

    const CREATED_AT = TemplateInterface::CREATED_AT;
    const UPDATED_AT = TemplateInterface::UPDATED_AT;

    const STORE_IDS = 'store_ids';

    const STATUS_ACTIVE = 1;

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
    public function getState();

    /**
     * @param string $value
     * @return $this
     */
    public function setState($value);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getScheduleType();

    /**
     * @param string $value
     * @return $this
     */
    public function setScheduleType($value);

    /**
     * @return string
     */
    public function getScheduleDate();

    /**
     * @param string $value
     * @return $this
     */
    public function setScheduleDate($value);

    /**
     * @return string
     */
    public function getEventIdentifier();

    /**
     * @param string $value
     * @return $this
     */
    public function setEventIdentifier($value);

    /**
     * @return int
     */
    public function getPushed();

    /**
     * @param int $value
     * @return $this
     */
    public function setPushed($value);

    /**
     * @return int
     */
    public function getFetched();

    /**
     * @param int $value
     * @return $this
     */
    public function setFetched($value);

    /**
     * @return int
     */
    public function getClicked();

    /**
     * @param int $value
     * @return $this
     */
    public function setClicked($value);

    /**
     * @param array $value
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return array
     */
    public function getStoreIds();

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
