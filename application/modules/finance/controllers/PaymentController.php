<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class Finance_PaymentController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Student Payment (New)";
    	
    	$mode = new Finance_Model_DbTable_Apply(); // applicant
    	
    	//search options
    	$search_ic = $this->_getParam('ic', null);
    	$this->view->search_ic = $search_ic;
    	
    	//process
    	if ($this->getRequest()->isPost()) {
	    	$list = $mode->search($search_ic);
    	}else{
	    	$list = $mode->getData();
    	}
    	
    	$complete = array();
    	$i=0; 
    	foreach ($list as $a){
			$complete[$i]['detail'] = $a;
			$i++;
    	}
    	
    	$this->view->list = $complete;
    	
	}
	
	public function existingAction() {
		
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Student Payment (Existing)";
    	
    	$mode = new Finance_Model_DbTable_Student(); // applicant
    	
    	//search options
    	$search_ic = $this->_getParam('ic', null);
    	$this->view->search_ic = $search_ic;
    	
    	//process
    	if ($this->getRequest()->isPost()) {
	    	$list = $mode->search($search_ic);
    	}else{
	    	$list = $mode->getData();
    	}
    	
    	$complete = array();
    	$i=0; 
    	foreach ($list as $a){
			$complete[$i]['detail'] = $a;
			$i++;
    	}
    	
    	$this->view->list = $complete;
    	
	}
	
	public function planAction() {
		
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Finance";
    	
    	$mode = new Finance_Model_DbTable_Paymentmode();
		$mode = $mode->getPaginateData("fr_payment_plan","id");
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mode));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function viewNewAction() {
		
		$this->_helper->layout->setLayout('default');
		
    	$this->view->title="Student Payment (New)";
    	
    	$plan = $this->_getParam('plan_id', 0);
        $this->view->plan = $this->_getParam('plan_id', 0);
        
        $planDB = new Finance_Model_DbTable_Payment();
        if(isset($planDB)){
        	
			$planList = $planDB->getFeeData("*, SUM( fr_amount ) AS fr_amount","fr_promotion_component","fr_charging_main","fr_charge_period = fr_charging_main.id","fr_component_group","fr_comp_group = fr_component_group.id","fr_component","fr_comp_desc = fr_component.id","semester","fr_semid = semesterID","fr_prom_id = $plan","fr_comp_group"); 
			
        	$this->view->planList = $planList;
        }
    	
        $id = $this->_getParam('id', 0);
        $this->view->id = $id;
    	

	}
	
	public function addAction(){
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Add Payment Plan ";
    	
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