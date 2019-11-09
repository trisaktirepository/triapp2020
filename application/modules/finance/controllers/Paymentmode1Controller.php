<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class Finance_Paymentmode1Controller extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('main');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("f001_paymentmode","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function addAction(){
		$this->_helper->layout->setLayout('main');
		//title
    	$this->view->title="Add Payment Mode";
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new Finance_Model_DbTable_Paymentmode();
				$id = $IndexDbTable->addData($formData);
				
			$this->_redirect('/finance/paymentmode/');	
        }
    	
        $id = $this->_getParam('id', 0);
        $this->view->id = $id;
	}
	
	public function editAction(){
		
    	//title
    	$this->view->title="Payment Mode";
    	    	
    	$form = new Onapp_Form_Apply();
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
				
				$IndexDbTable = new Finance_Model_DbTable_Paymentmode();
				$IndexDbTable->updateData($formData,$id);
				
				$this->_redirect('/finance/paymentmode/');	
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$IndexDbTable = new Finance_Model_DbTable_Paymentmode();
    		$IndexDbTable->deleteData($id);
    	}
    		
    	$this->_redirect('/finance/paymentmode/');
    	
    }
	
}