<?php
namespace Devbatch\CategoryShortDescription\Block;

class CategoryCollection extends \Magento\Framework\View\Element\Template
{

     protected $_categoryHelper;
     protected $categoryFlatConfig;
     protected $topMenu;
     private $categoryFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {

        $this->_categoryHelper = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->topMenu = $topMenu;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }
    /**
     * Return categories helper
     */   
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * Return categories helper
     * getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
     * example getHtml('level-top', 'submenu', 0)
     */   
    public function getHtml()
    {
        return $this->topMenu->getHtml();
    }
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */    
   public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }
    /**
     * Retrieve child store categories
     *
     */ 
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }
    
    public function getChildCategoryHtml($category, $icon_open_class="porto-icon-plus-squared", $icon_close_class="porto-icon-minus-squared") {
        $html = '';
        if($childrenCategories = $this->getChildCategories($category)) {
            $html .= '<ul>';
            $i = 0;
            foreach($childrenCategories as $childrenCategory) {
                if (!$childrenCategory->getIsActive()) {
                    continue;
                }
                $i++;
                $html .= '<li><a href="'.$this->_categoryHelper->getCategoryUrl($childrenCategory).'">'.$childrenCategory->getName().'</a>';
                    // Add by Muhib
            $PCategory = $this->categoryFactory->create()->load($childrenCategory->getId());
            // echo $PCategory->getData("tooltip");
            // End
            if($PCategory->getData("tooltip")){
            $html .= '<div class="tooltip wrapper">';
            $html .= '<span class="fa fa-info-circle" data-html="true" data-toggle="popover" data-content="<a href='.'#'.' class='.'close'.' data-dismiss='.'alert'.'>&times;</a>';
            $html .= $PCategory->getData("tooltip").'"></span></div>';
            }
                $html .= $this->getChildCategoryHtml($childrenCategory, $icon_open_class, $icon_close_class);
                $html .= '</li>';
            }
           
            $html .= '</ul>';
            if($i > 0)
                $html .= '<a href="javascript:void(0)" class="expand-icon"><em class="'.$icon_open_class.'"></em></a>';
        }
        return $html;
    }
    
    public function getCategorySidebarHtml($icon_open_class="porto-icon-plus-squared", $icon_close_class="porto-icon-minus-squared") {
        $html = '';
        $categories = $this->getStoreCategories(true,false,true);
        $html .= '<ul class="category-sidebar">';
        foreach($categories as $category) {
            if (!$category->getIsActive()) {
            continue;
            }
            $html .= '<li>';
            $html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($category).'">'.$category->getName().'</a>';
             // Add by Muhib
            $PCategory = $this->categoryFactory->create()->load($category->getId());
            // echo $PCategory->getData("tooltip");
            // End
            if($PCategory->getData("tooltip")){
            $html .= '<div class="tooltip wrapper">';
            $html .= '<span class="fa fa-info-circle" data-html="true" data-toggle="popover" data-content="<a href='.'#'.' class='.'close'.' data-dismiss='.'alert'.'>&times;</a>';
            $html .= $PCategory->getData("tooltip").'"></span></div>';
            }
            $html .= $this->getChildCategoryHtml($category, $icon_open_class, $icon_close_class);
            $html .= '</li>';
            

        }
        $html .= '</ul>';
        $html .= '<script type="text/javascript">
     require(["jquery", "jquery/ui"], function($){ 
    jQuery(function($){
        $(".category-sidebar li > .expand-icon").click(function(){
            if($(this).parent().hasClass("opened")){
                $(this).parent().children("ul").slideUp();
                $(this).parent().removeClass("opened");
                $(this).children(".'.$icon_close_class.'").removeClass("'.$icon_close_class.'").addClass("'.$icon_open_class.'");
            } else {
                $(this).parent().children("ul").slideDown();
                $(this).parent().addClass("opened");
                $(this).children(".'.$icon_open_class.'").removeClass("'.$icon_open_class.'").addClass("'.$icon_close_class.'");
            }
        });
    }); 
    });
</script>';
$html .= '<script type="text/javascript">
    require([
        "jquery"        
        ], function ($) {
            $(".fa-info-circle").popover({
                container: "body"
            });
            $(document).on("click", ".popover .close" , function(){
                $(this).parents(".popover").popover("hide");
            });
        });
</script>';
        return $html;
    }
}
