<?php

namespace MW\DailyDeal\Model;

class ProductDeal extends \Magento\Framework\Model\AbstractModel
{
    protected $dailyDealModel;
    protected $productRepository;

    /**
     * @param \MW\DailyDeal\Model\Dailydeal $dailyDealModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \MW\DailyDeal\Model\Dailydeal $dailyDealModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dailyDealModel = $dailyDealModel;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * If deal is expire and disable_product equal 1 -> disable product
     * @param array $deal_ids
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function disableProductByDealId($deal_ids = array())
    {
        $model_deal = $this->dailyDealModel;

        foreach ($deal_ids as $id) {
            $model_deal->setData(array());
            $model_deal->load($id);

            if ($model_deal->getId()) {

                if ($model_deal->getStatusTime() == \MW\Dailydeal\Model\Status::STATUS_TIME_ENDED) {

                    if ($model_deal->getData('disable_product_after_finish') == \MW\Dailydeal\Model\Status::STATUS_PRODUCT_ENABLED) {
                        $product = $this->productRepository->getById($model_deal->getProductId());
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $this->productRepository->save($product);
//                        $product->get($model_deal->getData('product_id'), 0, \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $model_deal->setData('disable_product_after_finish', \MW\Dailydeal\Model\Status::STATUS_PRODUCT_DONE);
                        $model_deal->save();
                    }
                }
            }
        }
    }

    /**
     * If deal is expire and disable_product equal 1 -> disable product
     * @param array $deal_ids
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function enableProductByDealId($deal_ids = array())
    {
        $model_deal = $this->dailyDealModel;

        foreach ($deal_ids as $id) {
            $model_deal->setData(array());
            $model_deal->load($id);

            if ($model_deal->getId()) {
                if ($model_deal->getData('disable_product_after_finish') == \MW\Dailydeal\Model\Status::STATUS_PRODUCT_DONE) {
                    $product = $this->productRepository->getById($model_deal->getProductId());
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                    $this->productRepository->save($product);
                    $model_deal->setData('disable_product_after_finish', \MW\Dailydeal\Model\Status::STATUS_PRODUCT_ENABLED);
                    $model_deal->save();
                }
            }
        }
    }

    public function getMinPriceProductGrouped($product)
    {
        if ($product->getTypeId() != \Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions::PRODUCT_TYPE_GROUPED) {
            return '';
        }
        $aProductIds = $product->getTypeInstance()->getChildrenIds($product->getId());
        $prices = array();

        foreach ($aProductIds as $ids) {
            foreach ($ids as $id) {
                $aProduct = $this->productRepository->getById($id);
                $prices[] = $aProduct->getPriceModel()->getPrice($aProduct);
            }
        }

        sort($prices);
        $min_price = array_shift($prices);
        return $min_price;
    }

    public function getMinPriceProductBundle($product)
    {
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return '';
        }
//        // \Magento\Catalog\Model\Product::getTypeInstance()
//        $optionCol = $product->getTypeInstance()
//            ->getOptionsCollection($product);
//        $selectionCol = $product->getTypeInstance()
//            ->getSelectionsCollection(
//                $product->getTypeInstance()->getOptionsIds($product), $product
//            );
//        $optionCol->appendSelections($selectionCol);
//        $price = $product->getPrice();
//
//        foreach ($optionCol as $option) {
//
//            if ($option->required) {
//                $selections = $option->getSelections();
//
//
//                $temp_price = array();
//                foreach ($selections as $selection) {
//                    $temp_price[] = $selection->getPrice();
//                }
//
//                $minPrice = min($temp_price);
//
//
//                if ($product->getSpecialPrice() > 0) {
//                    $minPrice *= $product->getSpecialPrice() / 100;
//                }
//
//                $price += round($minPrice, 2);
//            }
//        }

        $bundleObj = $product->getPriceInfo()->getPrice('final_price');
        return $bundleObj->getMinimalPrice();
    }
}
