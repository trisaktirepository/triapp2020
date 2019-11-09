<?php
/**
 * Branch Controller for ICampus
 * 
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_BranchController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Branch Setup";
    	
    	//paginator
		$branchDBTable = new App_Model_General_DbTable_Branch();
		$branchData = $branchDBTable->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($branchData));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Branch";
    	
    	$form = new GeneralSetup_Form_Branch();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$branchDBTable = new App_Model_General_DbTable_Branch();
				$branchDBTable->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'branch', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Branch";
    	    	
    	$form = new GeneralSetup_Form_Branch();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$branchDBTable = new App_Model_General_DbTable_Branch(); 
				$branchDBTable->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'branch', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$branchDBTable = new App_Model_General_DbTable_Branch();
    			$form->populate($branchDBTable->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$branchDBTable = new App_Model_General_DbTable_Branch();
    		$branchDBTable->deleteData($id);
    	}
    		
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'branch', 'action'=>'index'),'default',true));
    	
    }

}

