<?php
class AuthenticationController extends Zend_Controller_Action
{

	public function indexAction(){
		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'authentication', 'action'=>'login'),'default',true));
		exit;	
	}
	
    public function loginAction()
    {
    	
    	$this->_helper->layout->disableLayout();
    	 
    	$form = new App_Form_Login();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			 
			if ($form->isValid($formData)) {
				
				// collect the data from the user
	            Zend_Loader::loadClass('Zend_Filter_StripTags');
	            $filter = new Zend_Filter_StripTags();
	            $username = $filter->filter($this->_request->getPost('username'));
	            $password = $filter->filter($this->_request->getPost('password'));
	            
	            
				//process form 
				$dbAdapter = Zend_Db_Table::getDefaultAdapter();
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
				
				$authAdapter->setTableName('tbl_user')
				    		->setIdentityColumn('loginName')
				    		->setCredentialColumn('passwd');
				    		
				// Set the input credential values to authenticate against
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential(md5($password));
                //$authAdapter->setCredential($password);
                
				// do the authentication 
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                	
                	$data = $authAdapter->getResultRowObject(null, 'password');
                    
                    //mapping id with userid from table user
                    $data->id = $data->iduser;
                    
                    
                    if($formData["mytype"]==1){ //usakti
                    	 //inject adminrole
                   		 $data->role = "administrator";
                    }elseif ($formData["mytype"]==2){
                    	 $data->role = "agent";
                    }
                   
                    
                    $auth->getStorage()->write($data);
                    
                    $username = $auth->getIdentity()->username; 
                    
                     if($formData["mytype"]==1){ //usakti
                     	//echo 'here';exit;
                    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'language', 'action'=>'index'),'default',true));
                     }elseif ($formData["mytype"]==2){
                    	 $this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
                     }
                    
                } else {
                    // failure: clear database row from session
                    $this->view->alertError = 'Login failed. Either username or password is incorrect';
                }
				
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'authentication', 'action'=>'login'),'default',true));
    }
    
    
  	public function applicantloginAction()
    {
    	
    	$this->_helper->layout->disableLayout();
    	
    	$form = new App_Form_Login();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			 
			if ($form->isValid($formData)) {
				
				// collect the data from the user
	            Zend_Loader::loadClass('Zend_Filter_StripTags');
	            $filter = new Zend_Filter_StripTags();
	            $username = $filter->filter($this->_request->getPost('username'));
	            $password = $filter->filter($this->_request->getPost('password'));
	            
	            
				//process form 
				$dbAdapter = Zend_Db_Table::getDefaultAdapter();
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
				
				$authAdapter->setTableName('applicant_profile')
				    		->setIdentityColumn('appl_email')
				    		->setCredentialColumn('appl_password');
				    		
				// Set the input credential values to authenticate against
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);
                //$authAdapter->setCredential($password);
                
                // do the authentication 
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
               
                if ($result->isValid()) {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                    $data = $authAdapter->getResultRowObject(null, 'password');
                    
                    //mapping id with userid from table user
                    $data->id = $data->appl_id;
                                        
                    //inject applicant role
                    $data->role = "applicant";
                    
                                    
                    $auth->getStorage()->write($data);
                    
                    $username = $auth->getIdentity()->appl_email; 
                    
                   
                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata','id'=>$data->id),'default',true));
                } else {
                    // failure: clear database row from session
                    $this->view->alertError = 'Login failed. Either username or password is incorrect';
                }
				
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
    public function verifyAction()
    {
        $key = $this->_getParam('key', null);
        
        if($key != null)
        {
            $appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
            $applicant = $appProfileDB->getDataVerify($key);
            
            if(isset($applicant['verifyKey']))
            {
                $update = array(
                    'verifyKey' => NULL
                );
                $appProfileDB->updateData($update,$applicant['appl_id']);
                $this->view->status = 'success';
            }
            else
            {
                $this->view->status = 'failed';
            }
        }
        else
        {
            $this->view->status = 'failed';
        }
    }
    
    
	
}
?>