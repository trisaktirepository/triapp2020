<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class onapp_indexController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('onapp');
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
    
    	
    	$IndexDbTable = new Onapp_Model_DbTable_Apply();
		$this->programlist = $IndexDbTable->getList("*","sc001_program",1);
		$this->view->programlist = $this->programlist;
		
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
    	
    	$request = $this->getRequest();
    	$prog = $request->getParam("id_apply");
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
				
				$applyDbTable = new Onapp_Model_DbTable_Apply();
				$applyDbTable->updateData($formData,$id);
				
				$placeDB = new Onapp_Model_DbTable_Placementtest();
				$place = $placeDB->getData("placement_test","ID_Prog = ".$prog);
					if (empty($place)) {
						$this->_redirect('/onapp/index/offer-letter/id/'.$id);	
					}
					else {
						$this->_redirect('/onapp/index/placement/id/'.$id);	
					}
				
				
				
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
    }
    
//    public function deleteentryAction($id = null){
//    	$id = $this->_getParam('id', 0);
//    	$this->view->id = $id;
//    	
//    	if($id>0){
//    		$DbTable = new Onapp_Model_DbTable_ApplicantEntry();
//    		$DbTable->deleteData($id);
//    	}
//    		
//    	$this->_redirect('/onapp/index/add-apply/id/'.$id);
//    	
//    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$familyDbTable = new Onapp_Model_DbTable_ApplicantEntry();
    		$familyDbTable->deleteData($id);
    	}
    		
    	$this->_redirect('/onapp/index/add-apply/id/'.$id); 
    	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
    
    public function placementAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
	
}