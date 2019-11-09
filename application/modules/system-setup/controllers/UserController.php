<?php

class SystemSetup_UserController extends Zend_Controller_Action
{
	
    public function indexAction(){
    	$this->view->title = "User Management";
    	
    	$userDB = new SystemSetup_Model_DbTable_User();
    	
    	$usr = $userDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($usr));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
    }
    
    public function addAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }else{
        	$this->view->button = "<dt id='buttons-label'> </dt>
			<dd id='buttons-element'>
			<div class='buttons'>
			<input id='save' type='submit' value='Submit' name='save' onClick=\"$('#user_form').submit();\" />
			<input id='cancel' type='submit' onclick='window.location =\"/system-setup/user\"; return false;' value='Cancel' name='cancel'/>
			</div>
			</dd>";
        }
        
    	$form = new SystemSetup_Form_User();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$auth = Zend_Auth::getInstance();
				
				$userDB = new SystemSetup_Model_DbTable_User();
				
				try{
					$uid = $userDB->addData(
					array(
						'fullname'=>$formData['fullname'],
						'staff_id'=>$formData['staff_id'],
						'username'=>$formData['username'],
						'password'=>md5("ustyusty"),
						'created_by'=>$auth->getIdentity()->id
					));
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While add new user");
				}
				
				
				if($uid!=null){
					$this->_helper->flashMessenger->addMessage("Successfuly add new user");
				}
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'user', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    	
    }
    
    public function editAction(){
    	
    	$user_id = $this->_getParam('id', 0);
    	$this->view->user_id = $user_id;
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }else{
        	$this->view->button = "<dt id='buttons-label'> </dt>
			<dd id='buttons-element'>
			<div class='buttons'>
			<input id='save' type='submit' value='Submit' name='save' onClick=\"$('#user_form').submit();\" />
			<input id='cancel' type='submit' onclick='window.location =\"/system-setup/user\"; return false;' value='Cancel' name='cancel'/>
			</div>
			</dd>";
        }
        
    	$form = new SystemSetup_Form_User();
    	
    	$userDB = new SystemSetup_Model_DbTable_User();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$userDB->updateData($formData,$user_id);
				$this->_helper->flashMessenger->addMessage("Successfuly Update the user");
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'user', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($user_id!=0){
    			$form->populate($userDB->getData($user_id));
    		}
    	}
    	
        $this->view->form = $form;
    }
    
    public function deleteAction(){
    	$user_id = $this->_getParam('id', 0);
    	$this->view->user_id = $user_id;
    	
    	if($user_id!=0){
    		$userDB = new SystemSetup_Model_DbTable_User();
    		$userDB->deleteData($user_id);
    		$this->_helper->flashMessenger->addMessage("Successful deleting user");
    	}else{
    		$this->_helper->flashMessenger->addMessage("Error occurs while deleting user. <br />User not deleted");
    	}
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'user', 'action'=>'index'),'default',true));
    }
    
    public function resetAction(){
    	$user_id = $this->_getParam('id', 0);
    	$this->view->user_id = $user_id;
    	
    	if($user_id!=0){
    		$userDB = new SystemSetup_Model_DbTable_User();
    		$userDB->update(array('password'=>md5("ustyusty")),'id = '.$user_id);
    		
    		$this->_helper->flashMessenger->addMessage("Successful reseting user password. <br />Password will be 'ustyusty'");
    	}else{
    		$this->_helper->flashMessenger->addMessage("Error occurs while reseting user's password. <br />Password not reset");
    	}
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'user', 'action'=>'index'),'default',true));
    }
    
}

