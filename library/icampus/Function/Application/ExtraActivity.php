 
<?php 
class icampus_Function_Application_ExtraActivity extends Zend_View_Helper_Abstract{
	
	public function dispacthExtraActivity(){
		$user = Zend_Auth::getInstance();
		 
		$role=$user->getIdentity()->role;
 
		$dbExtra =new App_Model_Record_DbTable_ConfirmationPamira();
		$action=Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		if ($role=='student' || $role=='Student') {
			$registration_id = $user->getIdentity()->registration_id;
  			if ($registration_id!='') {
  			 // echo $dbSurvey->isAnyOpenSurvey($registration_id);exit;
				if ( !($action=='feedback'||$action=='logout') && $dbSurvey->isAnyOpenSurvey($registration_id) && ($role=='Student'||$role=='student')) {
					///$survey=new Servqual_SurveyController();
					 
					$dbExtra->dispatcher($registration_id,'student');
				}
			}
		}
	}
	
	
}
 
?>