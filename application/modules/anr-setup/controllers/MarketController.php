<?php
/**
 * Setup_MarketController
 * 
 * @author Muhamad Alif
 * @version 
 */

class AnrSetup_MarketController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Learning Mode Setup";
    	
    	//paginator
		$marketDB = new App_Model_Record_DbTable_Market();
		$market = $marketDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($market));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Learning Mode";
    	
    	$form = new AnrSetup_Form_Market();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$master = new App_Model_Record_DbTable_Market();
				$master->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'market', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Learning Mode";
    	
    	$form = new AnrSetup_Form_Market();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$market = new App_Model_Record_DbTable_Market(); 
				$market->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'market', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$market = new App_Model_Record_DbTable_Market();
    			$form->populate($market->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$market = new App_Model_Record_DbTable_Market();
    		$market->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'market', 'action'=>'index'),'default',true));
    	
    }

}


