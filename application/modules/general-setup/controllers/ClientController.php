<?php
/**
 * Setup_ClientController
 * 
 * @author Muhamad Alif
 * @version 
 */

class GeneralSetup_ClientController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function typeAction() {
		//title
    	$this->view->title="Client Type Setup";
    	
    	//paginator
		$clientTypeDB = new App_Model_General_DbTable_ClientType();
		$clientType = $clientTypeDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($clientType));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addTypeAction()
    {
    	//title
    	$this->view->title="Add Client Type";
    	
    	$form = new GeneralSetup_Form_ClientType();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$faculty = new App_Model_General_DbTable_ClientType();
				$faculty->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'client', 'action'=>'type'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editTypeAction(){
		//title
    	$this->view->title="Edit Client Type";
    	
    	$form = new GeneralSetup_Form_Faculty();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$type = new App_Model_General_DbTable_ClientType(); 
				$type->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'client', 'action'=>'type'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$type = new App_Model_General_DbTable_ClientType();
    			$form->populate($type->getData($id));
    		}
    		
    	}
    }
    
	public function deleteTypeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new App_Model_General_DbTable_ClientType();
    		$type->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'client', 'action'=>'type'),'default',true));
    	
    }
    
}

