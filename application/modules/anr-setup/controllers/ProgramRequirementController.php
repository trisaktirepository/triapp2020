<?php
/**
 * SetupProgramRequirementController
 * 

 * @version 
 */

class AnrSetup_ProgramRequirementController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		$search_name = $this->_getParam('name', null);
		$this->view->search_name = $search_name;
		
		$search_code = $this->_getParam('code', null);
		$this->view->search_code = $search_code;
		
		//title
    	$this->view->title="Programme Requirement";
    	
    	//faculty
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty = $facultyDB->getData();
		
		$this->view->faculty = $faculty;
    	
		//selected faculty
    	$programSelected=0;
         if ($this->_request->isPost()) {         	
			 $programSelected= $this->getRequest()->getPost('selProgram');
			 $this->view->programSelected = $programSelected;
         }
    	
    	//paginator
		$programDB = new App_Model_Record_DbTable_Program();
//		$program_list = $programDB->getPaginateData($search_name,$search_code);
		$program_list = $programDB->getDataFaculty($programSelected);
		
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
    	$this->view->title="Program Requirement";
    	
    	$program_id = $this->_getParam('id', 0);
    	$this->view->program_id = $program_id;
    	
    	//program info
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program_data = $programDB->getData($program_id);
    	$this->view->program = $program_data;    	
    	
    	//program course
    	$programCourseDB = new App_Model_Record_DbTable_ProgramCourse();
    	$this->view->program_course = $programCourseDB->getProgramCourse($program_id);
    	
    	//check for course
    	if($this->view->program_course==null){
    		$this->view->noticeMessage = "This program dont have any course tagged";	
    	}
    	
    	//course
    	$courseDB = new App_Model_Record_DbTable_Course();
    	$this->view->courselist = $courseDB->getCourseNotTagged($program_data['id']);
    	    	
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