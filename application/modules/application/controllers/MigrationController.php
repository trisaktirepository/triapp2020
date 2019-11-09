<?php
class Application_MigrationController extends Zend_Controller_Action
{

	public function indexAction(){
		//title
    	$this->view->title="Migration Applicant to Student";
    	
    	
    	//search options
    	$search_id = $this->_getParam('id', null);
    	$this->view->search_id = $search_id;
    	
    	$search_id_type = $this->_getParam('id_type', 0);
    	$this->view->search_id_type = $search_id_type;
    	
    	$search_name = $this->_getParam('name', null);
    	$this->view->search_name = $search_name;
    	
		$search_program = $this->_getParam('program', null);
    	$this->view->search_program = $search_program;
    	
    	
		//program
		$programDb = new App_Model_Record_DbTable_Program();
		$programlist = $programDb->getData();
		$this->view->programlist = $programlist;
	
    	
    	
    	//process
    	$applicantDb = new App_Model_Application_DbTable_Applicant();
    	if ($this->getRequest()->isPost()) {
    		
	    	$applicantList = $applicantDb->search($search_name, $search_id, $search_id_type, $search_program);
	    	$this->view->applicant = $applicantList;
    	}
//    	}else{
//	    	$applicantList = $applicantDb->getData();
//    	}
		
    	
		
    	
	} 
	
	public function viewAction(){

		if ($this->getRequest()->isPost()) {
			$formData = $_POST;
			
		}else{
			//title
	    	$this->view->title="Application Migration";
	    	
	    	$id = $this->_getParam('id', 0);
	    	$this->view->id = $id;
	    	
	    	if($id==0){
	    		$this->view->noticeError = "Unknown Applicant";
	    	}else{
	    		//get course details
	    		$applicantDB = new App_Model_Application_DbTable_Applicant();
	    		$this->view->applicant = $applicantDB->getApplicantMigrate($id);
	    		
	    		//get branch list
	    		$branchDB = new App_Model_General_DbTable_Branch();
	    		$this->view->branchlist = $branchDB->getData();
	    		
	    		//get intake list
	    		$intakeDB = new App_Model_Record_DbTable_Intake();
	    		$this->view->intakelist = $intakeDB->getIntake(); 
	    		
	    		//get entry requirement
	    		$entryDB = new App_Model_Application_DbTable_ApplicantEntry();
	    		$this->view->entry = $entryDB->getEntry($id); 
	    	}
		}
	}
	
