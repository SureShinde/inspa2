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

interface PromptInterface
{
    const TABLE_NAME = 'mst_push_notification_prompt';

    const ID = 'prompt_id';
    const NAME = 'name';
    const HEADLINE = 'headline';
    const BODY = 'body';
    const ACCEPT_TEXT = 'accept_text';
    const REJECT_TEXT = 'reject_text';
    const DELAY = 'delay';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';

    const IS_DEFAULT = 'is_default';
    const IS_ACTIVE = 'is_active';

    const STORE_IDS = 'store_ids';

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
    public function getHeadline();

    /**
     * @param string $value
     * @return $this
     */
    public function setHeadline($value);

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
    public function getAcceptText();

    /**
     * @param string $value
     * @return $this
     */
    public function setAcceptText($value);

    /**
     * @return string
     */
    public function getRejectText();

    /**
     * @param string $value
     * @return $this
     */
    public function setRejectText($value);

    /**
     * @return int
     */
    public function getDelay();

    /**
     * @param int $value
     * @return $this
     */
    public function setDelay($value);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $value
     * @return $this
     */
    public function setConditionsSerialized($value);

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsDefault($value);

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
     * @return array
     */
    public function getStoreIds();

    /**
     * @param array $value
     * @return $this
     */
    public function setStoreIds($value);
}