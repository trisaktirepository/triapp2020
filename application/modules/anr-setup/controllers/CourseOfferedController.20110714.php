<?php

class AnrSetup_CourseOfferedController extends Zend_Controller_Action
{
	
    public function indexAction()
    {
        $this->view->title = "Course Offered";
        
        /*view filter data*/
        $semesterID = $this->_getParam('semesterID', 0);
        $this->view->semesterID = $this->_getParam('semesterID', 0);
        
        /*Semester Data*/
        $semesterDB = new App_Model_Record_DbTable_Semester();
        $semesterList = $semesterDB->getData();
        $this->view->semesterlist = $semesterList;
        
        
        /*Course Offered*/
        $course_offeredDB = new App_Model_Record_DbTable_CourseOffered();
        if($semesterID!=0){
        	
        	$courseList = $course_offeredDB->getDataBySemester($semesterID,$departmentID);
        	$this->view->courseList = $courseList;
        }
        
        //no course offered data notification
        if((!isset($this->view->courseList)) && $semesterID !=0){
        	$this->view->noticeMessage = "There are no course offer for this semester. <a href='".$this->view->url(array('module'=>'anr-setup','controller'=>'course-offered', 'action'=>'add', 'semester-id'=>$this->view->semesterID))."'>Click here to add</a>";
        }
    }

	
    public function addAction(){
    	$this->view->title = "Add Course Offered";
    	
    	//semester data
    	$semesterID = $this->_getParam('semester-id', 0);
        $this->view->semesterID = $this->_getParam('semester-id', 0);
        
    	$semesterDB = new App_Model_Record_DbTable_Semester();
    	$semester = $semesterDB->getData($semesterID);
    	$this->view->semester = $semester;
    	
    	//Department Data
    	$departmentDB = new App_Model_General_DbTable_Department();
        $departmnetList = $departmentDB->getData();
        $this->view->departmentList = $departmnetList;
    	
    	//course Data
    	$departmentID = $this->_getParam('departmentID', 0);
    	$this->view->departmentID = $departmentID;
    	$courseDB = new App_Model_Record_DbTable_Course();
    	$courseList = $courseDB->getFacultyCourse($departmentID);
    	    	
    	//course offered
    	$courseOfferedDB = new App_Model_Record_DbTable_CourseOffered();
    	$courseoffered = $courseOfferedDB->getDataBySemester($semesterID);
    	$this->view->courseoffered = $courseoffered;
    	

    	//different
    	$courseClean = array();
    	$i=0;
    	if($courseList!=null && empty($courseList)){
	    	foreach ($courseList as $c1){
	    		foreach ($courseoffered as $c2){
	    			if($c1['id'] == $c2['course_id']){
	    				unset($courseList[$i]);
	    				break;
	    			}		
	    		}
	    		$i++;
	    	}
    	}
    	$this->view->courseList = $courseList;
    	
			    	    	    	
    	$form = new AnrSetup_Form_CourseOffered();
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$selCourse = explode(",", $formData['selProgram']);
						    	
				//add course-offered
				//del all
				$courseOfferedDB->deleteCourseOffered($semesterID);
				//add all
				foreach($selCourse as $course){
					$courseOfferedDB->addCourseOffered($semesterID,$course);
				}
							
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'course-offered', 'action'=>'index','semesterID'=>$semesterID),'default',true));		
			}else{
				echo "error";
				$form->populate($formData);
			}
    	}
    	
    	$this->view->form = $form;
    }
}

