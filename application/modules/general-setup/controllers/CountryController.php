<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */


class GeneralSetup_CountryController extends Zend_Controller_Action {

	public function indexAction() {
		//title
    	$this->view->title="Country Setup";
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		//paginator
			$countryDB = new App_Model_General_DbTable_Country();
			$country = $countryDB->getPaginateSearch($formData['search']);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($country));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    		
    	}else{
    	
	    	//paginator
			$countryDB = new App_Model_General_DbTable_Country();
			$country = $countryDB->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($country));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Country";
    	
    	$form = new GeneralSetup_Form_Country();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$country = new App_Model_General_DbTable_Country();
				$country->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'country', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Country";
    	
    	$form = new GeneralSetup_Form_Country();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$country = new App_Model_General_DbTable_Country(); 
				$country->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'country', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$country = new App_Model_General_DbTable_Country();
    			$form->populate($country->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$country = new App_Model_General_DbTable_Country();
    		$country->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'country', 'action'=>'index'),'default',true));
    	
    }
    
	public function ajaxGetStateAction($id=0){
    	$id = $this->_getParam('id', 0);
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	$stateDB = new App_Model_General_DbTable_Country();
		$state_data = $stateDB->getState($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($state_data);
		
		$this->view->json = $json;

    }

}

