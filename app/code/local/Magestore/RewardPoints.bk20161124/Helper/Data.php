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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'rewardpoints/general/enable';
    
    /**
     * check reward points system is enabled
     * 
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }
    public function isEnableOutput(){
        return Mage::helper('core')->isModuleOutputEnabled('Magestore_RewardPoints');
    }
    public function isEnablePolicy($store = null){
        return Mage::getStoreConfig('rewardpoints/general/show_policy_menu', $store);
    }
    public function getPolicyLink($store = null){
        if(!$this->isEnablePolicy()) return null;
        return Mage::getUrl('rewardpoints/index/policy');
    }
    
    /**
     * get rewards points label to show on Account Navigation
     * 
     * @return string
     */
    public function getMyRewardsLabel()
    {
        $pointAmount = Mage::helper('rewardpoints/customer')->getBalance();

        $imageHtml = Mage::helper('rewardpoints/point')->getImageHtml(false);
        return $this->__('My Rewards') . ' ' . $imageHtml;
    }
    
}
