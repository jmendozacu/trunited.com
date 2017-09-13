<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_Interest_Block_Adminhtml_Renderer_Customer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $html = '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit", array('id'=>$row->getData('customer_id'))).'">'.$value.'</a>';
        return '<span>' . $html . '</span>';
    }

}
