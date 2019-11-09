<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class onapp_qualificationitemController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		//$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Qualification Item";
    	$this->view->nat="1";
    	$this->view->lang="english";
    	
    	$qualification = new Onapp_Model_DbTable_Qualificationitem();
		$qualification = $qualification->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($qualification));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction(){
		//title
    	$this->view->title="Add Qualification Item";
    	
    	$form = new Onapp_Form_Apply();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Qualificationitem();
				$id = $IndexDbTable->addData($formData);
				
				$this->_redirect('/onapp/qualificationitem/');	
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
				
				$this->_redirect('/onapp/qualificationitem/id/'.$id);	
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$familyDbTable = new Onapp_Model_DbTable_Qualificationitem();
    		$familyDbTable->deleteData($id);
    	}
    		
    	$this->_redirect('/onapp/qualificationitem/');
    	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
	
}