<?php
namespace MW\DailyDeal\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerModelSession;

class CatalogProductCollectionLoadAfter implements ObserverInterface
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
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        \MW\DailyDeal\Helper\Config $helperConfig,
        \MW\DailyDeal\Model\Dailydeal  $dailydealModel,
        CustomerModelSession $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->helperConfig = $helperConfig;
        $this->dailydealModel = $dailydealModel;
        $this->customerSession = $customerSession;
    }

    /**
     * Ddd option gift.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperConfig->isEnabled()) {
            return;
        }

        $deal = $this->dailydealModel;
        foreach ($observer->getCollection()->getItems() as $product) {
            $deal->setData(array());
            $deal->loadByProductId($product->getId());

            if ($deal->getId()) {
                $this->_setProductPriceAndWatermark($product);
            }
        }
    }

    protected function _setProductPriceAndWatermark($model_product)
    {
        $dailydeal_collection = $this->dailydealModel->getCollection();
        $model_deal = $dailydeal_collection->loadcurrentdeal($model_product->getId());
        if ($model_deal) {
            $flag_qty = $model_deal->checkDealQty($model_product, $model_deal);
            $flag_price = $model_deal->checkDealPrice($model_product);
            if ($flag_qty && $flag_price) {
                $model_product->setSpecialPrice($model_deal->getDailydealPrice());
                $model_product->setFinalPrice($model_deal->getDailydealPrice());
            }
        }
    }
}
