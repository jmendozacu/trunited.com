<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Adesh
 * @package     Mymodule_Customerpage
 * @author      Adesh
 * @Website     adeshsuryan.in
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
 
class Marketing_Resources_IndexController extends Mage_Core_Controller_Front_Action {
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }    
	
    public function marketingAction()
    {
        $this->loadLayout();
		$head = Mage::app()->getLayout()->getBlock('head');
        $head->addItem('skin_css', 'css/bootstrap/custome-bootstrap.css');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Marketing Resources'));
        $this->renderLayout();
    }
	/*public function marketingAction() {
        // Changed By Adam 28/07/2014
        if(!Mage::helper('customer')->isAffiliateModuleEnabled()) return $this->_redirectUrl(Mage::getBaseUrl());
        
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('customer/account/login');
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Marketing Resources'));
        $this->renderLayout();
    }*/
}  