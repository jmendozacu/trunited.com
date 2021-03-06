<?php

class Magestore_TruWallet_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'truwallet/general/enable';

    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }

    public function isEnableModule(){
        return Mage::helper('core')->isModuleOutputEnabled('Magestore_TruWallet');
    }

    public function getMyTruWalletLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/truwallet/truwallet.png').'" />';
        return $this->__('My truWallet') . ' ' . $image;
    }

    public function getShareTruWalletLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/truwallet/truwallet.png').'" />';
        return $this->__('Share truWallet Money') . ' ' . $image;
    }

    public function formatTruwallet($credit)
    {
        return Mage::helper('core')->currency($credit, true, false);
    }

    public function getSpendConfig($code, $store = null)
    {
        return Mage::getStoreConfig('truwallet/spending/' . $code, $store);
    }

    public function getWarningMessage($store = null)
    {
        return Mage::getStoreConfig('truwallet/general/warning_message', $store);
    }

    public function isEnableTruWalletProduct($store = null)
    {
        return Mage::getStoreConfig('truwallet/product/enable', $store);
    }

    public function getTruWalletOrderStatus($store = null)
    {
        return Mage::getStoreConfig('truwallet/product/order_status', $store);
    }

    public function getTruWalletSku($store = null)
    {
        return Mage::getStoreConfig('truwallet/product/truwallet_sku', $store);
    }

    public function getTruWalletValue($store = null)
    {
        return Mage::getStoreConfig('truwallet/product/truwallet_value', $store);
    }

    public function getEnableChangeBalance($store = null)
    {
        return Mage::getStoreConfig('truwallet/general/enable_change_balance', $store);
    }

    public function getTruWalletPaymentEnable($store = null)
    {
        return Mage::getStoreConfig('truwallet/truwallet_payment/enable', $store);
    }

    public function getTruWalletPayment($store = null)
    {
        return Mage::getStoreConfig('truwallet/truwallet_payment/payment', $store);
    }

    public function getTruWalletOrderAmount($store = null)
    {
        return Mage::getStoreConfig('truwallet/truwallet_payment/order_amount', $store);
    }

    public function getTruWalletPaymentPoint($store = null)
    {
        return Mage::getStoreConfig('truwallet/truwallet_payment/reward_point', $store);
    }

    public function getEnableTransferBonus($store = null)
    {
        return Mage::getStoreConfig('truwallet/transfer/enable', $store);
    }

    public function getTransferBonus($store = null)
    {
        return Mage::getStoreConfig('truwallet/transfer/bonus', $store);
    }

    public function getMessageTransferBonus($store = null)
    {
        return Mage::getStoreConfig('truwallet/transfer/message', $store);
    }

    public function getEnableSharing($store = null)
    {
        return Mage::getStoreConfig('truwallet/sharewallet/enable', $store);
    }


    public function isShowWarningMessage()
    {
        if(Mage::helper('core')->isModuleOutputEnabled('Magestore_TruBox'))
        {
            $truBoxCollection = Mage::helper('trubox')->getCurrentTruBoxCollection();
            if(sizeof($truBoxCollection) <= 0)
                return false;

            $totalPrice = 0;
            foreach ($truBoxCollection as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $option_params = json_decode($item->getOptionParams(), true);
                $price_options = 0;

                if($product->getTypeId() != 'configurable')
                {
                    foreach ($product->getOptions() as $o)
                    {
                        $values = $o->getValues();
                        $_attribute_value = 0;

                        foreach($option_params as $k=>$v)
                        {
                            if($k == $o->getOptionId())
                            {
                                $_attribute_value = $v;
                                break;
                            }
                        }
                        if($_attribute_value > 0)
                        {
                            foreach ($values as $val) {
                                if(is_array($_attribute_value)){
                                    if(in_array($val->getOptionTypeId(), $_attribute_value)) {
                                        echo $val->getTitle().' ';
                                        $price_options += $val->getPrice();

                                    }
                                } else if($val->getOptionTypeId() == $_attribute_value){
                                    echo $val->getTitle().' ';
                                    $price_options += $val->getPrice();
                                }
                            }
                        }
                    }
                }

                $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                $totalPrice += $itemPrice;
            }

            if($totalPrice == 0)
                return false;

            $current_truWallet_balance = Mage::helper('truwallet/account')->getTruWalletCredit(false);
            if($current_truWallet_balance == null)
                return false;

            if($current_truWallet_balance < $totalPrice)
                return true;
            else
                return false;
        } else {
            return false;
        }


    }


    public function synchronizeCredit()
    {
        $reward_customers = Mage::getModel('rewardpoints/customer')->getCollection()
            ->setOrder('reward_id','desc')
        ;

        if(count($reward_customers) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_customers as $reward)
                {
                    $truWallet_customer = Mage::getModel('truwallet/customer');
                    $truWallet_customer->setData('customer_id', $reward->getCustomerId());
                    $truWallet_customer->setData('truwallet_credit', $reward->getProductCredit());
                    $truWallet_customer->setData('created_time', now());
                    $truWallet_customer->setData('updated_time', now());
                    $transactionSave->addObject($truWallet_customer);
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }

    }

    public function synchronizeTransaction()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',array('in'=>array(0,3,4,5)))
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        switch($transaction->getActionType())
                        {
                            case 0:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_BY_ADMIN;
                                break;

                            case 3:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER;
                                break;

                            case 4:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING;
                                break;

                            case 5:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING;
                                break;
                        }

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }

    }

    public function synchFeb()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SPEND)
            ->addFieldToFilter('action',array('like'=>'%spending_order%'))
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_CHECKOUT_ORDER;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }
    }

    public function synchGiftCard()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', 8)
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }
    }

    public function synchTransfer()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',3)
            ->addFieldToFilter('customer_id',array('gt' => 0))
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }
    }

     public function synchPurchaseGiftCard()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',8)
            ->addFieldToFilter('customer_id',array('gt' => 0))
            ->addFieldToFilter('transaction_id',array('in' => array(8902,9245,10648)))
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $this->__('Purchased truWallet Gift Card on order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$transaction->getOrderId())).'">'.$transaction->getOrderIncrementId().'</a>');
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }
    }

    public function synchOrder()
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at',array(
                'from' => '2017-02-09',
                'to'    => '2017-02-11'
            ))
            ->setOrder('entity_id','desc')
            ;

        $data_order = array();
        foreach($orders as $order)
        {
            $items = $order->getAllItems();
            foreach($items as $item)
            {
                if(strcasecmp($item->getSku(),'TWGIFTCARD') == 0)
                {
                    $data_order[] = array(
                        'order' => $order,
                        'qty' => $item->getQtyOrdered(),
                    );
                }
            }
        }
//        zend_debug::dump($data_order);
//        exit;

        if(sizeof($data_order) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($data_order as $order)
                {
                    $do = $order['order'];
                    $qty = $order['qty'];
                    $truwallet_account = Mage::helper('truwallet/account')->loadByCustomerId($do->getCustomerId());
                    $customer = Mage::getModel('customer/customer')->load($do->getCustomerId());
                    if($do->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $do->getCustomerId();
                        $_data['customer_email'] = $customer->getEmail();
                        $_data['title'] = $this->__('Purchased truWallet Gift Card on order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$do->getEntityId())).'">'.$do->getIncrementId().'</a>');
                        $_data['store_id'] = $do->getStoreId();

                        $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $do->getCreatedAt();
                        $_data['updated_time'] = $do->getUpdatedAt();
                        $_data['expiration_date'] = '';
                        $_data['order_id'] = $do->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $qty * $this->getTruWalletValue();
                        $_data['receiver_email'] = '';
                        $_data['receiver_customer_id'] = '';
                        
                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        }

        /*$reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', 8)
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }*/
    }
	
}
