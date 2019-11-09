<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */


class GeneralSetup_UniversityController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Company Setup";
    	
    	//paginator
		$universityDB = new App_Model_General_DbTable_University();
		$this->view->data = $universityDB->getData(1);
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Company";
    	
    	$form = new Setup_Form_University();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$university = new Setup_Model_DbTable_University();
				$university->addUniversity($formData);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'university', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Company";
    	
    	$form = new GeneralSetup_Form_University();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$university = new App_Model_General_DbTable_University(); 
				$university->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'university', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$university = new App_Model_General_DbTable_University();
    			$form->populate($university->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$university = new Setup_Model_DbTable_University();
    		$university->deleteUniversity($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'university', 'action'=>'index'),'default',true));
    	
    }

}
