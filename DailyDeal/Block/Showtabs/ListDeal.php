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

class ListDeal extends \Magento\Catalog\Block\Product\ListProduct
{
    const STATUS_ENABLED = '1';
    const STATUS_EXPIRE_FALSE = '1';
    /**
     * @var \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory
     */
    protected $dailydealCollectionFactory;
    protected $dealSchecdulerCollectionFactory;
    protected $dealSchecdulerModel;
    protected $dailydealModel;
    public $helperConfig;
    public $helperDailyDeal;
    public $filterGroup;
    public $filterBuilder;
    public $productStatus;
    public $productVisibility;
    public $productRepository;
    public $searchCriteria;
    public $productCollectionFactory;
    public $productModel;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory $dailydealCollectionFactory,
        \MW\DailyDeal\Model\ResourceModel\Dealscheduler\CollectionFactory $dealSchedulerCollectionFactory,
        \MW\DailyDeal\Model\Dealscheduler $dealSchecdulerModel,
        \MW\DailyDeal\Model\Dailydeal $dailydealModel,
        \MW\DailyDeal\Helper\Config $helperConfig,
        \MW\DailyDeal\Helper\Data $helperDailyDeal,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product $productModel,
        array $data = []
    ) {
//        $this->_catalogLayer = $layerResolver->get();
//        $this->_postDataHelper = $postDataHelper;
//        $this->categoryRepository = $categoryRepository;
//        $this->urlHelper = $urlHelper;
        $this->dailydealCollectionFactory = $dailydealCollectionFactory;
        $this->dealSchecdulerCollectionFactory = $dealSchedulerCollectionFactory;
        $this->dealSchecdulerModel = $dealSchecdulerModel;
        $this->dailydealModel = $dailydealModel;
        $this->helperConfig = $helperConfig;
        $this->helperDailyDeal = $helperDailyDeal;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $criteria;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productModel = $productModel;
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
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {

            $store_id = 1;
            $currenttime = date('Y-m-d H:i:s', time());

            $collection = $this->dailydealModel
                ->getCollection()
                ->addFieldToFilter('status', self::STATUS_ENABLED);
//                ->addFieldToFilter('expire', self::STATUS_EXPIRE_FALSE);
//                ->addFieldToFilter('store_view', array(array('like' => '%' . Mage::app()->getStore()->getId() . '%'), array('like' => '0')))
//                ->addProductStatusFilter($store_id);

//            $collection->addFieldToFilter('start_date_time', array('to' => $currenttime))
//                ->addFieldToFilter('end_date_time', array('from' => $currenttime));

            $collection->getSelect()->joinLeft(
                array('stock' => 'cataloginventory_stock_item'), 'stock.product_id = main_table.product_id', array('stock.qty', 'stock.is_in_stock')
            );
            $listProductIdDeal = $this->dailydealModel->getListActiveDeal();
//            $collection->getSelect()->where("stock.is_in_stock = " . Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);
            $col = $this->productCollectionFactory->create()->addFieldToFilter('entity_id', ['in' => $listProductIdDeal]);
            return $col;
        }
        return $this->_productCollection;
    }

    public function getProductListForDeal()
    {
        $productColection = $this->_getProductCollection();
        $productColection->addAttributeToFilter('type_id', 'configurable');
        return $productColection;
    }

    public function getDeals()
    {
        $deal = $this->dailydealCollectionFactory->create()->setValidationFilter(1)->getFirstItem();
        return $deal;
    }

    public function getScheduleById($deal_id)
    {
        $deal = $this->dealSchecdulerModel->create()
            ->load($deal_id);
        return $deal;
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

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductData()
    {

        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->productStatus->getVisibleStatusIds())
                ->create(),
            $this->filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->productVisibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);
        $productItems = $products->getItems();
        return $productItems;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts()
    {
        $listProductIdDeal = $this->dailydealModel->getListActiveDeal();
//        $dealModel = $this->dailydealModel;
//        foreach ($listProductIdDeal as $productId) {
//            $productModel = $this->productModel->load($productId);
////            $p = $productModel->load($productId);
//            $productName = $productModel->getName();
////            if ($productModel->getTypeId() == 'simple') {
//                $_deal = $dealModel->loadByProductId($productId);
//                $_deal->setData('cur_product', $productModel->getName())
////                    ->setData('product_sku', $productModel->getSku())
//                    ->setData('product_sku', $productModel->getTypeId())
//                    ->setData('product_price', $productModel->getPrice());
////            }
//
//                $_deal->save();
//        }
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();

        if ($this->getRequest()->getParam('product_list_order') == 'name') {
            if ($this->getRequest()->getParam('product_list_dir') == 'desc') {
                $collection->addAttributeToSelect('*')->setOrder('name', 'DESC');
            } else {
                $collection->addAttributeToSelect('*')->setOrder('name', 'ASC');
            }
        }

        if ($this->getRequest()->getParam('product_list_order') == 'deal_price') {
            if ($this->getRequest()->getParam('product_list_dir') == 'desc') {
                $collection->addAttributeToSelect('special_price')->setOrder('special_price', 'DESC');
            } else {
                $collection->addAttributeToSelect('special_price')->setOrder('special_price', 'ASC');
            }
        }

        //get values of current page. if not the param value then it will set to 1
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit. if not the param value then it will set to 1
        $pageSize = ($this->getRequest()->getParam('product_list_limit')) ? $this->getRequest()->getParam('product_list_limit') : 9;

        $collection->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $listProductIdDeal])
            ->getSelect();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
//        $collection->setPageSize(5);
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
//        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
//        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
//            ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

//        foreach ($collection as $item) {
////            \Zend_Debug::dump($item->getPrice());
//            \Zend_Debug::dump($item->getFinalPrice());
//        }
        $collection->getSelect()->assemble();
        $collection->getSelect()->__toString();
        echo $collection->getSelect();
        return $collection;
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        $toolbar->setAvailableOrders(array());  // clear
        $toolbar->addOrderToAvailableOrders('end_date_time', __('Deal Time'));
        $toolbar->addOrderToAvailableOrders('name', __('Name'));
        $toolbar->addOrderToAvailableOrders('deal_price', __('Deal Price'));
        $toolbar->setDefaultDirection('asc');
        $collection = $this->_getProductCollection();
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

    public function aroundGetAvailableOrders(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, \Closure $proceed)
    {
        $returnValue = $proceed();
        unset($returnValue['price']);
        $returnValue['priceDesc'] = 'price - high to low';
        $returnValue['priceAsc'] = 'price - low to high';
        return $returnValue;
    }

    public function aroundSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, \Closure $proceed, $collection)
    {
        $returnValue = $proceed($collection);
        if ($subject->getCurrentOrder() == 'priceHighToLow') {
            $collection->addAttributeToSelect('*')->setOrder('price', 'ASC');
            $collection->load();
        }
        return $collection;
    }
}
