<?php

class Application_EntryRequirementController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
		$this->view->title="Entry Requirement Setup";
    	
    	//paginator
		$entryDB = new App_Model_Application_DbTable_EntryRequirement();
		$level = $entryDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($level));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    	
	}
	
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Entry Requirement";
    	
    	$form = new Application_Form_EntryRequirement();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$master = new App_Model_Application_DbTable_EntryRequirement();
				$master->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Entry Requirement";
    	
    	$form = new Application_Form_EntryRequirement();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	$data = new App_Model_Application_DbTable_EntryRequirement();
    	$entry_data = $data->getData($id);
    	
    	$program_id = $entry_data["id_program"];
    	
    	$this->view->program_id = $program_id;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$data->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($data->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$market = new App_Model_Application_DbTable_EntryRequirement();
    		$market->deleteData($id);
    	}
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'index'),'default',true));
    }
    
    public function viewAction(){
		//title
    	$this->view->title="Entry Requirement Detail";
    	
    	$id = $this->_getParam('id', 0);
    	
    	$data = new App_Model_Application_DbTable_EntryRequirement();
    	$entry_data = $data->getData($id);
    	
    	$program_id = $entry_data["id_program"];
    	
    	$this->view->id = $id;
    	$this->view->program_id = $program_id;
    	
    	//program info
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program_data = $programDB->getData($program_id);
    	$this->view->program = $program_data;
    	
    	//requirement data
    	$program_requirementDB = new App_Model_Record_DbTable_ProgramRequirement();
    	$program_requirement = $program_requirementDB->getCourseRequirement($program_id);
    	$this->view->program_requirement = $program_requirement;
    	
    	//entry requirement detail
    	$entry_requirement_detailDB = new App_Model_Application_DbTable_Requirement();
    	$prog_req_detail = $entry_requirement_detailDB->getEntryRequirement($program_id);
    	$this->view->prog_req_detail = $prog_req_detail;
	}
	
	public function adddetailAction()
    {
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	//title
    	$this->view->title="Add Requirement Detail";
    	
    	$id = $this->_getParam('id', 0);
		$program_id = $this->_getParam('programID', 0);
		$entryID = $this->_getParam('entryID', 0);
    	
    	$form = new Application_Form_Requirement(array('programID'=>$program_id));
    	
		if ($this->getRequest()->isPost() && $program_id!=0) {
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				$requirement = new App_Model_Application_DbTable_Requirement();
				$requirement->addRequirement($formData);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'view','id'=>$entryID),'default',true)."#tabs-1");		
			}
        }
        
        	$this->view->form = $form;
            
    }
    
	public function editdetailAction(){
		if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
		$programrequirementDetailDB = new App_Model_Application_DbTable_Requirement();
		
		//title
    	$this->view->title="Edit Course Type Requirement";
    	
    	$id = $this->_getParam('id', 0);
		$program_id = $this->_getParam('programID', 0);
		$entryID = $this->_getParam('entryID', 0);
    	
		$form = new Application_Form_Requirement(array('programID'=>$program_id));
    	
		if ($this->getRequest()->isPost()) {
    		$formData = $_POST;
			if ($form->isValid($formData)) {
				
				//process form
				$programrequirementDetailDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'view','id'=>$entryID),'default',true)."#tabs-1");		
			}else{
				$form->populate($formData);
			}
    	}else{
    		if($id>0){
    			$form->populate($programrequirementDetailDB->getData($id));
    		}
    	}
    	$this->view->form = $form;
	}
	
    
    
    public function deletedetailAction($id = null){
    	
    	$id = $this->_getParam('id', 0);
		$program_id = $this->_getParam('programID', 0);
		$entryID = $this->_getParam('entryID', 0);
    	
    	if($id>0){
    		$market = new App_Model_Application_DbTable_Requirement();
    		$market->deleteData($id);
    	}
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'view','id'=>$entryID),'default',true)."#tabs-2");		
    }
    
    
	

}


