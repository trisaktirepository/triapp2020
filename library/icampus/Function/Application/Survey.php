 
<?php 
class icampus_Function_Application_Survey extends Zend_View_Helper_Abstract{
	
	public function dispacthSurvey(){
		$user = Zend_Auth::getInstance();
		 
		$role=$user->getIdentity()->role;
 
		$dbSurvey =new Servqual_Model_DbTable_Survey();
		$action=Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		if ($role=='student' || $role=='Student') {
			$registration_id = $user->getIdentity()->registration_id;
  			if ($registration_id!='') {
  			 // echo $dbSurvey->isAnyOpenSurvey($registration_id);exit;
				if ( !($action=='feedback'||$action=='logout') && $dbSurvey->isAnyOpenSurvey($registration_id) && ($role=='Student'||$role=='student')) {
					///$survey=new Servqual_SurveyController();
					 
					$dbSurvey->dispatcher($registration_id,'student');
				}
			}
		}
	}
	
	
}
 
?>