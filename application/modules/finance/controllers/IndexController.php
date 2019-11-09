<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class Finance_IndexController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('main');
		//title
    	$this->view->title="Finance";
    	$this->view->nat="1";
    	$this->view->lang="english";

	}
	
	public function editAction(){
		
    	//title
    	$this->view->title="Additional Info";
    	    	
    	$form = new Onapp_Form_Apply();
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
				
				$applyDbTable = new Finance_Model_DbTable_Payment();
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
	
}