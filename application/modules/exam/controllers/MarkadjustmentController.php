<?php

class Exam_MarkadjustmentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

   	public function indexAction() {
		
    	$this->view->title="Mark Adjustment";
    
    	//get semester
    	$oSemester = new App_Model_Record_DbTable_Semester();
    	$semester_list = $oSemester->getData();
    	$this->view->semester = $semester_list;  
    	
   	
        $semester_id=0;
         if ($this->_request->isPost()) {         	
			 $semester_id= $this->getRequest()->getPost('semester_id');
			 $this->view->semester_id = $semester_id;

         }
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->selectSemProgram($semester_id);
    	$this->view->program = $program_list;  

        $program_id=0;
         if ($this->_request->isPost()) {         	
			 $program_id= $this->getRequest()->getPost('program_id');
			 $this->view->program_id = $program_id;
         }
         
        //get course thru academic landscpe  
    	$oCourses = new App_Model_Record_DbTable_AcademicLandscape();
        $course_list = $oCourses->selectCourseAcademicLandscape($program_id);
    	$this->view->courses = $course_list;
    	   	

	}
	
	
	public function viewAction(){
		
		$this->view->title="Mark Adjustment";
		
		 $semester_id = $this->_getParam('semester_id', 0);
		 $program_id  = $this->_getParam('program_id', 0);
		 $course_id = $this->_getParam('course_id', 0);
	  	 $this->view->course_id   = $course_id;
	  	 
	     if ($this->_request->isPost()) {         	
			 $course_id = $this->getRequest()->getPost('course_id');
			 $this->view->course_id = $course_id;
         }		
	    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->selectSemProgram($semester_id);
    	$this->view->program = $program_list;  
    	
    	         
        //get course thru academic landscpe  
    	$oCourses = new App_Model_Record_DbTable_AcademicLandscape();
        $course_list = $oCourses->selectCourseAcademicLandscape($program_id);
    	$this->view->courses = $course_list;
    	
    	
	    //get scr_id
	    $oSCRegistarion = new App_Model_Exam_DbTable_StudentCourseRegistration();
	    $student_list   = $oSCRegistarion->getByCourseSemester($semester_id,$course_id); 
    
       	$a1=0;
	  	$a2=0;
	  	$a3=0;
	  	$b1=0;
	  	$b2=0;
	  	$c=0;
	  	$d=0;
	  	$e=0;
	  	$f=0;
	  	
	  	$oGrade = new App_Model_Exam_DbTable_Grade();
	    //to check grade foreach student.
	    foreach($student_list as $sl){    	
       		 
       		 list($a1,$a2,$a3,$b1,$b2,$c,$d,$e,$f)=$oGrade->checkGrade($sl["student_id"],$program_id,$a1,$a2,$a3,$b1,$b2,$c,$d,$e,$f);       		
			
	    }//end foreach student
	   
	  /* $grades = array('a1' => $a1,
	   			       'a2' => $a2,
	  				   'a3' => $a3,
	   				   'b1' => $b1,
					   'b2' => $b2,
					   'c' => $c,
					   'd' => $d,
					   'e' => $e,
					   'f' => $f);
	   
	   $this->view->grades = $grades;	   */
	   
	  
	   $grades     = array($a1,$a2,$a3,$b1,$b2,$c,$d,$e,$f);
	   $this->view->grades = $grades;

	   //to check percentage
	   $percentage = array();
	   for($i=0; $i<count($grades); $i++){
	   		$number[$i] = round(($grades[$i]/count($student_list))*100);	   		
	   		$percentage[$i] = number_format($number[$i], 2, '.', '');
	   }
	  
	   $this->view->percentage = $percentage;
	 
	   
           
	}
	
	
	

}

