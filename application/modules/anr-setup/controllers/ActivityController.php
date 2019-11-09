<?php
/**
 * @version 
 */


class AnrSetup_ActivityController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Activity Setup";
    	
    	//paginator
		$activityDB = new App_Model_Record_DbTable_Activity();
		$activity = $activityDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($activity));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Activity";
    	
    	$form = new AnrSetup_Form_Activity();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$ActivityDB = new App_Model_Record_DbTable_Activity();
				$ActivityDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'activity', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Activity";
    	
    	$form = new AnrSetup_Form_Activity();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$activityDB = new App_Model_Record_DbTable_Activity(); 
				$activityDB->updateData($formData,$id);
				
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'activity', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$activityDB = new App_Model_Record_DbTable_Activity();
    			$form->populate($activityDB->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$activity = new App_Model_Record_DbTable_Activity();
    		$activity->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'activity', 'action'=>'index'),'default',true));
    	
    }

}




