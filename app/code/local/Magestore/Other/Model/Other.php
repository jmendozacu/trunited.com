<?php

class Magestore_Other_Model_Other extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('other/other');
	}
}