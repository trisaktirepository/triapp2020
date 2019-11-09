<?php
/**
 * Department Controller for ICampus
 * 
 * @author Muhamad Alif Muhammad
 * @version 1.0
 */

class GeneralSetup_DepartmentController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Department Setup";
    	
    	//paginator
		$departmentDB = new App_Model_General_DbTable_Department();
		$department = $departmentDB->getDepartmentArray();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($department));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Department";
    	
    	$form = new GeneralSetup_Form_Department();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$departmentDB = new App_Model_General_DbTable_Department();
				$departmentDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'department', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Department";
    	    	
    	$form = new GeneralSetup_Form_Department();
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$departmentDB = new App_Model_General_DbTable_Department(); 
				$departmentDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'department', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$departmentDB = new App_Model_General_DbTable_Department();
    			$form->populate($departmentDB->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$departmentDB = new App_Model_General_DbTable_Department();
    		$departmentDB->deleteData($id);
    	}
    		
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'department', 'action'=>'index'),'default',true));
    	
    }
    
    public function viewAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
    	$this->view->title="Department Details";
    	
    	$id = $this->_getParam('id', 0);
		$departmentDB = new App_Model_General_DbTable_Department();
		
		$this->view->data  = $departmentDB->getData($id);
    }
    
	public function ajaxGetDepartmentAction($faculty_id=0){
    	$faculty_id = $this->_getParam('id', 0);
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	$departmentDB = new App_Model_General_DbTable_Department();
		$department_data = $departmentDB->getDepartmentFromFaculty($faculty_id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($department_data);
		
		$this->view->json = $json;

    }

}

