<?php

class Magestore_Affiliateplusprogram_Model_Mysql4_Account extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct(){
        $this->_init('affiliateplusprogram/account', 'id');
    }
}