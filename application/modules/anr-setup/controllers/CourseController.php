<?php


require_once 'Zend/Controller/Action.php';

class AnrSetup_CourseController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Course Setup";
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		//paginator
			$courseDB = new App_Model_Record_DbTable_Course();
			$course = $courseDB->search($formData['name'],$formData['code']);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($course));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
    	}else{
	    	//paginator
			$courseDB = new App_Model_Record_DbTable_Course();
			$course = $courseDB->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($course));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
    	}
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Course";
    	
    	$form = new AnrSetup_Form_Course();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$course = new App_Model_Record_DbTable_Course();
				$course->addData($formData);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'course', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Course";
    	
    	$id = $this->_getParam('id', 0);
    	
    	$form = new AnrSetup_Form_Course();
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$courseDB = new App_Model_Record_DbTable_Course(); 
				
				//TODO: check for course status in active landscape.
				
				$courseDB->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'course', 'action'=>'index'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$course = new App_Model_Record_DbTable_Course();
    			$form->populate($course->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$course = new App_Model_Record_DbTable_Course();
    		$course->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'course', 'action'=>'index'),'default',true));
    	
    }
    
	public function ajaxGetCourseAction($id=null){
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
            
	  	
	  	
	  	 $courseDB = new App_Model_Record_DbTable_Course();
		 $course_data = $courseDB->getData($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($course_data);
		
		$this->view->json = $json;

    }
}


