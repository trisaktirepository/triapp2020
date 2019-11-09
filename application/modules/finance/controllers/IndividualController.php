<?php
class Finance_IndividualController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Individual Payment";
    	
    	//paginator
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$registrationData = $registrationDB->getPaginateDataFinance('list',1);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($registrationData));
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		$this->view->page = $this->_getParam('page',1);
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
	}
	
	public function paymentDetailAction()
    {
    	//title
    	$this->view->title="Payment Details (Individual)";
    	
    	$idApp= $this->_getParam('idApp', 0); //id student
		$this->view->idApp = $idApp;
		
		$id= $this->_getParam('id', 0); //id registrationdetails
		$this->view->id = $id;
		
		$studentDB = new App_Model_Record_DbTable_Registrationdetails();
		$studentData = $studentDB->getStudentProfile($id);
		$this->view->candidate = $studentData;
		
		$paymentmodeDB = new Finance_Model_DbTable_Paymentmode();
		$paymentModeData = $paymentmodeDB->getData();
		$this->view->paymentmode = $paymentModeData;
    	
		
    	$form = new GeneralSetup_Form_Takaful();
    	
    	$busTypeDB = new App_Model_General_DbTable_BusinessType();
    	$busTypeData = $busTypeDB->getData();
    	$this->view->businesstype = $busTypeData;
    	
    	$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia only
		$this->view->state = $stateData;
		
		$securityQuesDB = new App_Model_General_DbTable_SecurityQuestion();
    	$securityQuesData = $securityQuesDB->getData();
    	$this->view->securityquestion = $securityQuesData;
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				//process form 
				$faculty = new Finance_Model_DbTable_Paymentmode();
				
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
				//$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));		
        	
        }
    }
    
	public function updateAction(){
		//title
    	$this->view->title="Update Payment Details";
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'finance','controller'=>'individual', 'action'=>'index'),'default',true));		
    	
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

