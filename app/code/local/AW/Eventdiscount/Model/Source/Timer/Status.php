<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Model_Source_Timer_Status
{
    const ENABLED = '1';
    const DISABLED = '0';

    const ENABLED_LABEL = 'Enabled';
    const DISABLED_LABEL = 'Disabled';

    public function toOptionArray()
    {
        $_helper = Mage::helper('eventdiscount');
        return array(
             self::ENABLED=>  $_helper->__(self::ENABLED_LABEL),
             self::DISABLED=>  $_helper->__(self::DISABLED_LABEL)
        );
    }
}