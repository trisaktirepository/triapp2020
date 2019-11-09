 
<?php 
class icampus_Plugin_Survey extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
	 
    	$fn=new icampus_Function_Application_Survey();
    	 
    	$fn->dispacthSurvey(); 
       //	if ($user->getIdentity()->hasIdentity) {
           
	}
	
}
 ?>