<?php

class Application_EducationLevelController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Education Level Setup";
    	
    	//paginator
		$marketDB = new App_Model_Application_DbTable_EducationLevel();
		$level = $marketDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($level));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Education Level";
    	
    	$form = new Application_Form_EducationLevel();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$master = new App_Model_Application_DbTable_EducationLevel();
				$master->add($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'education-level', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Education Level";
    	
    	$form = new Application_Form_EducationLevel();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	$level = new App_Model_Application_DbTable_EducationLevel(); 
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$level->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'education-level', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($level->getProgram($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$level = new App_Model_Application_DbTable_EducationLevel();
    		$level->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'education-level', 'action'=>'index'),'default',true));
    	
    }

}


