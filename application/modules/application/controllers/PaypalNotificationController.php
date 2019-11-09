<?php
ini_set('log_errors', true); 
class Application_PaypalNotificationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$primaryPaypal = 'suliana@meteor.com.my';
    	
    	

//    	if ($this->getRequest()->isPost()) {
//			$formData = $this->getRequest()->getPost();
			
//    	$this->_itemName = $postValues['item_name'];
//		$this->_itemNumber = $postValues['item_number'];
//		$this->_paymentStatus = $postValues['payment_status'];
//		$this->_paymentAmount = $postValues['mc_gross'];
//		$this->_paymentCurrency = $postValues['mc_currency'];
//		$this->_txnId = $postValues['txn_id'];
//		$this->_receiverEmail = $postValues['receiver_email'];
//		$this->_payerEmail = $postValues['payer_email'];
		
			$data = array(
					'item_name'=>'TBE',
					'mc_gross'=>'0.01',
					'item_number'=>'1',
					'mc_currency'=>'MYR',
					'receiver_email'=>'suliana@meteor.com.my',
					'payer_email'=>'sulieyana@oum.edu.my',
					'txn_id'=>'',
					'payment_status'=>'1'
					
			);
			
			
			$ipn = new App_Model_Application_DbTable_PaypalNotification($data, $primaryPaypal);
			
			
			
	    	if ($ipn->validate()) {
	
				if ($ipn->isCompleted()) {
	
					// Check if already processed
					$paypalMapper = new Application_Model_PayPalMapper();
	
					// Check reciever email
					if (! $ipn->isInMyPocket())
						throw new Exception( 'Receiver email is not mine!' );
	
					// Does the payment amount cover the invoice cost?
					if (! $ipn->isCorrectAmount($correctAmount))
						throw new Exception('Incorrect amount paid!');
	
					// Process payment
	
				}
				elseif ($ipn->isReversed()) {
					// TODO Ban user!
				}
			}
			else {
				throw new Exception( 'IPN didnt validate' );
			}
    	//}

    }


}
?>