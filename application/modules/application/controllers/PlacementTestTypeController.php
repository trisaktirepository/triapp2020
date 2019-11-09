<?php

class Application_PlacementTestTypeController extends Zend_Controller_Action {
	
	private $_placementTestTypeDb;
	
	public function init(){
		$this->_placementTestTypeDb = new App_Model_Application_DbTable_PlacementTestType();
	}
	
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Type - Setup";
    	
    	//form
    	$form = new Application_Form_PlacementTestType();
    	
    	if ( $this->getRequest()->isPost() && $form->isValid($this->_request->getPost ())){
    		
			$searchSql = $this->_placementTestTypeDb->searchPaginate( $form->getValues () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    	
	    	//paginator
			$dataList = $this->_placementTestTypeDb->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataList));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
    	
    	$this->view->form = $form;
    	
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Placement Test Type - Add";
    	
    	$form = new Application_Form_PlacementTestType();
    	$form->cancel->onClick = "window.location = '".$this->view->url(array('module'=>'application','controller'=>'placement-test-type', 'action'=>'index'),'default',true)."'; return false;";
    	
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			if ($form->isValid($formData)) {
				
				//process form
				$this->_placementTestTypeDb->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-type', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Placement Test Type - Edit";
    	
    	$form = new Application_Form_PlacementTestType(); 
    	$form->cancel->onClick = "window.location = '".$this->view->url(array('module'=>'application','controller'=>'placement-test-type', 'action'=>'index'),'default',true)."'; return false;";   	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$this->_placementTestTypeDb->updateData($formData,$id);
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-type', 'action'=>'index'),'default',true));
				
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($this->_placementTestTypeDb->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction(){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$this->_placementTestTypeDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-type', 'action'=>'index'),'default',true));
    	
    }
    
	public function detailAction() {
		//title
    	$this->view->title="Placement Test Type - Detail";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$placementTestType = $this->_placementTestTypeDb->getData($id);
    	$this->view->placementTestType = $placementTestType;
	}
	
}