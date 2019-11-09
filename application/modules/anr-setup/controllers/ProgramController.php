<?php
/**
 * @author Muhamad Alif
 * @version 
 */

class AnrSetup_ProgramController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Program Setup";
    	
		//faculty
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty = $facultyDB->getData();
		
		$this->view->faculty = $faculty;
		
		$programSelected=0;
         if ($this->_request->isPost()) {         	
			 $programSelected= $this->getRequest()->getPost('selProgram');
			 $this->view->programSelected = $programSelected;
         }
		
		//paginator
		$programDB = new App_Model_Record_DbTable_Program();
		$program = $programDB->getDataFaculty($programSelected);
//		$program = $programDB->getPaginateData();

		echo $program;
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($program));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Program";
    	
    	$form = new AnrSetup_Form_Program();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$program = new App_Model_Record_DbTable_Program();
				$program->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
    public function viewAction(){
    	//title
    	$this->view->title="Program Details";
    	$id = $this->_getParam('id', 0);
    	
    	$programDB = new App_Model_Record_DbTable_Program();
    	$this->view->data = $programDB->getData($id); 
    	
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Program";
    	
    	$form = new AnrSetup_Form_Program();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$program = new App_Model_Record_DbTable_Program(); 
				$program->updateData($formData,$id);
				 
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$program = new App_Model_Record_DbTable_Program();
    			$form->populate($program->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$program = new App_Model_Record_DbTable_Program();
    		$program->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program', 'action'=>'index'),'default',true));
    	
    }
    
	public function ajaxGetProgramAction($id=null){
    	$id = $this->_getParam('id', 0);
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
	  	// $programDB = new App_Model_DbTable_Program();
		// $program_data = $programDB->getProgramMaster($id);
				
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($program_data);
		
		$this->view->json = $json;

    }
}


