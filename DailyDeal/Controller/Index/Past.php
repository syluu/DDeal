<?php

namespace MW\DailyDeal\Controller\Index;

class Past extends \Magento\Framework\App\Action\Action //\MW\DailyDeal\Controller\Index
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
     * Execute action.
     */
    public function execute()
    {
        if (!$this->helperConfig->isEnabled()) {
            return $this->getResultRedirectNoroute();
        }

        return $this->initResultPage();
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function initResultPage()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Past Deal'));

        return $resultPage;
    }
}
