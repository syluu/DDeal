<?php
namespace MW\DailyDeal\Block;

use Magento\Store\Model\ScopeInterface;

class ProductPage extends \Magento\Framework\View\Element\Template
{
    protected $checkoutSession;
    protected $sessionManager;
    protected $dailydealModel;
    /**
     * @var \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory
     */
    protected $dailydealCollectionFactory;

    protected $registry;

    protected $helperConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \MW\DailyDeal\Model\Dailydeal $dailydealModel,
        \MW\DailyDeal\Model\ResourceModel\Dailydeal\CollectionFactory $dailydealCollectionFactory,
        \Magento\Framework\Registry $registry,
        \MW\DailyDeal\Helper\Config $helperConfig,
        array $data = []
    ) {

        $this->checkoutSession = $checkoutSession;
        $this->dailydealModel = $dailydealModel;
        $this->dailydealCollectionFactory = $dailydealCollectionFactory;
        $this->registry = $registry;
        $this->helperConfig = $helperConfig;
        parent::__construct($context, $data);
    }

    public function getDeals()
    {
        $deal = $this->dailydealCollectionFactory->create()->setValidationFilter(1)->getFirstItem();
        return $deal;
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function getProductDetailLabel()
    {
        //TODO
    }

    public function getTimeChange()
    {
        //TODO
    }

    public function getConfigTodayDealShowDetail()
    {
        return $this->helperConfig->getConfigTodayDealShowDetail();
    }

    public function getConfigDisplayQuantity()
    {
        return $this->helperConfig->getConfigDisplayQuantity();
    }

    public function renderDealQtyOnProductPage()
    {
        //TODO
    }
}
