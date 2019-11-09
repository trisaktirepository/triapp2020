<?php

class Application_OfferLetterTemplateController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Offer Letter Template Setup";
    	
    	//paginator
		$marketDB = new App_Model_Application_DbTable_OfferLetterTemplate();
		$market = $marketDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($market));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Offer Letter Template";
    	
    	$form = new Application_Form_OfferLetterTemplate();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$master = new App_Model_Application_DbTable_OfferLetterTemplate();
				$master->add($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'offer-letter-template', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Offer Letter Template";
    	
    	$form = new Application_Form_OfferLetterTemplate();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$market = new App_Model_Application_DbTable_OfferLetterTemplate(); 
				$market->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'offer-letter-template', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$market = new App_Model_Application_DbTable_OfferLetterTemplate();
    			$form->populate($market->getTemplate($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$market = new App_Model_Record_DbTable_Market();
    		$market->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'offer-letter-template', 'action'=>'index'),'default',true));
    	
    }
    
	public function viewdetailAction() {
		//title
    	$this->view->title="Offer Letter Template";
    	
    	$id = $this->_getParam('id', 0);
    	
    	//paginator
		$itemDB = new App_Model_Application_DbTable_OfferLetterItem();
		$item = $itemDB->checkItem($id);
		
    	$this->view->item = $item;
		$this->view->id = $id;
	}
	
	public function adddetailAction()
    {
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	//title
    	$this->view->title="Add Offer Letter Item";
    	
    	$id = $this->_getParam('id', 0);
		$program_id = $this->_getParam('programID', 0);
		$entryID = $this->_getParam('entryID', 0);
    	
    	$form = new Application_Form_OfferLetterItem(array('programID'=>$program_id));
    	
		if ($this->getRequest()->isPost() && $program_id!=0) {
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				$requirement = new App_Model_Application_DbTable_Requirement();
				$requirement->addRequirement($formData);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'entry-requirement', 'action'=>'view','id'=>$entryID),'default',true)."#tabs-2");		
			}
        }
        
        	$this->view->form = $form;
        	$this->view->id = $id;
            
    }

    public function addItemAction()
    {
    	//title
    	$this->view->title="Add Item Offer Letter Template";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			$master = new App_Model_Application_DbTable_OfferLetterItem();
//			$check = $master->checkItem($id);
			
//			if ($check) {
//					
//				$master->updateData($formData,$id);
				
				//redirect		
//				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'offer-letter-template', 'action'=>'index'),'default',true));
//			}else{
				$master->add($formData,$id);
//			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }

}