	public function migrateAction(){
		
		//title
		$this->view->title = "Student Registration Info";
		
		if ($this->getRequest()->isPost()) {
			$formData = $_POST;
			
			echo $id = $this->_getParam('id', 0);

			//get applicant data
			$applicantDB = new App_Model_Application_DbTable_Applicant();
			$applicant_data = $applicantDB->getData($id);
			
			//generate matrix number
			$matrix_no = $this->generatematrix($applicant_data,$formData); 
			$prog_id = $applicant_data['ARD_PROGRAM'];
			
			//get current academic landscape
			$landscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
			$landscape = $landscapeDB->getActiveProgramLandscape($prog_id);
			
			//insert student data
			$data = array(
						'matric_no'=>$matrix_no,
						'ic_no'=>$applicant_data['ARD_IC'],
						'passport_no'=>$applicant_data['ARD_IC'],
						'application_id'=>$id,
						'program_id'=>$applicant_data['ARD_PROGRAM'],
						'landscape_id'=>$landscape['id'],
						'admission_date'=>date('Y-m-d'),
						'admission_intake_id'=>$formData['intake_id'],
						'graduation_date'=>null,
						'student_status_id'=>1,
						'fullname'=>$applicant_data['ARD_NAME'],
						'firstname'=>'',
						'lastname'=>'',
						//'date_of_birth'=>$applicant_data['ARD_DOB'],
						'gender'=>$applicant_data['ARD_SEX'],
						'nationality'=>$applicant_data['ARD_CITIZEN'],
						'race'=>$applicant_data['ARD_RACE'],
						'email'=>$applicant_data['ARD_EMAIL'],
						'last_changes'=>date('Y-m-d')
					);
					
			$studentDB = new App_Model_Record_DbTable_Student();
			$student_id = $studentDB->addStudent($data); 
						
			
			$applicant_data = $applicantDB->updateStatus($id);
			
			
			//TODO: insert student contact and address
			/*$address = array(
						'student_id'=>'',
						'address_type_id'=>'',
						'address1'=>'',
						'address2'=>'',
						'city'=>'',
						'state_id'=>'',
						'country_id'=>'',
						'postcode'=>'',
						);*/

			//TODO: update applicant status to REGISTERED
			
			$this->view->student_data = $studentDB->getStudent($student_id);

			$this->view->noticeSuccess = "Success Creating New Student";
		}else{
			//redirect
			$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'registration', 'action'=>'index'),'default',true));
		}
	}
	
	public function migrateallAction(){
		//title
    	$this->view->title="Migrate All Offered";
    	
    	
    	//search options
    	$search_id = $this->_getParam('id', null);
    	$this->view->search_id = $search_id;
    	
    	$search_id_type = $this->_getParam('id_type', 0);
    	$this->view->search_id_type = $search_id_type;
    	
    	$search_name = $this->_getParam('name', null);
    	$this->view->search_name = $search_name;
    	
		$search_program = $this->_getParam('program', null);
    	$this->view->search_program = $search_program;
    	
    	
		//program
		$programDb = new App_Model_Record_DbTable_Program();
		$programlist = $programDb->getData();
		$this->view->programlist = $programlist;
	
    	
    	
    	//process
    	$applicantDb = new App_Model_Application_DbTable_Applicant();
    	if ($this->getRequest()->isPost()) {
    		
	    	$applicantList = $applicantDb->search($search_name, $search_id, $search_id_type, $search_program);
			
    	}else{
	    	$applicantList = $applicantDb->getData();
    	}
		$this->view->applicant = $applicantList;
    	
		
    	
	} 
	
	private function generatematrix($applicant_data,$formData){
		$matrix = "";
		
		//TODO:check award(1 digits)
		$matrix = $applicant_data['ARD_PROGRAM'];
		
		//TODO:check gender (1 digits)
		if($applicant_data['ARD_SEX']=="F"){
			$matrix .= "1";	
		}else{
			$matrix .= "2";
		}
		
		//TODO: check branch (2 digits)
		$matrix .= $formData['branch_id'];
		
		//TODO: check intake (1 digits)
		$matrix .= $formData['intake_id'];
		
		//TODO: check year (2 digits)
		$matrix .= date('Y');
		
		//TODO: seq number (5 digits)
		$matrixDB = new AdmissionRecord_Model_DbTable_Matrix();
		$matrix .= $matrixDB->getSeq();
		
		return $matrix;		
	}
	
	private function generatematrix2($applicant_data,$formData){
		$matrix = "";
		echo "<hr>";
		//TODO:check award
//		echo "".$matrix = $applicant_data['ARD_PROGRAM'];
		echo "award = ".$applicant_data['ARD_AWARD'];
		$matrix = $applicant_data['ARD_AWARD'];
		echo "<br>";
		//TODO:check gender
		if($applicant_data['ARD_SEX']=="F"){
			echo "gender = 1";	
			$matrix .= "1";	
		}else{
			echo "gender = 2";	
			$matrix .= "2";
		}
		
		echo "<br>";
		//TODO: check branch
		echo "branch = ".$formData['branch_id'];
		$matrix .= $formData['branch_id'];
		echo "<br>";
		//TODO: check intake
		echo "sem intake = ".$formData['intake_id'];
		$matrix .= $formData['intake_id'];
		echo "<br>";
		//TODO: check year
		echo "year intake = ".date('Y');
		$matrix .= date('Y');
		echo "<br>";
		//TODO: seq number
		$matrixDB = new AdmissionRecord_Model_DbTable_Matrix();
		echo "seq= ".$matrixDB->getSeq();
		$matrix .= $matrixDB->getSeq();
		
		
		
		return $matrix;		
	}
}
?>