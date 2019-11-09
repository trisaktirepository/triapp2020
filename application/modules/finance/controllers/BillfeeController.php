<?php
/**
 * @author Ain
 */

require_once 'Zend/Controller/Action.php';

class Finance_BillfeeController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Fee Structure Setup";
    	
    	//paginator
		$billfee = new Finance_Model_DbTable_Billfee();
		$billfee = $billfee->getPaginatePromotion();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($billfee));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function planAction(){
		
    	$this->view->title="Payment Plan";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
//    	if ($this->getRequest()->isPost()) {
//			
//			$formData = $this->getRequest()->getPost();
//
//			$billfee = new Finance_Model_DbTable_Billfee();
//			$id = $billfee->addPlan("fr_plan_new",$formData);
//			
//			//$this->_redirect('/finance/billfee/plan/id/'.$id);		
//					
//        }       
    }
    
	public function addAction()
    {
    	//title
    	$this->view->title="Add Fee";
    	
    	$form = new Finance_Form_Promotion();
    	
    }
	
	public function addFeeAction()
    {
    	$this->view->title="Add Fee Structure Component";
    	
    	$form = new Finance_Form_Promotion();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();

				$program = new Finance_Model_DbTable_Billfee();
				$id = $program->addFee($formData);
				
				$this->_redirect('/finance/billfee/add-fee/id/'.$id);		
					
        }       
        $id = $this->_getParam('id', 0);
		$this->view->id = $id; 
		$this->view->chargeID = $this->_getParam('charge', 0);
        
        
    }
    
    public function addCompAction()
    {
    	$request = $this->getRequest();
    	$this->view->title="Add Fee";
    	
    	$charge = $request->getParam("txtcharge");
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();

				//process form 
				$program = new Finance_Model_DbTable_Billcompfee();
				$id = $program->addComp($formData);
				
				//redirect
					$this->_redirect('/finance/billfee/add-fee/id/'.$id.'/charge/'.$charge);		
					
        	
        }       
        $id = $this->_getParam('id', 0);
		$this->view->id = $id; 
      
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Promotion";
    	
    	$form = new Finance_Form_Promotion();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$program = new Finance_Model_DbTable_Billcompfee();
				$program->updateProgram($formData,$id);
				
				$this->_redirect('/finance/billfee/add-fee/id/'.$id);		
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$program = new Finance_Model_DbTable_Billcompfee();
    			$form->populate($program->getProgram($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id_comp = $this->_getParam('id_comp', 0);
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$program = new Finance_Model_DbTable_Billfee();
    		$program->deleteData("fr_promotion_component",$id_comp);
    	}
    		
    	$this->_redirect('/finance/billfee/add-fee/id/'.$id);		
    	
    }
	
	public function ajaxGetProgramAction($id=null){
    	$id = $this->_getParam('id', 0);
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	// $programDB = new Setup_Model_DbTable_Placementtest();
		// $program_data = $programDB->getProgramMaster($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($program_data);
		
		$this->view->json = $json;

    }
}


