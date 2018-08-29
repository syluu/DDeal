<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MW\DailyDeal\Block\Showtabs;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;

class Past extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory
     */
    protected $dailydealCollectionFactory;
    protected $dailydealModel;
    public $helperConfig;
    public $helperDailyDeal;
    public $productCollectionFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory  $dailydealCollectionFactory,
        \MW\DailyDeal\Model\Dailydeal  $dailydealModel,
        \MW\DailyDeal\Helper\Config $helperConfig,
        \MW\DailyDeal\Helper\Data $helperDailyDeal,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
//        $this->_catalogLayer = $layerResolver->get();
//        $this->_postDataHelper = $postDataHelper;
//        $this->categoryRepository = $categoryRepository;
//        $this->urlHelper = $urlHelper;
        $this->dailydealCollectionFactory = $dailydealCollectionFactory;
        $this->dailydealModel = $dailydealModel;
        $this->helperConfig = $helperConfig;
        $this->helperDailyDeal = $helperDailyDeal;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getProductCollection()
    {
        $productCollection = parent::_getProductCollection();
        return $productCollection;
    }

    public function getPastDeals()
    {
        $listProductIdDeal = $this->dailydealModel->getListPastDeal();
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $listProductIdDeal])
            ->getSelect();
        //get values of current page. if not the param value then it will set to 1
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit. if not the param value then it will set to 1
        $pageSize =($this->getRequest()->getParam('product_list_limit'))? $this->getRequest()->getParam('product_list_limit') : 9;
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    public function loadByProductId($productId)
    {
        return $this->dailydealModel->loadByProductId($productId);
    }

    public function getConfigIsShowImageCatalogList()
    {
        return $this->helperConfig->getConfigIsShowImageCatalogList();
    }

    public function getConfigDisplayQuantity()
    {
        return $this->helperConfig->getConfigDisplayQuantity();
    }
}
