<?php
namespace MW\DailyDeal\Controller;

use Magento\Framework\Controller\ResultFactory;

abstract class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */

    protected $helperConfig;

    /**
     * Action constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MW\DailyDeal\Helper\Config $helperConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->helperConfig = $helperConfig;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function getResultRedirectNoroute()
    {
        /* @var \Magento\Framework\Controller\Result\Redirect $resultLayout */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('cms/noroute');

        return $resultRedirect;
    }

}
