<?php

class AnrSetup_AcademicLandscapeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $search_name = $this->_getParam('name', null);
		$this->view->search_name = $search_name;
		
		$search_code = $this->_getParam('code', null);
		$this->view->search_code = $search_code;
		
		//title
    	$this->view->title="Program Academic Landscape - Program List";
    	
    	//paginator
		$programDB = new App_Model_Record_DbTable_Program();
		$program_list = $programDB->getPaginateData($search_name,$search_code);
		
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
    	$this->view->title="Program Academic Landscape - Landscape List";
    	
    	$program_id = $this->_getParam('program-id', 0);
    	$this->view->program_id = $program_id;
    	
    	//program info
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program_data = $programDB->getData($program_id);
    	$this->view->program = $program_data;
    	    	
    	//requirement info
    	$programRequirementDetailDB = new App_Model_Record_DbTable_ProgramRequirementDetail();
    	$programRequirement_data  = $programRequirementDetailDB->getCourseRequirement($program_id);
    	$this->view->programRequirement_data = $programRequirement_data;
    	
    	if(!$programRequirement_data){
    		$this->view->noticeError = "Please setup program requirement for this program first. Click <a href=".$this->view->url(array('module'=>'anr-setup','controller'=>'program-requirement','action'=>'view','id'=>$program_id)).">Here</a> to setup program requirement";
    	}
    	    	
    	//landscape
    	$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
    	$this->view->landscapes = $academicLandscapeDB->getProgramLandscape($program_id);
    	
    	//check for landscape and active landscape
    	if($this->view->landscapes){
    		$status = false;
    		foreach($this->view->landscapes as $land){
    			if($land['status']==1){
    				$status=true;
    				break;
    			}
    		}
    		
    		if(!$status){
    			$this->view->noticeMessage = "This program dont have any active academic landscape";	
    		}
    	}else{
    		$this->view->noticeMessage = "This program dont have any active academic landscape";
    	}
    	
    	
    }
    
	public function addAction(){
    	//title
    	$this->view->title="Add New Academic Landscape";
    	
    	$program_id = $this->_getParam('program-id', 0);
    	$type = $this->_getParam('type', 0);
    	$this->view->program_id = $program_id;
    	
    	if($program_id!=0 && $type!=0){
    		$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
    		
    		$auth = Zend_Auth::getInstance();
    		
    		$data = array(
    					'program_id'=>$program_id,
    					'last_changes'=>date('Y-m-d H:i:s'),
    					'update_by'=>$auth->getIdentity()->id,
    					'status'=>0,
    					'landscape_type'=>$type
    				);
    		
			$academicLandscapeDB->addData($data);
    	}
		
		$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view','program-id'=>$program_id),'default',true));
    	
    }
    
    /*
     *  This function is removed from system for avoiding user to remove landscape
     */ 
    
	/*public function deleteAction($id = 0){
    	$id = $this->_getParam('id', 0);
    	$program_id = $this->_getParam('program-id', 0);
    	    	
    	if($id>0){
    		$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
    		$academicLandscapeDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view','program-id'=>$program_id),'default',true));
    	
    }*/
    
    public function toggleAction(){
    	$id = $this->_getParam('id', 0);
    	$program_id = $this->_getParam('program-id', 0);
    	
    	$academic_landscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
    	$academic_landscapeDB->update(array('status'=>0), 'program_id = '.$program_id);
    	$academic_landscapeDB->update(array('status'=>1), 'id = '.$id);
    	
    	//redirect
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view','program-id'=>$program_id),'default',true));
    }
    
	public function viewDetailAction(){
    	//title
    	$this->view->title="Program Academic Landscape - Courses List";
    	
    	$landscape_id = $this->_getParam('id', 0);
    	$this->view->id = $landscape_id;
    	    	
    	//landscape
    	$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
    	$landscape = $academicLandscapeDB->getData($landscape_id);
    	$this->view->landscapes = $landscape;
    	$this->view->program_id = $landscape['program_id'];
    	
    	//program info
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program_data = $programDB->getData($landscape['program_id']);
    	$this->view->program = $program_data;
    	    	
    	//requirement info
    	$programRequirementDetailDB = new App_Model_Record_DbTable_ProgramRequirementDetail();
    	$programRequirement_data  = $programRequirementDetailDB->getCourseRequirement($landscape['program_id']);
    	$this->view->programRequirement_data = $programRequirement_data;
    	
    	//get academic_landscape_course
    	$academicLandscapeCourseDB = new App_Model_Record_DbTable_AcademicLandscapeCourse();
    	$this->view->courses = $academicLandscapeCourseDB->getCourse($landscape_id);
    }
    
    public function addCourseAction(){
    	//title
    	$this->view->title="Add course to Academic Landscape";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$aid = $this->_getParam('aid', 0);
    	$this->view->aid = $aid;
    	
    	$program_id = $this->_getParam('program-id', 0);
    	$this->view->program_id = $program_id;
    	
    	$form = new AnrSetup_Form_AcademicLandscapeCourse(array('programID'=>$program_id,'academicID'=>$id));
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscapeCourse();
				$academicLandscapeDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view-detail','program-id'=>$program_id,'id'=>$id),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function editCourseAction(){
		$academicLandscapeCourseDB = new App_Model_Record_DbTable_AcademicLandscapeCourse();
		
		//title
    	$this->view->title="Edit Academic Landscape - Course";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$academic_landscape_id = $this->_getParam('aid', 0);
		$this->view->aid = $academic_landscape_id;
    	
    	$program_id = $this->_getParam('program-id', 0);
    	$this->view->program_id = $program_id;
    	
    	$form = new AnrSetup_Form_AcademicLandscapeCourse(array('programID'=>$program_id,'academicID'=>$academic_landscape_id));
				
		if ($this->getRequest()->isPost()) {
    		$formData = $_POST;
			if ($form->isValid($formData)) {
				
				//process form
				$academicLandscapeCourseDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view-detail','program-id'=>$program_id,'id'=>$academic_landscape_id),'default',true));
			}else{
				$form->populate($formData);
			}
    	}else{
    		if($id>0){
    			$form->populate($academicLandscapeCourseDB->getData($id));
    		}
    	}
    	$this->view->form = $form;
	}
    
	public function deleteCourseAction($id = 0){
    	$id = $this->_getParam('id', 0);
    	$aid = $this->_getParam('aid', 0);
    	$program_id = $this->_getParam('program-id', 0);
    	    	
    	if($id>0){
    		$academicLandscapeDB = new App_Model_Record_DbTable_AcademicLandscapeCourse();
    		$academicLandscapeDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'academic-landscape', 'action'=>'view-detail','id'=>$aid),'default',true));
    	
    }
}

