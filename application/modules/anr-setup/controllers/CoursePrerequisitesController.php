<?php
/**
 *
 * @author Muhamad Alif Muhammad
 * @version 1.00 2010/10/05
 * Meteor Technology & Consultancy SDN BHD
 */

class AnrSetup_CoursePrerequisitesController extends Zend_Controller_Action {
	
	public function indexAction(){
		//title
    	$this->view->title="Course Prerequisites Setup";
    	
    	//course list
	    $courseDb = new App_Model_Record_DbTable_Course();
	    	
    	//search options
    	$search_name = $this->_getParam('name', null);
    	$this->view->search_name = $search_name;
    	
    	$search_code = $this->_getParam('code', null);
    	$this->view->search_code = $search_code;
    	
    	//process
    	if ($this->getRequest()->isPost()) {
	    	$courselist = $courseDb->search($search_name, $search_code);
    	}else{
	    	$courselist = $courseDb->getData();
    	}
    	
    	$course_complete = array();
    	$i=0; 
    	foreach ($courselist as $course){
    			$course_prerequisitesDb = new App_Model_Record_DbTable_CoursePrerequisites();
    			
    			$course_complete[$i]['detail'] = $course;
    			$course_complete[$i]['prerequisites'] = $course_prerequisitesDb->getCourseData($course['id']);
    			$i++;
    	}
    	
    	$this->view->courses = $course_complete;
	}
	
	public function viewAction(){

		if ($this->getRequest()->isPost()) {
			$formData = $_POST;
			
			$prerequisitesDB = new App_Model_Record_DbTable_CoursePrerequisites();
			$prerequisitesDB->deleteCourseData($formData['courseid']);
			
			$courses = explode(",", $formData['precourse']);
			
			foreach ($courses as $course){
				$prerequisitesDB->addData(array('course_id'=>$formData['courseid'],'required_course_id'=>$course));
			}
			
			//redirect
			$this->view->noticeSuccess = "Successfully Updated";
			$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'course-prerequisites', 'action'=>'index'),'default',true));
		}else{
			//title
	    	$this->view->title="Course Prerequisites Setup";
	    	
	    	$courseid = $this->_getParam('course', 0);
	    	$this->view->courseid = $courseid;
	    	
	    	if($courseid==0){
	    		$this->view->noticeError = "Unknown Course";
	    	}else{
	    		//get course details
	    		$courseDB = new App_Model_Record_DbTable_Course();
	    		$this->view->course = $courseDB->getData($courseid);
	    		
	    		//prerequisites courses
	    		$coursePrerequisitesDB = new App_Model_Record_DbTable_CoursePrerequisites();
	    		$this->view->courseprerequisites = $coursePrerequisitesDB->getCourseData($courseid);
	    		
	    		//all courses
	    		$this->view->courselist = $coursePrerequisitesDB->getCourseList($courseid);
	    	}
		}
    	
	}
}
?>