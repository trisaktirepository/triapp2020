<?php
class Application_PaypalController extends Zend_Controller_Action {

	/* Initialize action controller here */
    public function init()
    {
        
    }
  
    public function indexAction(){
    	$this->view->title="Paypal";
    	
    	
    }
public function checkoutAction(){
    	$this->view->title="Paypal";
    	
    	
    }
    
public function paymentcompleteAction(){
    	$this->view->title="Paypal";
    	
    	
    }
   
    
    
}
?>