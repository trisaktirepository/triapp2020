<?php

class Application_PlacementTestProgramComponentController extends Zend_Controller_Action {
	
	private $_dbObject;
	
	public function init(){
		$this->_dbObject = new App_Model_Application_DbTable_PlacementTestProgramComponent();
	}
	
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Program's Component - Setup";
    	
    	//form
    	$form = new Application_Form_PlacementTestProgramComponent();
    	
    	if ( $this->getRequest()->isPost() ){
    		
			$searchSql = $this->_dbObject->searchPaginateProgram( $this->_request->getPost () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    	
	    	//paginator
			$programList = $this->_dbObject->getPaginateProgram();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($programList));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
    	
    	$this->view->form = $form;
    	
	}
	
	public function detailAction() {
		//title
    	$this->view->title="Placement Test Program's Component - Detail";
    	
    	$program_id = $this->_getParam('program_id', 0);
    	$this->view->program_id = $program_id;
    	
    	//program data
    	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('p'=>'tbl_program'))
				->where('p.IdProgram = '. $program_id);
								
		$row = $db->fetchRow($select);
		if($row){
			$this->view->program = $row;
		}
    	
    	//program's component data
    	$placementTestLocation = $this->_dbObject->getProgramData($program_id);
    	$this->view->component = $placementTestLocation;
    	
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Placement Test Program's Component - Add";
    	
    	$form = new Application_Form_PlacementTestProgramComponent();
    	
    	$program_id = $this->_getParam('program_id', 0);
    	$this->view->program_id = $program_id;
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			if ($form->isValid($formData)) {
				
				//process form
				$auth = Zend_Auth::getInstance();
				$formData['apps_program_id'] = $program_id;
				$formData['apps_create_by'] = $auth->getIdentity()->id;
				$formData['apps_create_date'] = date('d/m/y h:ia');
				
				$this->_dbObject->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-program-component', 'action'=>'detail', 'program_id'=>$program_id),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function deleteAction(){
    	$id = $this->_getParam('id', 0);
    	$data = $this->_dbObject->getData($id);
    	
    	if($id>0){
    		$this->_dbObject->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-program-component', 'action'=>'detail', 'program_id'=>$data['apps_program_id']),'default',true));
    	
    }
}