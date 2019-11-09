<?php
/**
 * 
 * @author Muhamad Alif
 * @version 
 */

class AnrSetup_MainProgramController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Main Program Setup";
    	
    	//paginator
		$masterDB = new App_Model_Record_DbTable_MainProgram();
		$master = $masterDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($master));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Main Program";
    	
    	$form = new AnrSetup_Form_MainProgram();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$masterDB = new App_Model_Record_DbTable_MainProgram();
				$masterDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'main-program', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Main Program";
    	
    	$form = new AnrSetup_Form_MainProgram();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$master = new App_Model_Record_DbTable_MainProgram(); 
				$master->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'main-program', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$master = new App_Model_Record_DbTable_MainProgram();
    			$form->populate($master->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$master = new App_Model_Record_DbTable_MainProgram();
    		$master->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'main-program', 'action'=>'index'),'default',true));
    	
    }

}

