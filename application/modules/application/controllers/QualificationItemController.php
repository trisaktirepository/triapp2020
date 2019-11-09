<?php

class Application_QualificationItemController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Qualification Item Setup";
    	
    	//paginator
		$marketDB = new App_Model_Application_DbTable_QualificationItem();
		$level = $marketDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($level));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Qualification Item";
    	
    	$form = new Application_Form_QualificationItem();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$master = new App_Model_Application_DbTable_QualificationItem();
				$master->add($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'qualification-item', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Qualification Item";
    	
    	$form = new Application_Form_QualificationItem();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	$qualificationItemDB = new App_Model_Application_DbTable_QualificationItem(); 
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$qualificationItemDB->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'qualification-item', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($qualificationItemDB->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$level = new App_Model_Application_DbTable_QualificationItem();
    		$level->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'qualification-item', 'action'=>'index'),'default',true));
    	
    }

}


