<?php

class Magestore_RewardPoints_Block_Adminhtml_Transaction_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importtransaction_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('truwallet')->__('Import Transaction'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('rewardpoints')->__('Import Transaction'),
          'title'     => Mage::helper('rewardpoints')->__('Import Transaction'),
          'content'   => $this->getLayout()->createBlock('rewardpoints/adminhtml_transaction_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}