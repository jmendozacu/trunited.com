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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Region
 */
class Magestore_Storepickup_Block_Adminhtml_Region extends Mage_Core_Block_Template
{
    /**
     * @return null
     */
    public function getStore()
	{
		$collection = null;
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
			$collection = Mage::getModel('storepickup/store')->load($id);
		}
		return $collection;
	}
}