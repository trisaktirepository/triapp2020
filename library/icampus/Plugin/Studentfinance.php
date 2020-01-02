 
<?php 
class icampus_Plugin_Studentfinance extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
	 
    	$fn=new icampus_Function_Studentfinance_Studentfinance();
    	 
    	$fn->dispacthStudentfinance(); 
       //	if ($user->getIdentity()->hasIdentity) {
           
	}
	
}
 ?>