 
<?php 
class icampus_Function_Studentfinance_Studentfinance extends Zend_View_Helper_Abstract{
	
	public function dispacthStudentfinance(){
		$user = Zend_Auth::getInstance();
		 
		$role=$user->getIdentity()->role;
 
		$dbInvoiceMain =new Studentfinance_Model_DbTable_InvoiceMain();
		$action=Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		if ($role=='student' || $role=='Student') {
			$registration_id = $user->getIdentity()->registration_id;
  			if ($registration_id!='') {
  			 // echo $dbSurvey->isAnyOpenSurvey($registration_id);exit;
  			 	$idactivity=$dbInvoiceMain->isAnyOpenInvoice($registration_id);
  			 	//echo $idactivity;exit;
				if ( !($action=='generate-std-invoice'||$action=='logout') && ($idactivity!=0) && ($role=='Student'||$role=='student')) {
					///$survey=new Servqual_SurveyController();
					 
					$dbInvoiceMain->dispatcher($registration_id,$idactivity);
				}
			}
		}
	}
	
	
}
 
?>