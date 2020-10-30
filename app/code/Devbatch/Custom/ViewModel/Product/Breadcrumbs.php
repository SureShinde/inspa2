<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Devbatch\Custom\ViewModel\Product;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Product breadcrumbs view model.
 */
class Breadcrumbs extends DataObject implements ArgumentInterface
{
    /**
     * Catalog data.
     *
     * @var Data
     */
    private $catalogData;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $json;
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Data $catalogData
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     * @param Escaper $escaper
     */
    public function __construct(
        Data $catalogData,
        ScopeConfigInterface $scopeConfig,
        Json $json = null,
        Escaper $escaper = null
    ) {
        parent::__construct();

        $this->catalogData = $catalogData;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(Escaper::class);
    }

    /**
     * Returns category URL suffix.
     *
     * @return mixed
     */
    public function getCategoryUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            'catalog/seo/category_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if categories path is used for product URLs.
     *
     * @return bool
     */
    public function isCategoryUsedInProductUrl()
    {
        return $this->scopeConfig->isSetFlag(
            'catalog/seo/product_use_categories',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns product name.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->catalogData->getProduct() !== null
            ? $this->catalogData->getProduct()->getName()
            : '';
    }

    /**
     * Returns breadcrumb json.
     *
     * @return string
     */
    public function getJsonConfiguration()
    {
        return $this->escaper->escapeHtml($this->json->serialize([
            'breadcrumbs' => [
                'categoryUrlSuffix' => $this->escaper->escapeHtml($this->getCategoryUrlSuffix()),
                'userCategoryPathInUrl' => (int)$this->isCategoryUsedInProductUrl(),
                'product' => $this->getProductName()
            ]
        ]));
    }
    public function getBreadCrumbsProduct()
    {
        /* Using Direct Object Manager */ 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /* Get store manager */ 
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        // BASE URL 
        $baseUrl = $storeManager->getStore()->getBaseUrl();
        $path = $this->catalogData->getBreadcrumbPath();
        echo"<li class='item home'>
                <a href='".$baseUrl."' title=''>Home</a>
                </li>";
        foreach ($path as $name => $breadcrumb) {
            if($name != 'product')
            {
               echo"<li class='item ".$name."'>
                <a href='".$breadcrumb['link']."' title=''>".$breadcrumb['label']."</a>
                </li>";
            }
            else
            {
                echo"<li class='item ".$name."'>
                <strong>".$breadcrumb['label']."</strong>
                </li>";
            }
        }
    }
}
