<?php


class Finance_TermController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Payment Term Setup";
    	
    	//paginator
		$clientTypeDB = new Finance_Model_DbTable_Term();
		$clientType = $clientTypeDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($clientType));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
	}
	
	public function addTypeAction()
    {
    	//title
    	$this->view->title="Add Bank";
    	
    	$form = new GeneralSetup_Form_Takaful();
    	
    	$busTypeDB = new App_Model_General_DbTable_BusinessType();
    	$busTypeData = $busTypeDB->getData();
    	$this->view->businesstype = $busTypeData;
    	
    	$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia
		$this->view->state = $stateData;
		
		$securityQuesDB = new App_Model_General_DbTable_SecurityQuestion();
    	$securityQuesData = $securityQuesDB->getData();
    	$this->view->securityquestion = $securityQuesData;
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				//process form 
				$faculty = new Finance_Model_DbTable_Term();
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					"name" => strtoupper($formData["name"]),
					"shortName" => $formData["shortName"],
					"registrationNo" => $formData["registrationNo"],
					"address" => $formData["address"],
					"city" => $formData["city"],
					"postcode" => $formData["postcode"],
					"state" => $formData["state"],
					"country" => $formData["country"],
					"email" => $formData["email"],
					"altEmail" => $formData["altEmail"],
					"contactperson" => $formData["contactperson"],
					"contactoffice" => $formData["contactoffice"],
					"contactno" => $formData["contactno"],
					"fax" => $formData["fax"],
					"idClienttype" => $formData["idClienttype"],
					"idbusinesstype" => $formData["idbusinesstype"],
					"loginid" => $formData["loginid"],
					"password" => md5($formData["password"]),
					"clearpass" => $formData["password"],
					"question" => $formData["question"],
					"answer" => $formData["answer"],
					"hint" => $formData["hint"],
					"updUser" => $idUpd,
					"updDate" => $date
				
				);
				
				try{
					$faculty->addData($data);
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While Insert");
				}
				
				if($id!=null){
					$this->_helper->flashMessenger->addMessage("Data has been saved");
				}
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));		
        	
        }
    }
    
	public function editTypeAction(){
		//title
    	$this->view->title="Edit Bank";
    	
    	$form = new GeneralSetup_Form_Takaful();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	$busTypeDB = new App_Model_General_DbTable_BusinessType();
    	$busTypeData = $busTypeDB->getData();
    	$this->view->businesstype = $busTypeData;
    	
    	$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia
		$this->view->state = $stateData;
		
		$securityQuesDB = new App_Model_General_DbTable_SecurityQuestion();
    	$securityQuesData = $securityQuesDB->getData();
    	$this->view->securityquestion = $securityQuesData;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
				$type = new Finance_Model_DbTable_Term(); 
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					"name" => strtoupper($formData["name"]),
					"shortName" => $formData["shortName"],
					"registrationNo" => $formData["registrationNo"],
					"address" => $formData["address"],
					"city" => $formData["city"],
					"postcode" => $formData["postcode"],
					"state" => $formData["state"],
					"country" => $formData["country"],
					"email" => $formData["email"],
					"altEmail" => $formData["altEmail"],
					"contactperson" => $formData["contactperson"],
					"contactoffice" => $formData["contactoffice"],
					"contactno" => $formData["contactno"],
					"fax" => $formData["fax"],
					"idClienttype" => $formData["idClienttype"],
					"idbusinesstype" => $formData["idbusinesstype"],
					"loginid" => $formData["loginid"],
					"password" => md5($formData["password"]),
					"clearpass" => $formData["password"],
					"question" => $formData["question"],
					"answer" => $formData["answer"],
					"hint" => $formData["hint"],
					"updUser" => $idUpd,
					"updDate" => $date
				
				);
				
				try{
					$type->updateData($data,$id);
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While Updating");
				}
				
				if($id!=null){
					$this->_helper->flashMessenger->addMessage("Data has been updated");
				}
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true)); 
    	}else{
    		if($id>0){
    			$type = new Finance_Model_DbTable_Term();
    			$datatakaful = $type->getData($id);
    			$this->view->data = $datatakaful;
    		}
    		
    	}
    }
    
	public function deleteTypeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new Finance_Model_DbTable_Term();
    		$type->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));
    	
    }
    
}

