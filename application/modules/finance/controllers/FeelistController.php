<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class Finance_FeelistController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Fee Structure List";
    	
    	//paginator
		$billfee = new Finance_Model_DbTable_Fee();
		$billfee = $billfee->getPaginateFee();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($billfee));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
  

      public function viewAction()
    {
   	
		//title
    	$this->view->title="Fee Structure";
 $id = $this->_getParam('id', 0);
 $this->view->id = $id;
 
//    	$this->_helper->layout->setLayout('default');
		$this->_helper->layout->setLayout('fee');
    	$billfee = new Finance_Model_DbTable_Feelist();
		$billfee = $billfee->getPaginateFee($id);
    	
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($billfee));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    	
//    	$this->_redirect('/finance/feelist/view');	
//    	$this->_redirect('/finance/feelist/view/id/'.$id);	
//$this->_redirect('/finance/fee/add/id/'.$id.'/addtn/'.$adid);	
//    	$form = new Setup_Form_Semester();
//
//    	$id = $this->_getParam('id', 0);
//    	$this->view->id = $id;
//    	
//    	$addtn = $this->_getParam('addtn', 0);
//		$this->view->addtn = $addtn; 
//				
//    	if ($this->getRequest()->isPost()) {
//    		$formData = $this->getRequest()->getPost();
//    		
//    		
//    		if($formData['flag']!="Y"){
//    		$selProgram = explode(",", $formData['selProgram']);
//    		
//				$semester_programDB = new Finance_Model_DbTable_Fee();
//				//validate,check for unique prog id.each id shud be assign once.
//				
//				//insert ,assign fee structure to various programs
//				foreach ($selProgram as $prog){
//					$semester_programDB->assignFeeProgramID($formData['txtpromote'],$prog,$formData['txtaddition']);
//				}
//				//redirect
//				$this->_redirect('/finance/fee');
//    		}else{
//    			$adid = $formData['txtaddition'];
//    			$id = $formData['txtpromote'];
//    			$this->_redirect('/finance/fee/add/id/'.$id.'/addtn/'.$adid);	
//				
//    		}	
//    	}
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