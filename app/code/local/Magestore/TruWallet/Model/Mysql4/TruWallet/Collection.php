<?php

class Magestore_TruWallet_Model_Mysql4_TruWallet_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('truwallet/truwallet');
	}
}