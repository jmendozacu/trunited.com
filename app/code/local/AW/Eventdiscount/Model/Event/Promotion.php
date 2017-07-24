<?php

class AW_Eventdiscount_Model_Event_Promotion extends AW_Eventdiscount_Model_Event
{
    public function controllerActionPredispatch($event)
    {
        $newEvent= new Varien_Object();

        if(Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
                ->addFieldToSelect('id')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('trigger_event', AW_Eventdiscount_Model_Event::PROMOTION)
                ->getFirstItem()
                ;

            if(isset($collection) && $collection->getId())
            {

            } else {
                $newEvent->setData('customer', Mage::getSingleton('customer/session')->getCustomer());
                $newEvent->setData('store_id', Mage::app()->getStore()->getId());
                $newEvent->setData('event_type', AW_Eventdiscount_Model_Event::PROMOTION);
                $newEvent->setData('quote', new Varien_Object());
                $this->collectTimersByEvent($newEvent);
                $this->filterByTrigger($newEvent);
                Mage::dispatchEvent('aweventdiscount_event_promotion', $newEvent->toArray());
                $this->activateTriggers($newEvent);
            }
        }

        $controller = $event['controller_action'];
        $request = $controller->getRequest();
        $config = Mage::helper('affiliateplus/config');
        $param = $config->getGeneralConfig('personal_param');
        $accountCode = $request->getParam($param);

        $expiredTime = $config->getGeneralConfig('expired_time');
        Mage::helper('eventdiscount')->saveCookie($accountCode, $expiredTime, false);

    }
}