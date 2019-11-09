<?php

class Application_PlacementTestEmailController extends Zend_Controller_Action {
	
	private $_dbObject;
	
	public function init(){
		$this->_dbObject = new App_Model_Application_DbTable_PlacementTestEmail();
	}
	
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Email - Setup";
    	
    	if ( $this->getRequest()->isPost() ){
    		
			$searchSql = $this->_dbObject->searchPaginateProgram( $this->_request->getPost () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    	
	    	//paginator
			$programList = $this->_dbObject->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($programList));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
	}
	
	public function detailAction() {
		//title
    	$this->view->title="Placement Test Email - Detail";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	
    	
    	if ( $this->getRequest()->isPost() ){
    		
    		$emailTemplateDetailDb = new App_Model_Application_DbTable_PlacementTestEmailDetail();
    		
    		$formData = $this->getRequest()->getPost();
    		
    		$i=0;
    		foreach ($formData['etd_language'] as $lang){
    			$data = array(
    				'etd_eth_id' => $formData['etd_eth_id'],
    				'etd_id' => isset($formData['etd_id'][$i]) && $formData['etd_id'][$i]!=""?$formData['etd_id'][$i]:null,
    				'etd_language' => $lang,
    				'etd_subject' => $formData['subject'][$i],
    				'etd_body' => $formData['content'][$i]
    			);
    			
    			/*echo "<pre>";
	    		print_r($data);
	    		echo "</pre>";*/
	    		
	    		if($data['etd_id']!=null){
	    			$emailTemplateDetailDb->updateData($data, $data['etd_id']);
	    		}else{
	    			$emailTemplateDetailDb->addData($data);
	    		}
    			
    			$i++;
    		}
    		
    		$this->view->noticeSuccess = $this->view->translate("Email Template Updated");
    	}
    	
    	//head data
    	$emailHeadData = $this->_dbObject->getData($id);
    	$this->view->email_head = $emailHeadData;
    	
    	//language
    	$applicantLanguageDb = new App_Model_Application_DbTable_ApplicantLanguage();
    	$languageList = $applicantLanguageDb->getData();
    	
    	//get everylanguage detail
    	$detail = array();
    	$i=0; 
    	foreach ($languageList as $lang){
    		$detail[$i] = $this->_dbObject->getHeadDetail($emailHeadData['eth_id'],$lang['al_id']);
    		$i++;
    	}
    	
    	$template = array(
    		'head' => $emailHeadData,
    		'language' => $languageList,
    		'detail' => $detail
    	);
    	
    	$this->view->emailTemplate = $template;

	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Placement Test Email - Add";
    	
    	$form = new Application_Form_PlacementTestEmailHead();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			if ($form->isValid($formData)) {
				
				//process form
				$auth = Zend_Auth::getInstance();
	    		$formData['eth_create_by'] = $auth->getIdentity()->id;
				$formData['eth_create_date'] = date('d/m/y h:ia');
	    		
				$this->_dbObject->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-email', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction()
    {
    	//title
    	$this->view->title="Placement Test Email - Edit";
    	
    	$form = new Application_Form_PlacementTestEmailHead();
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	$placementTestEmailDb = new App_Model_Application_DbTable_PlacementTestEmail();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$this->_dbObject->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-email', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($placementTestEmailDb->getData($id));
    		}
    		
    	}
        
    }
    
	public function deleteAction(){
    	$id = $this->_getParam('id', 0);
    	$data = $this->_dbObject->getData($id);
    	
    	if($id>0){
    		$this->_dbObject->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-email', 'action'=>'index'),'default',true));
    	
    }
}