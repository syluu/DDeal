<?php

namespace MW\DailyDeal\Controller\Index;

class Comming extends \MW\DailyDeal\Controller\Index
{
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
        $resultPage->getConfig()->getTitle()->set(__('Comming Deal'));

        return $resultPage;
    }
}
