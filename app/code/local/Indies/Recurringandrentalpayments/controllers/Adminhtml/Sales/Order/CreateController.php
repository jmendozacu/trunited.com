<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'Sales/Order/CreateController.php';
class Indies_Recurringandrentalpayments_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
	const HASH_SEPARATOR = ":::";
    public function indexAction()
    {
		
        $this->_title($this->__('Sales'))->_title($this->__('Orders'))->_title($this->__('New Order'));
        $this->_initSession();
        $this->loadLayout();

        $this->_setActiveMenu('sales/order')
            ->renderLayout();
    }
	public function saveAction()
    {
        try {
            $this->_processActionData('save');
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

            $order = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))
                ->createOrder();
				
			$quote = $this->_getOrderCreateModel()->getQuote();
			$this->assignSubscriptionToCustomer($quote,$order);
		
			$this->_getSession()->clear();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }
	
	public function assignSubscriptionToCustomer($quote,$order)
	{
		$items = $order->getItemsCollection();
        $paymentMethod = $order->getPayment()->getMethodInstance()->getCode(); 
        $period_date_hashs = array();
        if (!Mage::getSingleton('recurringandrentalpayments/subscription')->getId()) {
            /***
             * Joins set of products to one subscription if this can be done..
             **/
            foreach ($items as $item)
            {	
				$buyInfo = $item->getBuyRequest();
				if (Mage::helper('recurringandrentalpayments')->isSubscriptionType($item)) {
				   
				   	$period_type = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
					$Options =Mage::getModel('recurringandrentalpayments/terms')->load($period_type);
				   	$x = Mage::getModel('recurringandrentalpayments/plans')->load($Options->getPlanId())->getStartDate();
				   	$startdate = now();
					
					if($x==1)
				   	{
						$startdate = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionStart();
					}
					if($x==3)
					{
						$startdate= date('Y-m-01');
						$startdate = new Zend_Date(date('M-01-Y', strtotime("+1 months", strtotime(date("Y-m-d")))));
					}
                   if($x != 2)  // x = Subscription Start Date 
					{
						if (preg_match('/[\d]{4}-[\d]{2}-[\d]{2}/', $startdate))
						{
							$date = new Zend_Date($startdate);
						}
						else{
							$date = new Zend_Date($startdate, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));						
	
						}
						$date_start = $date->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);	

					}
					else   // add this  because when admin set subscription start date as 'Moment of purchase' that time issue is generated.
					{
						// We take magento's date.
						$date_start = Mage::getModel('core/date')->date('Y-m-d');
					}
					
					if ($period_type > 0) {
                        if (!isset($period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start])) {
                            $period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start] = array();
                        }
                        $period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start][] = $item;
                    }
                }
            }
			foreach ($period_date_hashs as $hash => $OrderItems)
            {
                $Options = $this->_getOrderItemOptionValues($item);
				
				$discountamount = 'null';
				$applydiscounton = 0;

				if(Mage::helper('recurringandrentalpayments')->isApplyDiscount())   // (enable/disable)
				{
					$amount = Mage::helper('recurringandrentalpayments')->discountAmount() ;
					$calculation_type = Mage::helper('recurringandrentalpayments')->applyDiscountType();
					if(Mage::helper('recurringandrentalpayments')->discountAvailableTo() == 3 )   // Specific customer group
					{
						$isavailable = Mage::helper('recurringandrentalpayments')->selectedCustomerGroup() ;
						$customer_group = explode(',',Mage::helper('recurringandrentalpayments')->selectedCustomerGroup());
						$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
						if(($isavailable == 1 ) || ($isavailable == 2 ) || (($isavailable == 3) && in_array($groupId,$customer_group)))
						{
							if(Mage::helper('recurringandrentalpayments')->applyDiscountOn()!= 3) 
							{
								if($calculation_type == 1)  //Fixed
										$discountamount = $amount;
								else
									$discountamount = $amount.'%';
								
								$applydiscounton = Mage::helper('recurringandrentalpayments')->applyDiscountOn();
							}
							else
							{
								if($calculation_type == 1)  //Fixed
										$discountamount = $amount;
								else
									$discountamount = $amount.'%';

								$applydiscounton = 3;
							}
						}
					}
					else
					{
						if(Mage::helper('recurringandrentalpayments')->applyDiscountOn()!= 3) 
						{
							if($calculation_type == 1)  //Fixed
								$discountamount = $amount;
							else
								$discountamount = $amount.'%';
							
							$applydiscounton = Mage::helper('recurringandrentalpayments')->applyDiscountOn();
						}
						else
						{
							if($calculation_type == 1)  //Fixed
									$discountamount = $amount;
							else
								$discountamount = $amount.'%';
							$applydiscounton = 3;
						}
					 }					
				}
                list($period_type, $date_start) = explode(self::HASH_SEPARATOR, $hash);
				
			    $Subscription = Mage::getModel('recurringandrentalpayments/subscription')
                        ->setCustomer(Mage::getModel('customer/customer')->load($order->getCustomerId()))
                        ->setPrimaryQuoteId($quote->getId())
                        ->setDateStart($date_start)
                        ->setStatus(Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($order) ? Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED : Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED)
                        ->setTermType($period_type)
                        ->initFromOrderItems($OrderItems, $order)
						->setDiscountAmount($discountamount)
						->setTransactionStatus('Success')
						->setApplyDiscountOn($applydiscounton)
						->save();
						
			
			// Start : Date : 2015-01-06 : Make change for add first sequence as a paid of order when it place	
			    Mage::getModel('recurringandrentalpayments/sequence')
                            ->setSubscriptionId($Subscription->getId())
                            ->setDate($date_start)
							->setOrderId($order->getId())
							->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)
							->setMailsent(1)
                            ->save();  
			// End : Date : 2015-01-06 : Make change for add first sequence as a paid of order when it place 
               
			    // Run payment method trigger
                $Subscription
                        ->getMethodInstance($paymentMethod)
                        ->onSubscriptionCreate($Subscription, $order, $quote);
            
				if(isset($Subscription) && $Subscription)
				{
					$alert= Mage::getModel('recurringandrentalpayments/alert_event');
					if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_NEW) == '1')
					{
						$alert->send($Subscription,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_NEW_TEMPLATE),0);
					}
					if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL) == '1')
					{
						$alert->send($Subscription,
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_TEMPLATE),
									0,
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_SENDER),
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_CC_TO)
									);
					} 	
				}
				else
				{
					$Subscription = '';
					//break;
				}
			}
        }
    	
	}
	protected function _getOrderItemOptionValues(Mage_Sales_Model_Order_Item $item)
    {
        $buyRequest = $item->getProductOptionByCode('info_buyRequest');
        $obj = new Varien_Object;
        $obj->setData($buyRequest);
        return $obj;
    }
	protected function _isAllowed()
    {
      return true; 
    }
}
