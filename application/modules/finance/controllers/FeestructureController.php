<?php
/**
 * SetupProgramRequirementController
 * 

 * @version 
 */

class Finance_FeestructureController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		$search_name = $this->_getParam('name', null);
		$this->view->search_name = $search_name;
		
		$search_code = $this->_getParam('code', null);
		$this->view->search_code = $search_code;
		
		//title
    	$this->view->title="Fee Structure";
    	
    
    	//paginator
		$programDB = new App_Model_Record_DbTable_Course();
//		$program_list = $programDB->getPaginateData($search_name,$search_code);
		$program_list = $programDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($program_list));
		$paginator->setItemCountPerPage(30);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		//Program Data
    	$program = $programDB->getData();
    	$this->view->programlist = $program;
    	
    	
	}
	
	public function viewAction(){
		//title
    	$this->view->title="Fee Structure Details";
    	
    	$courseID = $this->_getParam('id', 0);
    	$this->view->courseID = $courseID;
    	
    	//program info
    	$programDB = new App_Model_Record_DbTable_Course();
    	$program_data = $programDB->getData($courseID);
    	$this->view->program = $program_data;    	
    	
    	//program course
    	$feestructureDB = new Finance_Model_DbTable_Feestructure();
    	$this->view->feestructure = $feestructureDB->getFee($courseID);
    	
    	//check for course
    	if($this->view->feestructure==null){
    		$this->view->noticeMessage = "This program dont have any course tagged";	
    	}
    	
    	
    	    	
	}
	
	public function addCourseAction()
    {
    	//title
    	$this->view->title="Add Course";
    	
    	$program_id = $this->_getParam('program_id', 0);
    	
    	
		if ($this->getRequest()->isPost() && $program_id!=0) {
			$auth = Zend_Auth::getInstance();
			$formData = $this->getRequest()->getPost();
			
			//process form
			$programCourseDB = new App_Model_Record_DbTable_ProgramCourse();
			
			$data = array(
					'program_id'=>$program_id,
					'course_id'=>$formData['course_id'],
					'created_by'=>$auth->getIdentity()->id,
					'date_created'=>date('Y-m-d H:i:s')
					);
						
			$programCourseDB->addData($data);
			
			//redirect
			$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program-requirement', 'action'=>'view','id'=>$program_id),'default',true)."#tabs-1");		
			
        	
        }else{
        	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program-requirement', 'action'=>'index'),'default',true)."#tabs-1");
        }        
    }
    
	public function deleteCourseAction($id = 0){
    	$id = $this->_getParam('id', 0);
    	$program_id = $this->_getParam('programID', 0);
    	
    	if($id>0){
    		$programCourseDB = new App_Model_Record_DbTable_ProgramCourse();
    		$programCourseDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'program-requirement', 'action'=>'view','id'=>$program_id),'default',true)."#tabs-2");
    	
    }
    
}