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

use Mirasvit\PushNotification\Api\Data\TemplateInterface;

interface TemplateRepositoryInterface
{
    /**
     * @return \Mirasvit\PushNotification\Model\ResourceModel\Template\Collection|TemplateInterface[]
     */
    public function getCollection();

    /**
     * @return TemplateInterface
     */
    public function create();

    /**
     * @param TemplateInterface $template
     * @return TemplateInterface
     */
    public function save(TemplateInterface $template);

    /**
     * @param int $id
     * @return TemplateInterface|false
     */
    public function get($id);

    /**
     * @param TemplateInterface $template
     * @return bool
     */
    public function delete(TemplateInterface $template);
}
