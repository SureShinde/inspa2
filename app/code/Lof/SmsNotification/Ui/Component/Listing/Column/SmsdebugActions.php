<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_SmsNotification
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SmsNotification\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

/**
 * Class PageActions
 */
class SmsdebugActions extends Column
{
    /** Url path */
    const CMS_URL_PATH_DELETE   = 'lofsmsnotification/smsdebug/delete';
    const CMS_URL_PATH_PREVIEW  = 'lofsmsnotification/smsdebug/preview';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;
    /**
     * @var string
     */
    private $deleteUrl;
    private $previewUrl;
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components   = [],
        array $data         = [],
        $deleteUrl          = self::CMS_URL_PATH_DELETE,
        $previewUrl         = self::CMS_URL_PATH_PREVIEW
    ) {
        $this->urlBuilder       = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->deleteUrl        = $deleteUrl;
        $this->previewUrl       = $previewUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['smsdebug_id'])) {

                    $item[$name]['delete'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $this->deleteUrl,
                                [
                                    'smsdebug_id' => $item['smsdebug_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.smsdebug_id }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.smsdebug_id }" record?')
                            ]
                    ];  
                    $item[$name]['preview'] = [
                        'label' => __('Preview'),
                         'confirm' => [
                            'title' => '<div class="lof-preview">'.$item['created_at'].'</div>',
                            'message' => '<div class="lof-content">'.$item['message'].'</div><script>jQuery(".modal-footer .action-primary.action-accept").css("display","none");</script>'
                        ]
                    ];   
                }
            }
        }
        return $dataSource;
    }

}
