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
 * @package   mirasvit/module-message-queue
 * @version   1.0.9
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Mq\Api\Data;

interface EnvelopeInterface
{
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
    public function getReference();

    /**
     * @param string $value
     * @return $this
     */
    public function setReference($value);

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @param string $value
     * @return $this
     */
    public function setQueueName($value);
}