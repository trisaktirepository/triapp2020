<?php 
class icampus_Plugin_Layout extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		 
    	$user = Zend_Auth::getInstance(); 
        $role = $user->getIdentity()->role; 
        $layout = Zend_Layout::getMvcInstance(); 
    	
        switch ($role) { 
        	case 'administrator': 
            	$layout->setLayout('administrator'); 
               	break; 
               	
            case 'agent': 
            	$layout->setLayout('agent'); 
               	break; 
    
        	case 'company': 
            	$layout->setLayout('company'); 
                break;
            case 'parent':
                	$layout->setLayout('parent');
                	break;

            case 'exam': 
            	$layout->setLayout('exam'); 
                break;     
    
            default:
            	$layout->setLayout('applicant'); 
                break; 
        } 
	} 
	
}
?>