<?php

namespace MW\DailyDeal\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerModelSession;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Model\Cart as CustomerCart;

class ProcessApply implements ObserverInterface
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
     * Apply catalog rules to product on frontend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperConfig->isEnabled()) {
            return $this;
        }

        $this->_processRulePrice($observer);
        return $this;
    }

    /**
     * Process price for free product.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function _processRulePrice(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $item \Magento\Quote\Model\Quote\Item */
        $item = $observer->getEvent()->getQuoteItem();
        if ($item->getParentItem()) {
            $item = $item->getParentItem();
        }
        $deal = $this->dailydealModel->loadByProductId($item->getProductId());
        if ($deal->getId()) {
            $item->setCustomPrice($deal->getDailydealPrice());
            $item->setOriginalCustomPrice($deal->getDailydealPrice());
            $item->getProduct()->setIsSuperMode(true);
        }
        return $this;
    }
}
