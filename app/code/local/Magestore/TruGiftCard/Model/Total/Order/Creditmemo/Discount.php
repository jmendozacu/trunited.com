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
 * Trugiftcard Model
 * 
 * @category    Magestore
 * @package     Magestore_Trugiftcard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Model_Total_Order_Creditmemo_Discount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getTrugiftcardDiscount() < 0.0001) {
            return;
        }

        $creditmemo->setBaseTrugiftcardDiscount(0);
        $creditmemo->setTrugiftcardDiscount(0);

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;

        $hiddenTaxDiscount = 0;
        $baseHiddenTaxDiscount = 0;

        $totalDiscountRefunded = 0;
        $baseTotalDiscountRefunded = 0;

        $hiddenTaxRefunded = 0;
        $baseHiddenTaxRefunded = 0;

        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getTrugiftcardDiscount()) {
                $baseTotalDiscountRefunded += $existedCreditmemo->getBaseTrugiftcardDiscount();
                $totalDiscountRefunded += $existedCreditmemo->getTrugiftcardDiscount();

                $baseHiddenTaxRefunded += $existedCreditmemo->getBaseTrugiftcardHiddenTax();
                $hiddenTaxRefunded += $existedCreditmemo->getTrugiftcardHiddenTax();
            }
        }

        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmount += $baseShippingAmount * $order->getBaseTrugiftcardDiscountForShipping() / $order->getBaseShippingAmount();
            $totalDiscountAmount += $order->getShippingAmount() * $baseTotalDiscountAmount / $order->getBaseShippingAmount();

            $baseHiddenTaxDiscount += $baseShippingAmount * $order->getBaseTrugiftcardShippingHiddenTax() / $order->getBaseShippingAmount();
            $hiddenTaxDiscount += $order->getShippingAmount() * $baseHiddenTaxDiscount / $order->getBaseShippingAmount();
        }

        if ($this->isLast($creditmemo)) {
            $baseTotalDiscountAmount = $order->getBaseTrugiftcardDiscount() - $baseTotalDiscountRefunded;
//            echo $baseTotalDiscountRefunded;
            $totalDiscountAmount = $order->getTrugiftcardDiscount() - $totalDiscountRefunded;

            $baseHiddenTaxDiscount = $order->getBaseTrugiftcardHiddenTax() - $baseHiddenTaxRefunded;
            $hiddenTaxDiscount = $order->getTrugiftcardHiddenTax() - $hiddenTaxRefunded;
        } else {
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy())
                    continue;

                $orderItemBaseDiscount = (float) $orderItem->getBaseTrugiftcardDiscount();
                $orderItemDiscount = (float) $orderItem->getTrugiftcardDiscount();

                $orderItemBaseHiddenTax = (float) $orderItem->getBaseTrugiftcardHiddenTax();
                $orderItemHiddenTax = (float) $orderItem->getTrugiftcardHiddenTax();

                $orderItemQty = $orderItem->getQtyOrdered();
                $refundItemQty = $item->getQty();
                if ($orderItemDiscount && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $totalDiscountAmount += $creditmemo->roundPrice($refundItemQty * $orderItemBaseDiscount / $orderItemQty, 'regular', true);
                        $baseTotalDiscountAmount += $creditmemo->roundPrice($refundItemQty * $orderItemDiscount / $orderItemQty, 'base', true);

                        $hiddenTaxDiscount += $creditmemo->roundPrice($refundItemQty * $orderItemHiddenTax / $orderItemQty, 'regular', true);
                        $baseHiddenTaxDiscount += $creditmemo->roundPrice($refundItemQty * $orderItemBaseHiddenTax / $orderItemQty, 'base', true);
                    } else {
                        $totalDiscountAmount += $refundItemQty * $orderItemBaseDiscount / $orderItemQty;
                        $baseTotalDiscountAmount += $refundItemQty * $orderItemDiscount / $orderItemQty;

                        $hiddenTaxDiscount += $refundItemQty * $orderItemHiddenTax / $orderItemQty;
                        $baseHiddenTaxDiscount += $refundItemQty * $orderItemBaseHiddenTax / $orderItemQty;
                    }
                }
            }
        }
        $creditmemo->setBaseTrugiftcardDiscount($baseTotalDiscountAmount);
        $creditmemo->setTrugiftcardDiscount($totalDiscountAmount);

        $creditmemo->setBaseTrugiftcardHiddenTax($baseHiddenTaxDiscount);
        $creditmemo->setTrugiftcardHiddenTax($hiddenTaxDiscount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $hiddenTaxDiscount - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseHiddenTaxDiscount - $baseTotalDiscountAmount);

        $creditmemo->setAllowZeroGrandTotal(true);
    }

    /**
     * check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}