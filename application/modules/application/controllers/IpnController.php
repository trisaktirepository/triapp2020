<?php
//error_reporting (E_ALL ^ E_WARNING);
//error_reporting (E_ALL ^ E_NOTICE);
class Application_IpnController extends Zend_Controller_Action 
{   

	public function indexAction() {
		$this->_helper->layout->disableLayout();
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');		
		$listener = new App_Model_Application_DbTable_Ipn();		
		$listener->use_sandbox = true;
		
		try {
    		$listener->requirePostMethod();
    		$verified = $listener->processIpn();
		} catch (Exception $e) {
	    	error_log($e->getMessage());
//	    	exit(0);
		}

		if ($verified) {
	    	// TODO: Implement additional fraud checks and MySQL storage
	 		/* 		mysql_connect('localhost', 'root', 'admin') or exit(0);
	    	mysql_select_db('paypal') or exit(0);*/
	     	$txn_id = $_POST['txn_id'];
	      	$payer_email = $_POST['payer_email'];
	      	$item_name = $_POST['item_name'];
	      	$item_number = $_POST['item_number'];
	    	$mc_gross = $_POST['mc_gross'];
	    	$this->view->mess = "";	
			
	    	//$resultlistener=$listener->inserttopaypalorders($txn_id,$payer_email,$mc_gross,$item_name,$item_number);	   insert to database 	
	    	
//	    	$items = explode("-",$item_name);
//	    	$item_name= $items[0];
//	    	
//	    		$lintidstudent =  $items[1];
//				$larrresult = $this->lobjstudentmodel->fnviewstudentdetailssss($lintidstudent);		
						
//				$postArray = $this->_request->getPost ();
//	    		if($postArray){								
//					if($postArray['payment_status'] = 'Completed'){						
//						$postArray['UpdUser']= 1;//$auth->getIdentity()->iduser;
//						$postArray['UpdDate']= date ( 'Y-m-d:H-i-s' );					
//						$this->lobjstudentmodel->fnInsertPaypaldetails($postArray,$lintidstudent);						
//					}								
//				}
//	
//    		mail('ibfiminfo@gmail.com', 'Valid IPN', $listener->getTextReport()); 
    		mail('suliana@meteor.com.my', 'Valid IPN', $listener->getTextReport());     	
		} else {   	// manually investigate the invalid IPN
//			mail('ibfiminfo@gmail.com', 'Invalid IPN', $listener->getTextReport());
	    	mail('suliana@meteor.com.my', 'Invalid IPN', $listener->getTextReport());
		}
	}
}