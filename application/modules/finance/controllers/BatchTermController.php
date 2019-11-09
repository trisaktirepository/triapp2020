<?php
class Finance_BatchTermController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Tag Company/Takaful Operator Payment Term";
    	
    	//paginator
		$companyDB = new App_Model_Finance_DbTable_BatchTerm();
		$takafulData = $companyDB->listCompany(2);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($takafulData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		
		$companyData = $companyDB->listCompany(3);
		
		$paginatorComp = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($companyData));
		$paginatorComp->setItemCountPerPage(20);
		$paginatorComp->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginatorCompany = $paginatorComp;
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
	}
	
	public function settermAction()
    {
    	//title
    	$this->view->title="Payment Term";
    	
		$id= $this->_getParam('id', 0);
		$this->view->id = $id;
    	
    	$companyDB = new App_Model_General_DbTable_TakafulOperator();
    	$companyData = $companyDB->getData($id);
    	$this->view->companyData = $companyData;
    	
    	$termDB = new Finance_Model_DbTable_Term();
		$termData = $termDB->getData(); 
		$this->view->term = $termData;
		
		$batchtermDB = new App_Model_Finance_DbTable_BatchTerm();
		$batchtermdata = $batchtermDB->getBatchTerm($id);
		$this->view->batchterm = $batchtermdata;
		
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				//process form 
				
			$date = date('Y-m-d H:i:s');
			
			$auth = Zend_Auth::getInstance();
			$idUpd = $auth->getIdentity()->id;
			
			
			
			try{
				if($batchtermdata){
					$data = array(
						"id_term" => $formData["term"],
						"date" => $date,
						"UpdUser" =>$idUpd
					);
			
					$idBatchterm = $batchtermdata['id'];
					$batchtermDB->updateData($data,$idBatchterm);
				}else{
					$data = array(
						"id_batch" => $id,
						"id_term" => $formData["term"],
						"date" => $date,
						"UpdUser" =>$idUpd
					);
					$batchtermDB->addData($data);
				}
			}catch (Exception $e){
				$this->_helper->flashMessenger->addMessage("Error While Insert");
			}
			
			if($id!=null){
				$this->_helper->flashMessenger->addMessage("Data has been saved");
			}
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'finance','controller'=>'batch-term', 'action'=>'index'),'default',true));		
        	
        }
    }
    
	public function updateAction(){
		//title
    	$this->view->title="Update Payment Term";
    	
    	//redirect
//		$this->_redirect($this->view->url(array('module'=>'finance','controller'=>'individual', 'action'=>'index'),'default',true));		
    	
    }
    
	public function deleteTypeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new Finance_Model_DbTable_Paymentmode();
    		$type->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));
    	
    }
    
}

