<?php
/**
 * Setup_FacultyController
 * 
 * @author Muhamad Alif
 * @version 
 */

class GeneralSetup_FacultyController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Department Setup";
    	
    	//paginator
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty = $facultyDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($faculty));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Faculty";
    	
    	$form = new GeneralSetup_Form_Faculty();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$faculty = new App_Model_General_DbTable_Faculty();
				$faculty->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'faculty', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Faculty";
    	
    	$form = new GeneralSetup_Form_Faculty();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$faculty = new App_Model_General_DbTable_Faculty(); 
				$faculty->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'faculty', 'action'=>'index'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$faculty = new App_Model_General_DbTable_Faculty();
    			$form->populate($faculty->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$faculty = new App_Model_General_DbTable_Faculty();
    		$faculty->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'faculty', 'action'=>'index'),'default',true));
    	
    }
    
	public function ajaxGetFacultyAction($id=null){
    	$id = $this->_getParam('id', 0);
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	$facultyDB = new Setup_Model_DbTable_Faculty();
		$faculty_data = $facultyDB->getFacultyBranch($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($faculty_data);
		
		$this->view->json = $json;

    }
}

