<?php
namespace MW\DailyDeal\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;

class Config
{
    const XML_PATH_ENABLED = 'mw_dailydeal/group_general/active';
    const XML_PATH_SHOW_TODAY_DEAL_BLOCK = 'mw_dailydeal/group_general/sidebardeal';
    const XML_PATH_SHOW_WEEK_DEAL_BLOCK = 'mw_dailydeal/group_general/calendar';
    const XML_PATH_SHOW_ACTIVE_DEAL_BLOCK = 'mw_dailydeal/group_general/sidebaractive';
    const XML_PATH_DAILY_PAGE_LAYOUT = 'mw_dailydeal/group_general/deallayout';
    const XML_PATH_NUMBER_PRODUCT_PER_ROW = 'mw_dailydeal/group_general/columncount';

    const XML_PATH_TODAY_DEAL_SHOW_DETAIL = 'mw_dailydeal/deal_display/today_deal_show_detail';
    const XML_PATH_SHOW_QTY = 'mw_dailydeal/deal_display/showqty';
    const XML_PATH_IMAGE_CATALOG_LIST = 'mw_dailydeal/deal_display/catalog_list_show_image';

    const XML_PATH_DEAL_QTY_ON_CATALOG_PAGE = 'mw_dailydeal/titles_messages/deal_qty_on_catalog_page';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Escaper $escaper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->storeId = $store;
        return $this;
    }

    /**
     * Check if Minimum Advertised Price is enabled
     *
     * @return bool
     * @api
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    public function getConfigTodayDealShowDetail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TODAY_DEAL_SHOW_DETAIL,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    public function getConfigDisplayQuantity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_QTY,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    public function getConfigIsShowImageCatalogList()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_CATALOG_LIST,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    public function getConfigDealQtyOnCatalogPage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEAL_QTY_ON_CATALOG_PAGE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

}
