<?php
/*
 *  @author Suliana
 *  @created July 14, 2011
 */

class AdmissionRecord_CreditTransferController extends Zend_Controller_Action {
	
	
	
	public function indexAction(){
		//title
		$this->view->title = "Credit Transfer (Admin)";
		
		$studentDB = new App_Model_Record_DbTable_CreditTransfer();
		
		//program
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->programList = $programDB->getActiveProgram();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->searchAdmin($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateDataAdmin();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
	}
	
	
	
	public function transferAction(){
		//title
		$this->view->title = "Student Credit Transfer";
		
		$student_id = $this->_getParam('stud_id', 0);
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		       
		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		//get current semester    	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;     	
    	//get list semester		
		$this->view->semesterlist = $semesterDB->getData();
		

		foreach ($this->view->semesterlist as $sem)  {
			$sems = $semester["id"];
		}
    	
    	//get selection semester   
    	if ($this->getRequest()->isPost()) {	
    		$semester_id = $this->getRequest()->getPost('semester_id');  
    		$select_semester_id = $semester_id;    	  	
    	}else{
    		$select_semester_id = $sems;
    	}    	
    	$this->view->semester_id= $select_semester_id;
    	$this->view->semesterName = $semesterDB->getData($select_semester_id);
    	
    	    
		$transferDB = new App_Model_Record_DbTable_CreditTransfer();
		$transfer = $transferDB->getList($student_id);
		
		$this->view->transfer = $transfer;
		
		$this->view->landscape = $landscape_id;
    	 
	}
		
	
	public function viewAction(){
		
		$this->view->title = "Student Credit Transfer - Completed";
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_id = $this->_getParam('student_id', 0);
			$semester_id = $this->_getParam('semester_id', 0);
			$courseTransfer = $this->getRequest()->getPost('selTransfer');  
			$courseID= $this->getRequest()->getPost('courseID');  
			
			$transferDB = new App_Model_Record_DbTable_CreditTransfer();
			$n = count($courseTransfer);

			$i=0;
			while ($i<$n) {
//				echo $courseTransfer[$i];
//				echo " + ";
//				$courseID = $courseID[$i];
//				echo "<hr>";
				
				$data = array(
					'status' => $courseTransfer[$i],
					'date_approved' => date('Y-m-d H:i:a')
					);	
						
				//update status credit transfer application					
				$transfer = $transferDB->updateData($data,$student_id,$semester_id,$courseID[$i]);		
				
				$i++;
			}
			
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$student = $studentDB->getStudent($student_id);
			$this->view->student = $student;
			
			$programDB = new App_Model_Record_DbTable_Program();
			$this->view->program = $programDB->getData($student['program_id']);		
			
			$semesterDB = new App_Model_Record_DbTable_Semester();		
			//get current semester    	
	    	$semester = $semesterDB->currentSemester();    	
	    	$this->view->currentsemester = $semester;     	
	    	//get list semester		
	    	
	    	//get selection semester   
	    	if ($this->getRequest()->isPost()) {	
	    		$semester_id = $this->getRequest()->getPost('semester_id');  
	    		$this->view->semester_id= $semester_id;
    			$this->view->semesterName = $semesterDB->getData($semester_id);
    			  	
	    	}
			
    		
    		$viewTransfer = $transferDB->getList($student_id);
    		$this->view->creditTransfer= $viewTransfer;
			
			//redirect 
//		    $this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'registration', 'action'=>'receipt','student_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
		
			
		}//end if
	}
	
	public function indexStudentAction(){
		//title
		$this->view->title = "Credit Transfer (Student)";
		
		$studentDB = new App_Model_Record_DbTable_Student();
		
		//program
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->programList = $programDB->getActiveProgram();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->search($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
	}
	
	public function transferStudentAction(){
		//title
		$this->view->title = "Student Credit Transfer";
		
		$student_id = $this->_getParam('stud_id', 0);
		$this->view->student_id= $student_id;
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		       
		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		//get current semester    	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;     	
    	//get list semester		
		$this->view->semesterlist = $semesterDB->getData();
		

		foreach ($this->view->semesterlist as $sem)  {
			$sems = $semester["id"];
		}
    	
    	//get selection semester   
    	if ($this->getRequest()->isPost()) {	
    		$semester_id = $this->getRequest()->getPost('semester_id');  
    		$select_semester_id = $semester_id;    	  	
    	}else{
    		$select_semester_id = $sems;
    	}    	
    	$this->view->semester_id= $select_semester_id;
    	$this->view->semesterName = $semesterDB->getData($select_semester_id);
    	
    	    
		$academicDB = new App_Model_Record_DbTable_AcademicLandscape();
		$landscape = $academicDB->getAcademicLandscape($landscape_id);
		
		$this->view->landscape = $landscape;
		
	}
	
	public function viewTransferAction(){
		
		$this->view->title = "Student Credit Transfer - Completed";
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_id = $this->_getParam('student_id', 0);
			$semester_id = $this->_getParam('semester_id', 0);
			$courseTransfer = $this->getRequest()->getPost('courseTransfer');  
			
			$transferDB = new App_Model_Record_DbTable_CreditTransfer();

			$n = count($courseTransfer);

			$i=0;
			while ($i<$n) {
				$course = $courseTransfer[$i];
				
				$data = array(
							'student_id' => $student_id,
							'semester_id' => $semester_id,	
							'course_id' => $courseTransfer[$i],
							'status' => 0,
							'date' => date('Y-m-d H:i:a')
							);		
							
				$checkTransfer = $transferDB->checkTransfer($student_id,$course,$semester_id);
				
				if (!$checkTransfer["id"]) {
					$transfer = $transferDB->addData($data);
				}
				
				$i++;
			}
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$student = $studentDB->getStudent($student_id);
			$this->view->student = $student;
			
			$programDB = new App_Model_Record_DbTable_Program();
			$this->view->program = $programDB->getData($student['program_id']);		
			
			$semesterDB = new App_Model_Record_DbTable_Semester();		
			//get current semester    	
	    	$semester = $semesterDB->currentSemester();    	
	    	$this->view->currentsemester = $semester;     	
	    	//get list semester		
	    	
	    	//get selection semester   
	    	if ($this->getRequest()->isPost()) {	
	    		$semester_id = $this->getRequest()->getPost('semester_id');  
	    		$this->view->semester_id= $semester_id;
    			$this->view->semesterName = $semesterDB->getData($semester_id);
    			  	
	    	}
			
    		
    		$viewTransfer = $transferDB->getList($student_id);
    		$this->view->creditTransfer= $viewTransfer;
			
			//redirect 
//		    $this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'registration', 'action'=>'receipt','student_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
		
			
		}//end if
	}
	
}
	
		
?>