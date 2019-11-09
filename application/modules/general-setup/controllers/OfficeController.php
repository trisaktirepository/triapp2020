<?php
/**
 * Office Controller for ICampus
 * 
 * @author Muhamad Alif Muhammad
 * @version 1.0
 */

class GeneralSetup_OfficeController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Office Setup";
    	
    	//paginator
		$branchDB = new App_Model_General_DbTable_Branch();
		$data = $branchDB->getBranchArray();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($data));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Office";
    	
    	$form = new GeneralSetup_Form_Office();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$officeDB = new App_Model_General_DbTable_Office();
				$officeDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'office', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Office";
    	    	
    	$form = new GeneralSetup_Form_Office();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$officeDB = new App_Model_General_DbTable_Office(); 
				$officeDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'office', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$officeDB = new App_Model_General_DbTable_Office();
    			$form->populate($officeDB->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$officeDB = new App_Model_General_DbTable_Office();
    		$officeDB->deleteData($id);
    	}
    		
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'office', 'action'=>'index'),'default',true));
    	
    }
    
    public function viewAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
    	$this->view->title="Office Details";
    	
    	$id = $this->_getParam('id', 0);
		$officeDB = new App_Model_General_DbTable_Office();
		
		$this->view->data  = $officeDB->getData($id);
    }

}

