<?php
class Application_SelectionHighschoolReportController extends Zend_Controller_Action
{

	public function reportListAction(){
	
		$this->view->title = $this->view->translate("report list");
				
		
    	
	} 
	
	public function reportStudentViewAction(){
	
		$this->view->title = $this->view->translate("pengumuman hasil seleksi");
				
		//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData();
    	$this->view->period = $period;
    	
    	//get program
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program = $programDB->getData();
    	$this->view->program = $program;
    	
    	$form = new Application_Form_HighSchoolSelectionSearch();
    	$this->view->form=$form;    	
    	
	} 
	
	public function printStudentViewAction(){
	
		$this->_helper->layout->setLayout('print');
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_info = $periodDB->getData($period);
    	$fperiod = $period_info["ap_code"];
    	$this->view->period_name = $period_info["ap_desc"];
    	
    	$condition=array('admission_type'=>2,
							"academic_year"=>$academic_year,
							"period"=>$fperiod,
							"program_code"=>$program_code,
							'status'=>'OFFER',
			 				);
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getResultSelection($condition);	
		$this->view->applicant = $applicant_data;
		
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year_info=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year_info;
    	
	} 
	
	public function printDeanViewAction(){
	
		$this->_helper->layout->setLayout('print');
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_info = $periodDB->getData($period);
    	$fperiod = $period_info["ap_code"];
    	$this->view->period_name = $period_info["ap_desc"];
    	
    	$condition=array('admission_type'=>2,
							"academic_year"=>$academic_year,
							"period"=>$fperiod,
							"program_code"=>$program_code,
							'status'=>'OFFER',
			 				);
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getResultSelection($condition);	
		$this->view->applicant = $applicant_data;
		
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year_info=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year_info;
    	
	} 
	
	public function raportPssbAction(){
	
		$this->view->title = $this->view->translate("Hasil Seleksi PSSB");	
    	
    	$form = new Application_Form_SearchRaportPssb();
    	$this->view->form=$form;    	
    	
	} 
	
	
	public function printRaportPssbAction(){
		
				
		$this->_helper->layout->setLayout('print');
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		$faculty_id = $this->_getParam('faculty', 0);
				
		$collegeDB = new App_Model_General_DbTable_Collegemaster();
		$faculty = $collegeDB->getData($faculty_id);
		
			if($locale=="id_ID"){
				$college_name = $faculty["ArabicName"];				
			}elseif($locale=="en_US"){
				$college_name = $faculty["CollegeName"];				
			}
			
		$this->view->faculty_name = $college_name;
	
		
		//get program based on faculty    	
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition = array("IdCollege"=>$faculty_id);    	
    	$program = $programDB->searchAllProgram($condition);
    	$this->view->program_list = $program;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period_id = $this->_getParam('period', 0);
    	$this->view->period_id = $period_id;
    	
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period_id);
    	    
    	setlocale (LC_ALL, $locale);
    	$this->view->bulan = strftime('%B', mktime(null, null, null, $period["ap_month"], 01));
    	    	
    	$this->view->periode = $period["ap_desc"];
    	
	}
	
	
	
}
?>