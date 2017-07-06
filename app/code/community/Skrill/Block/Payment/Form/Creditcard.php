<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *
 * @package     Skrill
 * @copyright   Copyright (c) 2014 Skrill
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Skrill_Block_Payment_Form_Creditcard extends Skrill_Block_Payment_Form_Abstract
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_logoAlt = Mage::helper('skrill')->__('FRONTEND_PM_CC');
        parent::_construct();
    }

    protected function _getLogoHtml()
    {
        if ( !Mage::getStoreConfig('payment/skrill_acc/card_selection') )
        {
            $cards = "AMEX,VISA,MASTER,MAESTRO";
        }
        else
        {
            $cards = explode(',', Mage::getStoreConfig('payment/skrill_acc/card_selection'));
        }

        $html = '';
        foreach ($cards as $key => $value) {
            if ($value == "AMEX" )
                $logo_name = "amex.jpg";
            if ($value == "VISA" )
                $logo_name = "visa.png";
            if ($value == "MASTER" )
                $logo_name = "mastercard.jpg";
            if ($value == "MAESTRO" )
                $logo_name = "maestro.png";

            $html .= "<img src='".$this->getSkinUrl('images/skrill/'.$logo_name.'')."' alt='".$this->_getLogoAlt()."' height='35px' style='margin-right:5px' />";

            if ($value == "VISA" )
            {
                $logo_name = "visa-pay.png";
                $html .= "<img src='".$this->getSkinUrl('images/skrill/'.$logo_name.'')."' alt='".$this->_getLogoAlt()."' height='35px' style='margin-right:5px' />";
            }

        }

        return $html;
    }
}