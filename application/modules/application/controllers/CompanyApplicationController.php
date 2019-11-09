<?php
class Application_CompanyApplicationController extends Zend_Controller_Action
{

	public function indexAction(){
	
		//title
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Company Application";
		
    	if ($this->getRequest()->isPost()) {
    		
			$compID = $this->getRequest()->getPost('comp_regID',"");
			
			//check already applied or not
			$companyDB = new App_Model_General_DbTable_TakafulOperator();
			$company = $companyDB->getDataByCompRegID($compID);
			
					
			if(!$company){
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'company-application', 'action'=>'company-particular','compID'=>$compID),'default',true));
			}else{
				$this->view->noticeMessage = "You have already registered for Takaful Basic Examination. Please log in at http://www.takafuleexam.com";
			}
			
			
    	}
		
	} 
	
	
	public function companyParticularAction(){
		
		//title
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Company Particular";
    	
    	$compID= $this->_getParam('compID', 0);
		$this->view->registrationNo = $compID;
		
		$takafulDB = new App_Model_General_DbTable_TakafulOperator();
		$takafulData = $takafulDB->getDataType(2);
		$this->view->takaful = $takafulData;
		
		$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia only
		$this->view->state = $stateData;
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$formdata["password"] = md5($formdata["clearpass"]);			
			$formdata["updUser"]  = $auth->getIdentity()->id; 			
			$formdata["updDate"]  = date('Y-m-d H:i:s');			
			
			$companyDB = new App_Model_General_DbTable_TakafulOperator();
			$id = $companyDB->addData($formdata);
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'company-application', 'action'=>'confirmation','id'=>$id),'default',true));
		}
		
	}
	
	
	
	public function confirmationAction()
    {
    
    	$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Company Registration";
    	
    
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	
    	$takafulDB = new App_Model_General_DbTable_TakafulOperator();
    	$company = $takafulDB->getInfo($id);  

    	$subject = "COMPANY REGISTRATION";
    	
    	$address = $company["address"]."<br>".
    	           $company["postcode"]." ".$company["city"]."<br>".
    	           $company["state"]. "<br>".
    	           $company["country"];
    	           
     	
        $emailtemplateDb = new App_Model_General_DbTable_EmailTemplate();
    	$templateData = $emailtemplateDb->getData(6);
    	 
		$templateMail = $templateData['TemplateBody'];	
		
		$templateMail = str_replace("[Company]",$company['name'],$templateMail);
		$templateMail = str_replace("[Person]",$company['contactperson'],$templateMail);
		$templateMail = str_replace("[Address]",$address,$templateMail);
		$templateMail = str_replace("[RegistrationNo]",$company['registrationNo'],$templateMail);	
		$templateMail = str_replace("[LoginId]",$company['loginid'],$templateMail);	
		$templateMail = str_replace("[Password]",$company['clearpass'],$templateMail);			
		
	    $sent = $this->sendMail($company['email'], $company['contactperson'], $subject, $templateMail);
	    	    
	    $this->view->emailTemplate = $templateMail;
		
    }
    
     public function sendMail($recipient,$fullname,$subject,$templateMail){
     	
     	//require_once 'Zend/Mail.php';
		require_once 'Zend/Mail/Transport/Smtp.php';
			
			$config = array( 'auth' => 'login',
                             'username' => 'ibfiminfo@gmail.com',
                             'password' => 'abcd123#',
                             'ssl' => 'ssl',
                             'port' => 465);		
					
          	
			$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config);
            Zend_Mail::setDefaultTransport($transport);  								
                        		
            $mail = new Zend_Mail();
			$mail->setFrom('IBFIM Administrator');
			//$mail->addTo($recipient,$fullname);	//onkan bila nak pakai
			$mail->addTo('mardhiyati@oum.edu.my',$fullname); //disabledkan utk testing only
			$mail->setSubject('COMPANY REGISTRATION');
			$mail->setBodyHtml($templateMail);		
			$mail->send(); 
			
			//Send
			 $sent = true;
			 try {
			  $mail->send();
			 }
			 catch (Exception $e) {
			  $sent = false;
			 }
			
			 return $sent;
    }
    
    
    
    
    public function ajaxCheckUsernameAction($id=null){
    	
    	$username = $this->_getParam('id', 0);    	
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        $takafulDB = new App_Model_General_DbTable_TakafulOperator();
    	$companyData = $takafulDB->checkUsername($username);  	
    	
		
		if($companyData){
			$msg = "Please select different username";
		}else{
			$msg = "";
		}
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($msg);
		
		$this->view->json = $json;

    }
	
	
	
}