<?php
/**
 * Setup_ClientController
 * 
 * @author Muhamad Alif
 * @version 
 */

class GeneralSetup_EmailTemplateController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Email Template Setup";
    	
    	//paginator
		$clientTypeDB = new App_Model_General_DbTable_EmailTemplate();
		$clientType = $clientTypeDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($clientType));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addTypeAction()
    {
    	//title
    	$this->view->title="Add Client Type";
    	
    	$form = new GeneralSetup_Form_EmailTemplate();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$faculty = new App_Model_General_DbTable_EmailTemplate();
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					"name" => $formData["name"],
					"shortName" => $formData["shortName"],
					"email" => $formData["email"],
					"contactperson" => $formData["contactperson"],
					"contactno" => $formData["contactno"],
					"loginid" => $formData["loginid"],
					"password" => md5($formData["password"]),
					"clearpass" => $formData["password"],
					"updUser" => $idUpd,
					"updDate" => $date
				
				);
				
				
				$faculty->addData($data);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editTypeAction(){
		//title
    	$this->view->title="Edit Client Type";
    	
    	$form = new GeneralSetup_Form_EmailTemplate();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$type = new App_Model_General_DbTable_EmailTemplate(); 
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					"name" => $formData["name"],
					"shortName" => $formData["shortName"],
					"email" => $formData["email"],
					"contactperson" => $formData["contactperson"],
					"contactno" => $formData["contactno"],
					"loginid" => $formData["loginid"],
					"password" => md5($formData["password"]),
					"clearpass" => $formData["password"],
					"updUser" => $idUpd,
					"updDate" => $date
				
				);
				
				$type->updateData($data,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$type = new App_Model_General_DbTable_EmailTemplate();
    			$form->populate($type->getData($id));
    		}
    		
    	}
    }
    
	public function deleteTypeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new App_Model_General_DbTable_EmailTemplate();
    		$type->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));
    	
    }
    
}

