<?php

class Magestore_Affiliatepluswidget_Block_Field_Affiliatepluswidget extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * render config row
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        $html  = '<tr id="row_' . $id . '">'
                . '<td class="label" colspan="3"></td>';
        $html .= '<td class="value"><a href="https://docs.google.com/viewer?url=https://www.magestore.com/media/productfile/a/f/affiliateplus_widget_user-guide.pdf" target="_bank">'.$element->getLabel().'</a>';
        $html .= '</td><td style="padding-top:5px;"><strong>('.$this->__('Version: ').Mage::getConfig()->getModuleConfig("Magestore_Affiliateplus")->version.')</strong></td></tr>';
        return $html;
    }
}
