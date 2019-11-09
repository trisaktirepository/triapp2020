<?php
/**
 * AnrSetup_AwardController
 * 
 * @author Muhamad Alif
 * @version 
 */


class AnrSetup_AwardController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Award Setup";
    	
    	//paginator
		$awardDB = new App_Model_Record_DbTable_Award();
		$award = $awardDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($award));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Award";
    	
    	$form = new AnrSetup_Form_Award();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$award = new App_Model_Record_DbTable_Award();
				$award->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'award', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Award";
    	
    	$form = new AnrSetup_Form_Award();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$award = new App_Model_Record_DbTable_Award(); 
				$award->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'award', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$award = new App_Model_Record_DbTable_Award();
    			$form->populate($award->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$award = new App_Model_Record_DbTable_Award();
    		$award->deleteData($id);
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'award', 'action'=>'index'),'default',true));
    	
    }

}




