<?php
class Exam_ExamSlipController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->view->title = $this->view->translate("Exam Slip");
		
		$auth = Zend_Auth::getInstance(); 
    	$registration_id = $auth->getIdentity()->registration_id;
		
		//To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($registration_id);
    	
        //get program
        $programDb = new App_Model_General_DbTable_Program();
        $program = $programDb->fngetProgramData($student["IdProgram"]);
		
		//get current semester
		$semesterDb = new App_Model_General_DbTable_Semestermaster();
		$current_semester = $semesterDb->getCurrentSemester($program['IdScheme']);
		
		//get exam slip release status
		$ExamSlipReleaseDb = new App_Model_Exam_DbTable_ExamSlipRelease();
		if ($current_semester) 
			 $current_semester['release_data'] = $ExamSlipReleaseDb->getStudentExamSlipReleseDataPerYear($registration_id,$current_semester['idacadyear']);
		else 
			$current_semester['release_data']=array();
		$this->view->program=$student['IdProgram'];
		$this->view->semester=$current_semester['IdSemesterMaster'];
		$this->view->examslip = $current_semester;
				
	}
	
	public function printExamSlipAction(){
	
		$auth = Zend_Auth::getInstance();
		$registration_id = $auth->getIdentity()->registration_id;
		//$redirect="http://www.spmb.trisakti.ac.id/student-portal/exam/exam-slip";
		//$redirect=str_replace('/', '_', $redirect);
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
		//	$this->_redirect('http://www.print.trisakti.ac.id/student-portal/print-exam-slip/appl_id/0/registration_id/'.$registration_id.'/idsemester/'.$formData['semid'].'/redirectto/'.$redirect.'/examtype/'.$formData['ass_type']);
			
			$studentRegistrationDB = new App_Model_Record_DbTable_StudentRegistration();
			$studentdetails = $studentRegistrationDB->getStudentInfo($registration_id);
			
			//photo
			$documentDb = new App_Model_Application_DbTable_ApplicantUploadFile();
			$photo = $documentDb->getTxnFile($studentdetails['transaction_id'],51);
			$studentdetails['photo_raw'] = $photo;
	
			$fnImage = new icampus_Function_General_Image();
			$studentdetails['photo'] = $fnImage->getImagePath($photo['pathupload'],143,150);
				
			//academic advisor
			$staffMasterDb = new App_Model_General_DbTable_Staffmaster();
			$academicAdvisor['FullName'] = $studentdetails['AcademicAdvisor'];
			
			// semester
			$semesterDB = new App_Model_General_DbTable_Semestermaster();
			$semester = $semesterDB->fnGetSemestermaster($formData['semid']);
			
			// get course registed in semester selected
			$courseRegisterDb = new App_Model_Registration_DbTable_Studentregistration();
			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$formData['semid']);
				
			//find exam schedule
			$examStudentDb = new Exam_Model_DbTable_ExamGroupStudent();
			$haveExam = false;
			foreach ($courses as $index=>$course){
				$schedule = $examStudentDb->getExamGroupSchedule($registration_id,$formData['semid'],$course['IdSubject'],$formData['ass_type']);
				if($schedule){
					$courses[$index]['exam'] = $schedule[0];
					
					$haveExam = true;
				} else $courses[$index]['exam']=array('eg_date'=>'','eg_start_time'=>'','av_room_code'=>'');
			}
			foreach ($courses as $key => $row) {
				$egdate[$key]  = $row['exam']['eg_date'];
				$egtime[$key]  = $row['exam']['eg_start_time'];
			}
				
			// Sort the data with volume descending, edition ascending
			array_multisort($egdate, SORT_ASC, $egtime, SORT_ASC, $courses);
			//if(!$haveExam){
			//	throw new Exception("Student dont have exam schedule");
			//}
			
			
			
			//sort based on exam date & time
			usort($courses, array("Exam_ExamSlipController","cmp"));
			//$program
			$dbProgram=new App_Model_General_DbTable_Program();
			$program=$dbProgram->fngetProgramData($studentdetails['IdProgram']);
			$attendancemode=$program['Attendance_cal_mode'];
			//policy
			Global $attendance_policy;
			$attendancePolicyDb = new Exam_Model_DbTable_ExamSlipAttendancePolicy();
			$attendance_policy = $attendancePolicyDb->getDataByProgram($studentdetails['IdProgram'],$formData['ass_type']);
			$mode=$attendance_policy['mode'];
			//class group
			$courseGroupStudentDb = new GeneralSetup_Model_DbTable_CourseGroupStudent();
				
			//class attendance
			$courseGroupStudentAttendanceDb = new Exam_Model_DbTable_CourseGroupStudentAttendanceDetail();
			foreach ($courses as $index=>$course){
			
				$classGroup = $courseGroupStudentDb->checkStudentCourseGroup($registration_id,$formData['semid'],$course['IdSubject']);
				
				 
				if($classGroup){
				
					$classGroup['class_session'] = (int)$courseGroupStudentAttendanceDb->getAttendanceSessionCount($classGroup['IdCourseTaggingGroup'],$registration_id);
					//kehadiran dihitung dari hadir+ijin+sakit
					$hadir = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,395);
					$ijin  = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,396);
					$sakit = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,397);
					$dispensasi = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,1701);
						
					if ($mode=="1") $classGroup['class_attended'] = $hadir;
					if ($mode=="12") $classGroup['class_attended'] = $hadir+$sakit;
					if ($mode=="123" || trim($mode)=="") $classGroup['class_attended'] = $hadir+$ijin+$sakit;
					if ($mode=="200" || trim($mode)=="") $classGroup['class_attended'] = $hadir+$ijin+$sakit+$dispensasi;
						
					//$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$formData['sid'],395);
					
					if($classGroup['class_session']<=0){
						$classGroup['class_attendance_percentage'] = 100;
					}else{
						$classGroup['class_attendance_percentage'] = ($classGroup['class_attended']/$classGroup['class_session'] )*100;
					}
					
					$courses[$index]['class_group'] = $classGroup;
				}
			}
			
			
			//echo var_dump($courses);exit;			
			//assessment type
			$assessmentTypeDb = new App_Model_Exam_DbTable_Assessmenttype();
			$assessmentType = $assessmentTypeDb->fnGetAssesmentTypeNamebyID($formData['ass_type']);

			//program
			$programDb = new App_Model_Record_DbTable_Program();
			$program = $programDb->getData($studentdetails['IdProgram']);
			
			//faculty data
			$facultyDb = new App_Model_General_DbTable_Collegemaster();
			$faculty = $facultyDb->getData($program['IdCollege']);
			$faculty = $faculty[0];
			
			Global $semester_data;
			$semester_data = $semester;
				
			Global $faculty_data;
			$faculty_data = $faculty;
	
			Global $student_data;
			$student_data = $studentdetails;
				
			Global $academic_advisor;
			$academic_advisor = $academicAdvisor;
				
			Global $courses_data;
			$courses_data = $courses;
	
			Global $assessment_type;
			$assessment_type = $assessmentType;
			
			
			
			/*
			 * Payment checking
			 */
			
			$sfhelper= new icampus_Function_Studentfinance_PaymentInfo();
			
			$paymentstatus = 0;
			$pymtinfo=$sfhelper->getStudentPaymentInfo($registration_id,$formData['semid']);
			//echo var_dump($pymtinfo);exit;
			$dbGlobalException=new Exam_Model_DbTable_GlobalException();
			if ($dbGlobalException->isException(1, $program['IdCollege'], $formData['semid'], $formData['ass_type']))
				$paymentstatus = 1;
				else {
				if( ($pymtinfo["invoices"]=='')){
					$paymentstatus=1;
				}else{
					//check dah x dak balance
					if($pymtinfo["total_invoice_balance"]<=0 ){
						$paymentstatus=1;
					}else{
						$paymentstatus==0;
					}
				}
					
				//kalau ada payment exception utk course registration
				if ($pymtinfo['exception']!=array()) {
					if($pymtinfo["exception"][2]){
						$paymentstatus=1;
					}
				}
			}
			if($paymentstatus==0){
				try {
					throw new Exception("Outstanding Payment");
				} catch (Exception $e) {
					echo $e->getMessage();
					exit;
				}
				
			}
				
			/*
			 * PDF Generation
			*/
				
			require_once 'dompdf_config.inc.php';
				
			$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
			$autoloader->pushAutoloader('DOMPDF_autoload');
				
			$html_template_path = DOCUMENT_PATH."/template/ExamSlip.html";
				
			$html = file_get_contents($html_template_path);
				
			//echo $html;
			//exit;
			$dompdf = new DOMPDF();
			$dompdf->load_html($html);
			$dompdf->set_paper('a4', 'portrait');
			@$dompdf->render();
				
				
			@$dompdf->stream("ExamSlip_".$registration_id."_".date('Ymd_Hi').".pdf");
	
		}
	
		exit;
	}
	
	function cmp($a, $b){
		if(!isset($a['exam']['eg_date']) || !isset($b['exam']['eg_date']) ){
			return 0;
		}
		
		if ( date('Y/m/d H:i:s', strtotime($a['exam']['eg_date']." ".$a['exam']['eg_start_time'])) == date('Y/m/d H:i:s', strtotime($b['exam']['eg_date']." ".$a['exam']['eg_start_time']))) {
			return 0;
		}
		return (date('Y/m/d H:i:s', strtotime($a['exam']['eg_date']." ".$a['exam']['eg_start_time'])) < date('Y/m/d H:i:s', strtotime($b['exam']['eg_date']." ".$a['exam']['eg_start_time']))) ? -1 : 1;
	}
	
	public function printExamSlipQrAction(){
	
		$auth = Zend_Auth::getInstance();
		$registration_id = $auth->getIdentity()->registration_id;
		//$redirect="http://www.spmb.trisakti.ac.id/student-portal/exam/exam-slip";
		//$redirect=str_replace('/', '_', $redirect);
		
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
			//$this->_redirect('http://www.print.trisakti.ac.id/student-portal/print-exam-slip-qr/appl_id/0/registration_id/'.$registration_id.'/idsemester/'.$formData['semid'].'/redirectto/'.$redirect.'/examtype/'.$formData['ass_type']);
				
			$studentRegistrationDB = new App_Model_Record_DbTable_StudentRegistration();
			$studentdetails = $studentRegistrationDB->getStudentInfo($registration_id);
				
			//photo
			$documentDb = new App_Model_Application_DbTable_ApplicantUploadFile();
			$photo = $documentDb->getTxnFile($studentdetails['transaction_id'],51);
			$studentdetails['photo_raw'] = $photo;
	
			$fnImage = new icampus_Function_General_Image();
			$studentdetails['photo'] = $fnImage->getImagePath($photo['pathupload'],143,150);
	
			//academic advisor
			$staffMasterDb = new App_Model_General_DbTable_Staffmaster();
			$academicAdvisor['FullName'] = $studentdetails['AcademicAdvisor'];
				
			// semester
			$semesterDB = new App_Model_General_DbTable_Semestermaster();
			$semester = $semesterDB->fnGetSemestermaster($formData['semid']);
				
			// get course registed in semester selected
			$courseRegisterDb = new App_Model_Registration_DbTable_Studentregistration();
			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$formData['semid']);
	
			//find exam schedule
			$examStudentDb = new Exam_Model_DbTable_ExamGroupStudent();
			$haveExam = false;
			foreach ($courses as $index=>$course){
				$schedule = $examStudentDb->getExamGroupSchedule($registration_id,$formData['semid'],$course['IdSubject'],$formData['ass_type']);
				if($schedule){
					$courses[$index]['exam'] = $schedule[0];
					//generate png QR
					$data=array('nim'=>$schedule[0]['egst_student_nim'],
							'exam'=>$schedule[0]['eg_assessment_type'],
							'groupid'=>$schedule[0]['egst_group_id'],
							'egst_id'=>$schedule[0]['egst_id'],
							'SubjectCode'=>$course['SubCode']
					);
					$token=md5($schedule[0]['egst_group_id'].$schedule[0]['egst_student_nim']);
					$examStudentDb->update(array('token'=>$token), 'egst_id='.$schedule[0]['egst_id']);
					$string='www.sismob.trisakti.ac.id/examination/attendance/student/id/'.$schedule[0]['egst_id'].'/tokenqr/'.$token;
					$path=$this->generateQr($data, $string);
					$courses[$index]['exam']['qrpath']=$path;
					$examStudentDb->update(array('qr_path'=>$path), 'egst_id='.$schedule[0]['egst_id']);
					//========================
					$haveExam = true;
				} else $courses[$index]['exam']=array('eg_date'=>'','eg_start_time'=>'','av_room_code'=>'');
			}
			foreach ($courses as $key => $row) {
				$egdate[$key]  = $row['exam']['eg_date'];
				$egtime[$key]  = $row['exam']['eg_start_time'];
			}
	
			// Sort the data with volume descending, edition ascending
			array_multisort($egdate, SORT_ASC, $egtime, SORT_ASC, $courses);
			//if(!$haveExam){
			//	throw new Exception("Student dont have exam schedule");
			//}
				
				
				
			//sort based on exam date & time
			usort($courses, array("Exam_ExamSlipController","cmp"));
			//$program
			$dbProgram=new App_Model_General_DbTable_Program();
			$program=$dbProgram->fngetProgramData($studentdetails['IdProgram']);
			$attendancemode=$program['Attendance_cal_mode'];
			//policy
			Global $attendance_policy;
			$attendancePolicyDb = new Exam_Model_DbTable_ExamSlipAttendancePolicy();
			$attendance_policy = $attendancePolicyDb->getDataByProgram($studentdetails['IdProgram'],$formData['ass_type']);
			$mode=$attendance_policy['mode'];
			//class group
			$courseGroupStudentDb = new GeneralSetup_Model_DbTable_CourseGroupStudent();
	
			//class attendance
			$courseGroupStudentAttendanceDb = new Exam_Model_DbTable_CourseGroupStudentAttendanceDetail();
			foreach ($courses as $index=>$course){
					
				$classGroup = $courseGroupStudentDb->checkStudentCourseGroup($registration_id,$formData['semid'],$course['IdSubject']);
	
					
				if($classGroup){
	
					$classGroup['class_session'] = (int)$courseGroupStudentAttendanceDb->getAttendanceSessionCount($classGroup['IdCourseTaggingGroup'],$registration_id);
					//kehadiran dihitung dari hadir+ijin+sakit
					$hadir = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,395);
					$ijin  = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,396);
					$sakit = (int)$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$registration_id,397);
	
					if ($mode=="1") $classGroup['class_attended'] = $hadir;
					if ($mode=="12") $classGroup['class_attended'] = $hadir+$sakit;
					if ($mode=="123" || trim($mode)=="") $classGroup['class_attended'] = $hadir+$ijin+$sakit;
	
					//$courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$formData['sid'],395);
						
					if($classGroup['class_session']<=0){
						$classGroup['class_attendance_percentage'] = 100;
					}else{
						$classGroup['class_attendance_percentage'] = ($classGroup['class_attended']/$classGroup['class_session'] )*100;
					}
						
					$courses[$index]['class_group'] = $classGroup;
				}
			}
				
				
			//echo var_dump($courses);exit;
			//assessment type
			$assessmentTypeDb = new Exam_Model_DbTable_Assessmenttype();
			$assessmentType = $assessmentTypeDb->fnGetAssesmentTypeNamebyID($formData['ass_type']);
	
			//program
			$programDb = new App_Model_Record_DbTable_Program();
			$program = $programDb->getData($studentdetails['IdProgram']);
				
			//faculty data
			$facultyDb = new App_Model_General_DbTable_Collegemaster();
			$faculty = $facultyDb->getData($program['IdCollege']);
			$faculty = $faculty[0];
				
			Global $semester_data;
			$semester_data = $semester;
	
			Global $faculty_data;
			$faculty_data = $faculty;
	
			Global $student_data;
			$student_data = $studentdetails;
	
			Global $academic_advisor;
			$academic_advisor = $academicAdvisor;
	
			Global $courses_data;
			$courses_data = $courses;
	
			Global $assessment_type;
			$assessment_type = $assessmentType[0];
				
				
				
			/*
			 * Payment checking
			*/
				
			
			$sfhelper= new icampus_Function_Studentfinance_PaymentInfo();

			$paymentstatus = 0;
			$dbGlobalException=new Exam_Model_DbTable_GlobalException();
			if ($dbGlobalException->isException(1, $program['IdCollege'], $formData['semid'], $formData['ass_type']))
				$paymentstatus = 1;
			else {
				$pymtinfo=$sfhelper->getStudentPaymentInfo($registration_id,$formData['semid']);
				//echo var_dump($pymtinfo);exit;
				if( ($pymtinfo["invoices"]=='')){
					$paymentstatus=1;
				}else{
					//check dah x dak balance
					if($pymtinfo["total_invoice_balance"]<=0 ){
						$paymentstatus=1;
					}else{
						$paymentstatus==0;
					}
				}
				 
				//kalau ada payment exception utk course registration
				if ($pymtinfo['exception']!=array()) {
					if($pymtinfo["exception"][2]){
						$paymentstatus=1;
					}
				}
			}
			if($paymentstatus==0){
				try {
					throw new Exception("Outstanding Payment");
				} catch (Exception $e) {
					echo $e->getMessage();
					exit;
				}
	
			}
	
			/*
			 * PDF Generation
			*/
	
			require_once 'dompdf_config.inc.php';
	
			$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
			$autoloader->pushAutoloader('DOMPDF_autoload');
	
			$html_template_path = DOCUMENT_PATH."/template/ExamSlipQr.html";
	
			$html = file_get_contents($html_template_path);
	
			//echo $html;
			//exit;
			$dompdf = new DOMPDF();
			$dompdf->load_html($html);
			$dompdf->set_paper('a4', 'portrait');
			@$dompdf->render();
	
	
			@$dompdf->stream("ExamSlip_".$registration_id."_".date('Ymd_Hi').".pdf");
	
		}
	
		exit;
	}
	public function generateQr($data,$string) {
	
		//include('../lib/full/qrlib.php');
		//include('config.php');
		require_once 'qrlib.php';
		require_once 'qrconfig.php';
		// how to save PNG codes to server
		//directory to locate file
		$app_directory_path = DOCUMENT_PATH."/student/".date('Y')."/".$data['nim']."/".$data['exam'];
		 
		//create directory to locate file
		if (!is_dir($app_directory_path)) {
			mkdir($app_directory_path, 0775,true);
		}
		 
		$output_directory_path = DOCUMENT_PATH."/student/".date('Y')."/".$data['nim']."/exam/".$data['groupid'];
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		$tempDir = $output_directory_path;
	
		$codeContents = $string;
	
		// we need to generate filename somehow,
		// with md5 or with database ID used to obtains $codeContents...
		$fileName = $data['SubjectCode'].md5($codeContents).'.png';
	
		$pngAbsoluteFilePath = $tempDir."/".$fileName;
		//$urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
	
		// generating
		if (!file_exists($pngAbsoluteFilePath)) {
			require_once 'qrconfig.php';
	
			//$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
			//$autoloader->pushAutoloader('QRcode_autoload');
			QRcode::png($codeContents, $pngAbsoluteFilePath);
			$dbExamGrpStd=new Exam_Model_DbTable_ExamGroupStudent();
			$dbExamGrpStd->update(array('qr_path'=>$pngAbsoluteFilePath), 'egst_id='.$data['egst_id']);
		 	
		}
		return $pngAbsoluteFilePath;
	
	}
}
?>