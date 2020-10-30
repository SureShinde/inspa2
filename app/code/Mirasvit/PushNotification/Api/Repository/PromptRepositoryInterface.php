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



namespace Mirasvit\PushNotification\Api\Repository;

use Mirasvit\PushNotification\Api\Data\PromptInterface;

interface PromptRepositoryInterface
{
    /**
     * @return \Mirasvit\PushNotification\Model\ResourceModel\Prompt\Collection|PromptInterface[]
     */
    public function getCollection();

    /**
     * @return PromptInterface
     */
    public function create();

    /**
     * @param PromptInterface $prompt
     * @return PromptInterface
     */
    public function save(PromptInterface $prompt);

    /**
     * @param int $id
     * @return PromptInterface|false
     */
    public function get($id);

    /**
     * @param PromptInterface $prompt
     * @return bool
     */
    public function delete(PromptInterface $prompt);
}