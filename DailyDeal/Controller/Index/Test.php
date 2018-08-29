<?php

namespace MW\DailyDeal\Controller\Index;

class Test extends \Magento\Framework\App\Action\Action
{
    /**
     * Execute action.
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $model = $objectManager->create('\MW\DailyDeal\Model\DailydealFactory');
        $collection = $objectManager->create('\MW\DailyDeal\Model\ResourceModel\Dailydeal\Collection');
        $collection1 = $objectManager->create('\MW\DailyDeal\Model\ResourceModel\Dealscheduler\Collection');
        $model = $objectManager->create('\MW\DailyDeal\Model\Dealschedulerproduct');
        \Zend_Debug::dump(count($collection));
        \Zend_Debug::dump($collection->getFirstItem()->debug());
        \Zend_Debug::dump($collection1->getFirstItem()->debug());
        \Zend_Debug::dump($model->load(1)->getData());
        \Zend_Debug::dump($model->load(2)->getData());
        \Zend_Debug::dump("KKKKKKKKKKKKKKKSSSSSSSSS");
        die('asdasdasd');
    }
}
