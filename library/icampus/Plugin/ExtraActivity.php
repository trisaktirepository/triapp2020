 
<?php 
class icampus_Plugin_ExtraActivity extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
	 
    	$fn=new icampus_Function_Application_ExtraActivity();
    	 
    	$fn->dispacthExtraActivity(); 
       //	if ($user->getIdentity()->hasIdentity) {
           
	}
	
}
 ?>