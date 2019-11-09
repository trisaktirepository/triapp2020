<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_HighschoolController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$this->_DbObj = new GeneralSetup_Model_DbTable_SchoolDisciplineProgramme();
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("High School Set-up");
    	
    	$this->view->searchForm = new GeneralSetup_Form_SchoolMaster();
    	
    	$schoolMasterDb = new GeneralSetup_Model_DbTable_HighSchool();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			//paginator
			$data = $schoolMasterDb->getPaginateData($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
		
    	}else{
    		//paginator
			$data = $schoolMasterDb->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;	
    	}
    	
	}
	
	public function detailAction(){
    	$this->view->title= $this->view->translate("High School Set-up")." - ".$this->view->translate("Detail");
    	
		$id = $this->_getParam('id', null);
		$this->view->id = $id;
		
    	if($id){  		
    		//school data
    		$schoolMasterDb = new GeneralSetup_Model_DbTable_HighSchool();
    		$this->view->school = $schoolMasterDb->getData($id);
    					
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool', 'action'=>'index'),'default',true));
		}
    }
	
	public function addAction()
    {
    	//title
    	$this->view->title= $this->view->translate("High School Set-up")." - ".$this->view->translate("Add");
    	    	
    	$form = new GeneralSetup_Form_SchoolMaster();
    	
    	$form->cancel->onClick = "window.location ='".$this->view->url(array('module'=>'general-setup','controller'=>'highschool', 'action'=>'index'),'default',true)."'; return false;";
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				
				$schoolMasterDb = new GeneralSetup_Model_DbTable_HighSchool();
				$id = $schoolMasterDb->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool', 'action'=>'detail', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function editAction(){
		$id = $this->_getParam('id', 0);
		
		//title
    	$this->view->title= $this->view->translate("High School Set-up")." - ".$this->view->translate("Edit");
    	
    	$form = new GeneralSetup_Form_SchoolMaster();
    	$schoolMasterDb = new GeneralSetup_Model_DbTable_HighSchool();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$schoolMasterDb->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool', 'action'=>'detail', 'id'=>$id),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			
    			$form->populate($schoolMasterDb->getData($id));
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$schoolMasterDb = new GeneralSetup_Model_DbTable_HighSchool();
    		$data = $schoolMasterDb->getData($id);
    		
    		$schoolMasterDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool', 'action'=>'index'),'default',true));
    	
    }
}

