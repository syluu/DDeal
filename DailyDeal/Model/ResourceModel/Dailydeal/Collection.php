<?php
namespace MW\DailyDeal\Model\ResourceModel\Dailydeal;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $eavConfig;
    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|NULL          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|NULL    $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \MW\DailyDeal\Model\Dailydeal::class,
            \MW\DailyDeal\Model\ResourceModel\Dailydeal::class
        );
    }

    public function joinDate()
    {
//        $mainTable = $this->getMainTable();
//        $this->getSelect()->joinLeft(
//            ['scheduler'=>$this->getTable('mw_deal_scheduler')],
//            'mw_dailydeal.deal_scheduler_id = scheduler.deal_scheduler_id',
//            [
//                'mw_dailydeal.*'
//            ]
//        );
        $orderTable = $this->_resource->getTable('mw_deal_scheduler');
        $this->getSelect()
        ->join(
            ['ot'=>$orderTable],
            "main_table.deal_scheduler_id = ot.deal_scheduler_id",
            [
                'deal_data' => 'ot.*'
            ]
        );
        return $this;
    }

    /**
     * Add scheduler to collection
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
//        $this->getSelect()->joinLeft(
//            ['scheduler' => $this->getTable('mw_deal_scheduler')],
//            'main_table.deal_scheduler_id = scheduler.deal_scheduler_id AND scheduler.is_primary = 1',
//            ['*']
//        );
        return $this;
    }

    /**
     * Filter collection by specified website, customer group, coupon code, date.
     * Filter collection to use only active rules.
     * Involved sorting by sort_order column.
     *
     * @param int $dailydeal_id
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return $this
     */
    public function setValidationFilter($dailydeal_id)
    {
//        if (!$this->getFlag('validation_filter')) {
            /* We need to overwrite joinLeft if coupon is applied */
            $this->getSelect()->reset();
            parent::_initSelect();
//        $this->joinDate();
//        $this->getSelect()->joinLeft(
//            ['scheduler' => $this->getTable('mw_deal_scheduler')],
//            'main_table.deal_scheduler_id = scheduler.deal_scheduler_id AND scheduler.is_primary = 1'
//        );
//            $this->addFieldToFilter('status_scheduler', 1);
            $this->addFieldToFilter('main_table.dailydeal_id', $dailydeal_id);

//            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
//            $this->setOrder('main_table.dailydeal_id', self::SORT_ORDER_ASC);
//            $this->setFlag('validation_filter', true);
//        }

        return $this;
    }

    public function loadcurrentdeal($productid = null)
    {
        $deal = null;

        $now = date('Y-m-d H:i:s', time());

        $this   ->addFieldToFilter('product_id', $productid)
//            ->addFieldToFilter('status', 1)
//            ->addFieldToFilter('expire', MW_Dailydeal_Model_Status::STATUS_EXPIRE_FALSE)
//            ->addFieldToFilter('store_view',array(array('like'=>'%'. $this->getStore()->getId() .'%'),array('like'=>'0')))
//            ->addFieldToFilter('start_date_time', array('to' => $now))
//            ->addFieldToFilter('end_date_time', array('from' => $now))
            ->load();

        if (count($this->getItems())) {
            $deal = $this->getFirstItem();
        }

        return $deal;
    }

    /**
     * Filter product status
     * @param $store_id
     * @return Collection
     */
    public function addProductStatusFilter($store_id)
    {
        $store_id_all = 0;
        $code_id = $this->eavConfig->loadByCode('catalog_product', 'status')->getId();

        $prefix = '';

        $this->getSelect()->joinInner(
            array( 'at_status_default' => $prefix .'catalog_product_entity_int'),
            "(main_table.product_id = at_status_default.entity_id) AND (at_status_default.attribute_id = {$code_id}) AND (at_status_default.store_id = {$store_id_all})",
            array('at_status.value')
        );
        $this->getSelect()->joinLeft(
            array( 'at_status' => $prefix . 'catalog_product_entity_int'),
            "(main_table.product_id = at_status.entity_id) AND (at_status.attribute_id = {$code_id}) AND (at_status.store_id = {$store_id})",
            array('at_status.value')
        );

        $this->getSelect()->where(" (IF(at_status.value_id > 0, at_status.value, at_status_default.value) = '1')");

        return $this;
    }
}
