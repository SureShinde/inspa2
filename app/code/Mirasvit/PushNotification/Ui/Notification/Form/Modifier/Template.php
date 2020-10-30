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



namespace Mirasvit\PushNotification\Ui\Notification\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Template implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->createNewModal($meta);

        return $meta;
    }

    /**
     * @param array $meta
     * @return array
     */
    private function createNewModal($meta)
    {
        return $this->arrayManager->set(
            'create_template_modal',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'isTemplate'    => false,
                            'componentType' => 'modal',
                            'options'       => [
                                'title' => __('New Template'),
                            ],
                            'imports'       => [
                                'state' => '!index=create_template:responseStatus',
                            ],
                        ],
                    ],
                ],
                'children'  => [
                    'create_template' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label'            => '',
                                    'componentType'    => 'container',
                                    'component'        => 'Magento_Ui/js/form/components/insert-form',
                                    'dataScope'        => '',
                                    'update_url'       => $this->urlBuilder->getUrl('mui/index/render'),
                                    'render_url'       => $this->urlBuilder->getUrl(
                                        'mui/index/render_handle',
                                        [
                                            'handle'  => 'push_notification_template_create',
                                            'buttons' => 1,
                                        ]
                                    ),
                                    'autoRender'       => false,
                                    'ns'               => 'pushNotification_template_form',
                                    'externalProvider' => 'pushNotification_template_form.template_form_data_source',
                                    'toolbarContainer' => '${ $.parentName }',
                                    'formSubmitType'   => 'ajax',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
