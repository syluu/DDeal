<?php
namespace MW\DailyDeal\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session as CustomerModelSession;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $checkoutSession;
    protected $layoutFactory;
    protected $layout;
    protected $cart;
    /**
     * @var CustomerModelSession
     */
    protected $customerSession;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    protected $helperConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \MW\DailyDeal\Helper\Config $helperConfig,
        CustomerModelSession $customerSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->layoutFactory = $layoutFactory;
        $this->layout = $layout;
        $this->cart = $cart;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->helperConfig = $helperConfig;
        parent::__construct($context);
    }

    public function getStoreConfig($xmlPath)
    {
        return $this->scopeConfig->getValue(
            $xmlPath,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getUrlImageCatalogList()
    {
        if (!$this->scopeConfig->getValue('mw_dailydeal/group_general/active', ScopeInterface::SCOPE_STORE)) {
            return '';
        }

        $short_url_image = $this->helperConfig->getConfigIsShowImageCatalogList();
        $url_image = $this->storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'mw_dailydeal/'.$short_url_image;
        return $url_image;
    }

    public function renderDealQtyOnCatalogPage($qty)
    {
        $value = $this->helperConfig->getConfigDealQtyOnCatalogPage();
        $search = array('{{qty}}');
        $replace = array($qty);
        $html = str_replace($search, $replace, $value);
        echo $html;
    }
}
