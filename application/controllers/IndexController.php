<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	//echo 'ok';exit;
    }

    public function indexAction()
    {
    	 //Controller ini tidak terpakai-------tested
    	//echo 'ok';exit;
        $auth = Zend_Auth::getInstance();
       
        if($auth->hasIdentity()){
        	
        	if($auth->getIdentity()->role == 'agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				
				//$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'dispatcher','id'=>$auth->getIdentity()->registration_id,'type'=>'student'),'default',true));
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
				
        	//$this->_redirect( $this->view->url(array('module'=>'general-setup','controller'=>'university', 'action'=>'index'),'default',true) );
        }else{
        	 
        	$this->_helper->layout->disableLayout();
	    	$loginURL = $this->view->url(array('module'=>'default','controller'=>'authentication', 'action'=>'login'),'default',true);
	   		//echo 'here';exit;
	    	$form = new App_Form_Login(); 
	    	$form->setAction($loginURL);
	    	
	    	$this->view->form = $form;
        }
    }
    
    public function applicantloginAction()
    {
        
    	
        $auth = Zend_Auth::getInstance();
        
        if($auth->hasIdentity()){
        	$this->_redirect( $this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true) );
        }else{        	
        	$this->_helper->layout->disableLayout();
	    	$loginURL = $this->view->url(array('module'=>'default','controller'=>'authentication', 'action'=>'applicantlogin'),'default',true);
	    	
	    	$form = new App_Form_Login(); 
	    	$form->setAction($loginURL);
	    	
	    	$this->view->form = $form;
        }
    }
    


}

