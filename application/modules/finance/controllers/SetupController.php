<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class Finance_SetupController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_paymentmode","fr_paymodeid");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function cardtypeAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_cardtype","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function billtypegroupAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_component_group","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function billtypeAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_component","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function discmainAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_discount_main","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function bankAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_banktype","fr_bankid");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function chargingAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_charging_main","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));

		
		$this->view->paginator = $paginator;

	}
	public function paymentPlanAction() {
		
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Payment Plan Setup";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_payment_plan","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function paymentMethodAction() {
		
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Payment Method Setup";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_plan_method","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}

	
	
	public function addAction(){
		$this->_helper->layout->setLayout('default');
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