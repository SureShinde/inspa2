<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_BetterSorting
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterSorting\Model\System\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mageplaza\BetterSorting\Helper\Data as HelperData;

/**
 * Class SearchDefaultSort
 *
 * @package Mageplaza\BetterSorting\Model\System\Config\Backend
 */
class SearchDefaultSort extends Value
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SearchDefaultSort constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param HelperData $helperData
     * @param RequestInterface $request
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        HelperData $helperData,
        RequestInterface $request,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->request = $request;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return Value|void
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $savedValue = $this->getValue();
        $storeId = $this->request->getParam('store', 1);
        $availableSorts = $this->helperData->getEnabledSortOptions($storeId);
        if (!in_array($savedValue, $availableSorts, true)) {
            throw new ValidatorException(__('This sorting option is not enabled'));
        }
        $this->setValue($savedValue);
        parent::beforeSave();
    }
}
