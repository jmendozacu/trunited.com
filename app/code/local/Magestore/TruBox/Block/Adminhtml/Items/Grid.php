<?php

class Magestore_TruBox_Block_Adminhtml_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('truboxGrid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $truBox_table = Mage::getSingleton('core/resource')->getTableName('trubox/trubox');
        $productsTableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
        $customerTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity');
        $customerVarcharTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar');

        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();
        $prodNameAttrId = Mage::getModel('eav/entity_attribute')
            ->loadByCode($entityTypeId, 'name')
            ->getAttributeId();

        $collection = Mage::getModel('trubox/item')->getCollection();
        $collection->getSelect()
            ->joinLeft(
                array("tb" => $truBox_table),
                "main_table.trubox_id = tb.trubox_id",
                array("customer_id" => "tb.customer_id")
            );

        $collection->getSelect()->joinLeft(
            array('cpev' => $productsTableName),
            'cpev.entity_id=main_table.product_id AND cpev.attribute_id=' . $prodNameAttrId . '',
            array('product_name' => 'value')
        );

        $collection->getSelect()
            ->join(array('ce1' => $customerTableName), 'ce1.entity_id=tb.customer_id', array('customer_email' => 'email'));

        $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $collection->getSelect()
            ->join(array('ce2' => $customerVarcharTableName), 'ce2.entity_id=tb.customer_id', array('firstname' => 'value'))
            ->where('ce2.attribute_id=' . $fn->getAttributeId())
            ->join(array('ce3' => $customerVarcharTableName), 'ce3.entity_id=tb.customer_id', array('lastname' => 'value'))
            ->where('ce3.attribute_id=' . $ln->getAttributeId())
            ->columns(new Zend_Db_Expr("CONCAT(`ce2`.`value`, ' ',`ce3`.`value`) AS customer_name"));


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('item_id', array(
            'header' => Mage::helper('trubox')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'item_id',
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('trubox')->__('Customer ID'),
            'index' => 'customer_id',
//            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Customer_Name',
            'filter_name' => 'customer_id',
//            'filter_condition_callback' => array($this, '_filterCustomerNameCallback')
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('trubox')->__('Customer Name'),
            'index' => 'customer_name',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Customer_Name',
            'filter_name' => 'customer_name',
            'filter_condition_callback' => array($this, '_filterCustomerNameCallback')
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('Sales')->__('Customer Email'),
            'index' => 'customer_email',
            'type' => 'text',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Customer_Email',
            'filter_condition_callback' => array($this, '_filterCustomerEmailCallback')
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('trubox')->__('Product Name'),
            'index' => 'product_name',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Items_Name',
            'filter_condition_callback' => array($this, '_filterProductNameCallback')
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('trubox')->__('Price'),
            'width' => '200px',
            'type' => 'price',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'index' => 'price',
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('trubox')->__('Product Qty'),
            'width' => '50px',
            'index' => 'qty',
            'type' => 'number',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('trubox')->__('Created At'),
            'align'     =>'right',
            'index'     => 'created_at',
            'type'		=> 'date'
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('trubox')->__('Updated At'),
            'align'     =>'right',
            'index'     => 'updated_at',
            'type'		=> 'date'
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('trubox')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('trubox')->__('View Details'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('trubox')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('trubox')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('trubox');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('trubox')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('trubox')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('order', array(
            'label' => Mage::helper('trubox')->__('Make orders'),
            'url' => $this->getUrl('*/*/massOrder')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _filterCustomerNameCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);

        if (!empty($value)) {
            $_customers = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('firstname')
                ->addAttributeToFilter('firstname', array('like' => '%' . $value . '%'))
                ->setOrder('firstname', $dir);

            $rs = $_customers->getColumnValues('entity_id');

            if (sizeof($rs) == 0)
                $collection->getSelect()->where('tb.customer_id IS NULL');
            else
                $collection->getSelect()->where('tb.customer_id IN (' . implode(',', $rs) . ')');
        }


        return $this;
    }

    protected function _filterCustomerEmailCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        if (!empty($value)) {
            $_customers = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('email')
                ->addAttributeToFilter('email', array('like' => '%' . $value . '%'));

            $rs = array();
            if (sizeof($_customers) > 0) {
                foreach ($_customers as $_customer) {
                    $rs[] = $_customer->getId();
                }
            }
            if (sizeof($rs) == 0)
                $collection->getSelect()->where('tb.customer_id IS NULL');
            else
                $collection->getSelect()->where('tb.customer_id IN (' . implode(',', $rs) . ')');
        }


        return $this;
    }

    protected function _filterProductNameCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        if (!empty($value)) {
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter('name', array('like' => '%' . $value . '%'));

            $rs = array();
            if (sizeof($products) > 0) {
                foreach ($products as $product) {
                    $rs[] = $product->getId();
                }
            }
            if (sizeof($rs) == 0)
                $collection->getSelect()->where('main_table.product_id IS NULL');
            else
                $collection->getSelect()->where('main_table.product_id IN (' . implode(',', $rs) . ')');
        }


        return $this;
    }

    public function _filterProductPriceCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $values = $column->getFilter()->getValue();
        $from = $values['from'];
        $to = $values['to'];
        return $this;
    }

}
