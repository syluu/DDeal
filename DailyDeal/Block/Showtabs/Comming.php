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

class Comming extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory
     */
    protected $dailydealCollectionFactory;
    protected $dailydealModel;
    public $helperConfig;
    public $helperDailyDeal;

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
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    public function getCommingdeals()
    {
        return $this->_getProductCollection();
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

//    public function getDeals()
//    {
//        $deal = $this->dailydealCollectionFactory->create()->setValidationFilter(1)->getFirstItem();
//        return $deal;
//    }

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
