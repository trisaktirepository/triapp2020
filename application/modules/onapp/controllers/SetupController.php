<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class onapp_setupController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Online Application";
    	$this->view->nat="1";
    	$this->view->lang="english";

	}
	
	public function addApplyAction(){
		$this->_helper->layout->setLayout('onapp');
		//title
    	$this->view->title="Add Applicant Basic Info";
    	
    	$form = new Onapp_Form_Apply();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Apply();
				$id = $IndexDbTable->addData($formData);
				
				$this->_redirect('/onapp/index/add-apply/id/'.$id);	
        }
    	$this->view->form = $form;
    	$id = $this->_getParam('id', 0);
		$this->view->id = $id; 
       
	}
	
	public function programmeAction(){
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Programme Setup";
    	
    	$programme = new Onapp_Model_DbTable_Setup();
		$programme = $programme->getPaginateData("sc005_course","sc005prog");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($programme));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addDetailAction(){
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Add detail Info";
    	
    	$form = new Onapp_Form_Apply();
    	
		$app_id = $this->_getParam('app_id', 0);
		$this->view->id = $app_id; 
		    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Applyreq();
				$id = $IndexDbTable->addData($formData);
				
				$this->_redirect('/onapp/index/add-detail/id/'.$app_id);	
        	
        }
    	$this->view->form = $form;
    	$id = $this->_getParam('id', 0);
		$this->view->id = $id; 
       
	}
	
	public function addReqAction(){
		$this->_helper->layout->setLayout('public');
		//title
    	$this->view->title="Add Entry Requirement";
    	
    	$form = new Onapp_Form_Apply();
    	
    	$app_id = $this->_getParam('id', 0);
		$this->view->id = $app_id; 
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Applyreq();
				$id = $IndexDbTable->addData($formData);
				
			$this->_redirect('/onapp/index/add-apply/id/'.$app_id);	
        }
    	
        $this->view->form = $form;
        $id = $this->_getParam('id', 0);
        $this->view->id = $id;
	}
	
	public function editAction(){
		
    	//title
    	$this->view->title="Additional Info";
    	    	
    	$form = new Onapp_Form_Apply();
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
				
				$applyDbTable = new Onapp_Model_DbTable_Apply();
				$applyDbTable->updateData($formData,$id);
				
				$this->_redirect('/onapp/index/offer-letter/id/'.$id);	
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$familyDbTable = new Employee_Model_DbTable_Family();
    		$familyDbTable->deleteData($id);
    	}
    		
    	$this->_redirect('/employee/next-of-kin/');
    	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
	
}