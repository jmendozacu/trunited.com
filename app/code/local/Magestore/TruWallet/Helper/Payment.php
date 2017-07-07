<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_TruWallet_Helper_Payment
 */
class Magestore_TruWallet_Helper_Payment extends Mage_Payment_Helper_Data
{

    /**
     * @param null $store
     * @param null $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $res = array();
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            if ($quote && $quote->getGrandTotal() == 0) {
                if ($code == 'free') {
                    $prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
                    if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                        continue;
                    }
                    $methodInstance = Mage::getModel($model);
                    if (!$methodInstance) {
                        continue;
                    }
                    $methodInstance->setStore($store);
                    if (!$methodInstance->isAvailable($quote)) {
                        /* if the payment method cannot be used at this time */
                        continue;
                    }
                    $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
                    $methodInstance->setSortOrder($sortOrder);
                    $res[] = $methodInstance;
                }
            } else {
                $prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
                if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                    continue;
                }
                $methodInstance = Mage::getModel($model);
                if (!$methodInstance) {
                    continue;
                }
                $methodInstance->setStore($store);
                if (!$methodInstance->isAvailable($quote)) {
                    /* if the payment method cannot be used at this time */
                    continue;
                }
                $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
                $methodInstance->setSortOrder($sortOrder);
                $res[] = $methodInstance;
            }
        }

        usort($res, array($this, '_sortMethods'));
        return $res;
    }

    public function getActivPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array(array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select')));

        foreach ($payments as $paymentCode => $paymentModel) {
            $is_active = Mage::getStoreConfig('payment/'. $paymentCode .'/active');
            if($is_active){
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode,
                );
            }
        }

        return $methods;

    }

    public function calculatePoints($order)
    {
        $grandTotal = $order->getGrandTotal();
        $amount = Mage::helper('truwallet')->getTruWalletOrderAmount();
        $points = Mage::helper('truwallet')->getTruWalletPaymentPoint();
        
        $reward = 0;
        if($amount > 0 && $grandTotal >= $amount){
            $reward += ($grandTotal / $amount) * $points;
        }

        return $reward;
    }

}
