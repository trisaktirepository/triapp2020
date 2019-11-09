<?php

class Application_PlacementTestComponentController extends Zend_Controller_Action {
	
	private $_placementTestComponentDb;
	
	public function init(){
		$this->_placementTestComponentDb = new App_Model_Application_DbTable_PlacementTestComponent();
	}
	
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Component - Setup";
    	
    	//form
    	$form = new Application_Form_PlacementTestComponent();
    	
    	if ( $this->getRequest()->isPost() ){
    		
			$searchSql = $this->_placementTestComponentDb->searchPaginate( $this->_request->getPost () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    	
	    	//paginator
			$dataList = $this->_placementTestComponentDb->getPaginateData();
			
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
    	$this->view->title="Placement Test Component - Add";
    	
    	$form = new Application_Form_PlacementTestComponent();
    	$form->cancel->onClick = "window.location = '".$this->view->url(array('module'=>'application','controller'=>'placement-test-component', 'action'=>'index'),'default',true)."'; return false;";
    	
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			if ($form->isValid($formData)) {
				
				$auth = Zend_Auth::getInstance();
				
				
				//process form
				$formData['ac_update_by'] = $auth->getIdentity()->id;
				$formData['ac_update_date'] = date('d/m/y h:ia');
				$formData['ac_status'] = 1;
				
				$this->_placementTestComponentDb->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-component', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Placement Test Component - Edit";
    	
    	$form = new Application_Form_PlacementTestComponent(); 
    	$form->cancel->onClick = "window.location = '".$this->view->url(array('module'=>'application','controller'=>'placement-test-component', 'action'=>'index'),'default',true)."'; return false;";   	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				$auth = Zend_Auth::getInstance(); 
	    		$formData['ac_update_by'] = $auth->getIdentity()->id;
				$formData['ac_update_date'] = date('d/m/y h:ia');
				
				$this->_placementTestComponentDb->updateData($formData,$id);
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-component', 'action'=>'index'),'default',true));
				
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($this->_placementTestComponentDb->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction(){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$auth = Zend_Auth::getInstance(); 
    		$formData['ac_update_by'] = $auth->getIdentity()->id;
			$formData['ac_update_date'] = date('d/m/y h:ia');
			$formData['ac_status'] = 0;
			
				
    		$this->_placementTestComponentDb->deleteData($formData,$id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-component', 'action'=>'index'),'default',true));
    	
    }
    
	public function detailAction() {
		//title
    	$this->view->title="Placement Test Type - Detail";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$data = $this->_placementTestComponentDb->getData($id);
    	$this->view->data = $data;
		
	}
	
}