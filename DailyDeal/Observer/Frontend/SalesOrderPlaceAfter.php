<?php
namespace MW\DailyDeal\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerModelSession;

class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var CustomerModelSession
     */
    protected $customerSession;
    protected $helperConfig;
    protected $dailydealModel;
    protected $productDealModel;
    protected $orderDealModel;

    /**
     * Initialize dependencies.
     *
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param \MW\DailyDeal\Helper\Config $helperConfig
     * @param \MW\DailyDeal\Model\Dailydeal $dailydealModel
     * @param \MW\DailyDeal\Model\ProductDeal $productDealModel
     * @param \MW\DailyDeal\Model\ProductDeal $orderDealModel
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        \MW\DailyDeal\Helper\Config $helperConfig,
        \MW\DailyDeal\Model\Dailydeal  $dailydealModel,
        \MW\DailyDeal\Model\ProductDeal  $productDealModel,
        \MW\DailyDeal\Model\Order  $orderDealModel,
        CustomerModelSession $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->helperConfig = $helperConfig;
        $this->dailydealModel = $dailydealModel;
        $this->productDealModel = $productDealModel;
        $this->orderDealModel = $orderDealModel;
        $this->customerSession = $customerSession;
    }

    /**
     * Ddd option gift.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return SalesOrderPlaceAfter|void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperConfig->isEnabled()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllVisibleItems();
        $order_id = $order->getData('entity_id');
        foreach ($items as $item) {
            $productid = $item->getProductId();
            if ($productid) {
                $deal = $this->dailydealModel->loadByProductId($productid);
                if ($deal->getId()) {
                    $sold_qty = $item->getData('qty_ordered') + $deal->getData('sold_qty');
                    $deal->setSoldQty($sold_qty)
                        ->insertOrderId($order_id)->save();

                    // Action : disable product after place order success, update deal success
                    $this->productDealModel->disableProductByDealId(array($deal->getId()));

                    $info_buyRequest = $this->orderDealModel->markProductOfDeal($item, $deal);
                    $item->setProductOptions($info_buyRequest);
                }
            }
        }

        return $this;
    }
}
