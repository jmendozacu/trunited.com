<?php

class Magestore_TruGiftCard_Block_Adminhtml_Transaction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'trugiftcard';
		$this->_controller = 'adminhtml_transaction';
		
//		$this->_updateButton('save', 'label', Mage::helper('trugiftcard')->__('Save Transaction'));
//		$this->_updateButton('delete', 'label', Mage::helper('trugiftcard')->__('Delete Transaction'));
//
//		$this->_addButton('saveandcontinue', array(
//			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
//			'onclick'	=> 'saveAndContinueEdit()',
//			'class'		=> 'save',
//		), -100);

		$this->_removeButton('save');
		$this->_removeButton('delete');
		$this->_removeButton('reset');

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('trugiftcard_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'trugiftcard_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'trugiftcard_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('transaction_data') && Mage::registry('transaction_data')->getId())
			return Mage::helper('trugiftcard')->__("Edit Transaction '%s'", $this->htmlEscape(Mage::registry('transaction_data')->getTitle()));
		return Mage::helper('trugiftcard')->__('Add Transaction');
	}
}