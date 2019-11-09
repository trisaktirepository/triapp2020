<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class onapp_credittransferController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Credit Transfer Setup";
    	$this->view->nat="1";
    	$this->view->lang="english";

	}
	
	public function courseaddAction(){
		$this->_helper->layout->setLayout('default');
    	$this->view->title="Academic Landscape";
    	
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
    		
    	$this->_redirect('/onapp/credittransfer/');
    	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
	
}