<?php
namespace MW\DailyDeal\Model;

class Dailydeal extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = '1';
    const STATUS_EXPIRE_FALSE = '1';
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
//    public function __construct(
//        \Magento\Framework\Model\Context $context,
//        \Magento\Framework\Registry $registry,
//        \Magento\Framework\Model\ResourceModel\AbstractResource $resource,
//        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection,
//        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
//        array $data = []
//    ) {
//        parent::__construct(
//            $context,
//            $registry,
//            $resource,
//            $resourceCollection,
//            $data
//        );
//        $this->pricingHelper = $pricingHelper;
//    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MW\DailyDeal\Model\ResourceModel\Dailydeal');
        $this->setIdFieldName('dailydeal_id');
    }

    /**
     * true : allow show deal, false : not show deal
     * @return boolean
     */
    public function checkDealQtyToShow($model_product)
    {
        $return = false;
        if ($model_product->getData('type_id') == 'configurable' ||
            $model_product->getData('type_id') == 'grouped' ||
            $model_product->getData('type_id') == 'bundle') {
            // alow buy
            $return = false;
        }

        if ($model_product->getData('type_id') == 'simple' ||
            $model_product->getData('type_id') == 'virtual' ||
            $model_product->getData('type_id') == 'downloadable') {
            $dealqty = $this->getData('deal_qty');
            $soldqty = $this->getData('sold_qty');

            if ($dealqty == 0) {
                $return = true;
            }

            if ($dealqty - $soldqty > 0) {
                // alow buy
                $return = true;
            }
        }

        return true; //$return;
    }

    public function processValueDiscountSaveBought($param = array())
    {

        if (isset($param['model_product'])) {
            $model_product = $param['model_product'];
        } else {
            // load by product_id
            $model_product = $param['model_product'];
        }

        $product_price = round($model_product['price'], 2);
        $deal_price = $this->getData('dailydeal_price');
        $you_save = $product_price - $this->getData('dailydeal_price');
        $one_percent_price = $product_price / 100;
        $percent = round(($product_price - $deal_price ) / $one_percent_price, 2);

        if ($this->getId()) {
            $data = array();
            $data['discount'] = $percent . '%';
            $data['you_save'] = $this->pricingHelper->currency($you_save, true, false);
            $data['bought'] = $this->getData('sold_qty');
            $this->setData('value_discount_save_bought', $data);
        }
    }

    /**
     * get deal running
     * @param int $product_id
     * @return $this
     */
    public function loadByProductId($product_id = null)
    {
        $now = date('Y-m-d H:i:s', time());

        $collection_deal = $this->getCollection()
            ->addFieldToFilter('product_id', $product_id)
//            ->addFieldToFilter('status', self::STATUS_ENABLED)
//            ->addFieldToFilter('expire', self::STATUS_EXPIRE_FALSE)
//            ->addFieldToFilter('store_view', array(array('like' => '%' . $this->getStore()->getId() . '%'), array('like' => '0')))
//            ->addFieldToFilter('start_date_time', array('to' => $now))
//            ->addFieldToFilter('end_date_time', array('from' => $now))
            ->load();

        if (count($collection_deal->getItems())) {
            $this->setData($collection_deal->getFirstItem()->getData());
        }

        return $this;
    }

    public function checkDealQty($model_product, $model_deal)
    {
        $return = false;
//        if ($model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE ||
//            $model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_GROUPED ||
//            $model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
//            // alow buy
//            $return = true;
//        }
//
//        if ($model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE ||
//            $model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL ||
//            $model_product->getData('type_id') == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
//
//            $dealqty = $model_deal->getData('deal_qty');
//            $soldqty = $model_deal->getData('sold_qty');
//
//            if($dealqty == 0){
//                $return = true;
//            }
//
//            if ($dealqty - $soldqty > 0) {
//                // alow buy
//                $return = true;
//            }
//        }
        $return = true;
        return $return;
    }

    /**
     * true : allow view price, false : not view price
     * @return boolean
     */
    public function checkDealPrice($model_product)
    {
        $return = true;
//        if ($model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_GROUPED ||
//            $model_product->getData('type_id') == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
//            // not alow view price
//            $return = false;
//        }

        return $return;
    }

    public function getListActiveDeal()
    {
        $now = date('Y-m-d H:i:s', time());

        $collection_deal = $this->getCollection()
            ->addFieldToFilter('status', self::STATUS_ENABLED)
//            ->addFieldToFilter('expire', self::STATUS_EXPIRE_FALSE)
//            ->addFieldToFilter('store_view', array(array('like' => '%' . $this->getStore()->getId() . '%'), array('like' => '0')))
//            ->addFieldToFilter('start_date_time', array('to' => $now))
//            ->addFieldToFilter('end_date_time', array('from' => $now))
            ->load();
        $listProductId = [];
        foreach ($collection_deal as $deal) {
            $listProductId[] += $deal->getProductId();
        }

        return $listProductId;
    }

    public function getListPastDeal()
    {
        $now = date('Y-m-d H:i:s', time());

        $collection_deal = $this->getCollection()
            ->addFieldToFilter('status', self::STATUS_ENABLED)
//            ->addFieldToFilter('expire', self::STATUS_EXPIRE_FALSE)
//            ->addFieldToFilter('store_view', array(array('like' => '%' . $this->getStore()->getId() . '%'), array('like' => '0')))
//            ->addFieldToFilter('start_date_time', array('to' => $now))
//            ->addFieldToFilter('end_date_time', array('from' => $now))
            ->load();
        $listProductId = [];
        foreach ($collection_deal as $deal) {
            $listProductId[] += $deal->getProductId();
        }

        return $listProductId;
    }

    /**
     * true : allow buy, false : not buy
     * @param $model_product
     * @param $model_deal
     * @param $buy_qty
     * @return boolean
     */
    public function checkSoldQty($model_product, $model_deal, $buy_qty)
    {
        $return = false;
        if ($model_product->getData('type_id') == \MW\DailyDeal\Model\Status::TYPE_CONFIGURABLE ||
            $model_product->getData('type_id') == \MW\DailyDeal\Model\Status::TYPE_GROUPED ||
            $model_product->getData('type_id') == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            // alow buy
            $return = true;
        }

        if ($model_product->getData('type_id') == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE ||
            $model_product->getData('type_id') == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL ||
            $model_product->getData('type_id') == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            $dealqty = $model_deal->getData('deal_qty');
            $soldqty = $model_deal->getData('sold_qty');

            if ($dealqty == 0) {
                $return = true;
            }

            if ($buy_qty <= ($dealqty - $soldqty)) {
                // alow buy
                $return = true;
            }
        }

        return $return;
    }
}
