<?php 

class Finalassignment_ExaminationController extends Zend_Controller_Action { 
	
	private $dbProposal;
	private $dbTaApplication;
	private $dbProgram;
	private $dbExamination;
	private $dbSupervisorMaster;
	private $dbsupervisor;
	private $dbStaff;
	private $dbApproval;
	private $dbExamCompo;
	private $dbflow;
	
	public function init() { //initialization function
	//	$this->_gobjlog = Zend_Registry::get('log'); //instantiate log object
		$this->fnsetObj();
	}
	
	
	public function fnsetObj() {
		$this->dbflow=new Finalassignment_Model_DbTable_Flow();
		$this->dbExamCompo=new Finalassignment_Model_DbTable_ExaminationComposition();
		$this->dbApproval=new Finalassignment_Model_DbTable_Approval();
		$this->dbsupervisor=new Finalassignment_Model_DbTable_Supervisor();
		$this->dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->dbReqStudent=new Finalassignment_Model_DbTable_DocRequisiteStudent();
		$this->dbExamination=new Finalassignment_Model_DbTable_Examination();
		$this->dbProposal=new Finalassignment_Model_DbTable_Proposal();
		$this->dbTaApplication=new Finalassignment_Model_DbTable_Application();
		$this->dbSupervisorMaster=new Finalassignment_Model_DbTable_Supervisormaster();
		
	}
	
	public function examApplicationAction(){
		$this->view->title = $this->view->translate("Propose Examination");
		
		$proposedBy = $this->_getParam('proposedby', null);
		$this->view->proposedBy=$proposedBy;
		$idtaapplication= $this->_getParam('idtaapplication', null);
		$this->view->idtaapplication=$idtaapplication;
		if ($this->getRequest()->isPost()) {
		
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			$proposedBy=$formData['IdStudentRegistration'];
			$this->view->proposedBy=$proposedBy;
			$idtaapplication=$formData['idtaapplication'];
			$this->view->idtaapplication=$idtaapplication;
			$data=array('IdTAApplication'=>$formData['idtaapplication'],
						'IdStudentRegistration'=>$formData['IdStudentRegistration'],
						'dt_exam'=> $formData['dt_exam'] ,
						'time_exam'=>$formData['time_exam'],
						'Ujian_ke'=>$formData['exam_ke']
			);
			if ( $formData['IdTAExamination']=='' ) {
				$idexam=$this->dbExamination->addData($data);
				
			}
			else 
				$this->dbExamination->updateData($data, $formData['IdTAExamination']);
			
		}
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($proposedBy);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$this->view->proposal=$this->dbTaApplication->getApplication($idtaapplication);
		$this->view->process_status=$this->dbApproval->fnGetOpenStatusProcess($idtaapplication);
		$supervisors=$this->dbsupervisor->getAllSupervisor($idtaapplication);
		foreach ($supervisors as $key=>$value) {
			$staff=$this->dbStaff->getData($value['IdStaff']);
			$supervisors[$key]=$staff;
		}
		$this->view->supervisor=$supervisors;
		$this->view->exam=$this->dbExamination->getDataByStudentList($proposedBy, $idtaapplication);
		
	}
	public function examApplicationPdfAction(){

		global $student;
		global $proposal;
		global $exam;
		global $photo_url;
		global $supervisor;
		 
		$proposedBy = $this->_getParam('proposedby', null);
		$idtaapplication= $this->_getParam('idtaapplication', null);
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($proposedBy);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		 
		$proposal=$this->dbTaApplication->getApplication($idtaapplication);
		//get ActivityName
		$activity=$this->dbApproval->getActivityNameByProcessName($idtaapplication, 'SPV');
		$proposal['ActivityName']=$activity['ActivityName'];
		//get Legalname
		$role=$this->dbApproval->getRoleByApprovalType($idtaapplication, 'SPV', 'LEG');
		$proposal['RoleAs']=$role['StaffAs'];
		//get supervisor
		//echo var_dump($proposal);exit;
		$supervisors=$this->dbsupervisor->getAllSupervisor($idtaapplication);
		foreach ($supervisors as $key=>$value) {
			$staff=$this->dbStaff->getData($value['IdStaff']);
			$supervisors[$key]=$staff;
		}
		$supervisor=$supervisors;
		$exam=$this->dbExamination->getDataByStudent($proposedBy, $idtaapplication);
	
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
		global $photo_url;
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
					
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
		
				$photo_url  = $photo_url;
			}else{
				$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
		$fieldValues = array(
				'$[PROGRAM]'=>$student["ArabicName"],
				'$[FACULTY]'=>'FAKULTAS '.$student["NamaKolej"],
				'$[NIM]'=>$student["registrationId"],
				'$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[PHOTO]'=>$photo_url,
				'$[ADD]'=>$student['Add1'],
				'$[PHONE]'=>$student['Phone1'],
				'$[EMAIL]'=>$student['Email'],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
		);
		//filesysrtem
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path
		$html_template_path = DOCUMENT_PATH."/template/exam_application_pdf.html";
		
		$html = file_get_contents($html_template_path);
			
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/finalassignment/exam";
		
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "exam_".$student['registrationId'].".pdf";
		
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		//save file address
		$db = new Finalassignment_Model_DbTable_Examination();
		$db->updateData( array('propose_doc'=>'/documents/student/finalassignment/exam/'.$output_filename),$exam['IdTAExamination']);
		//exit;
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'examination', 'action'=>'exam-application', 'proposedby'=>$student['IdStudentRegistration'],'idtaapplication'=>$proposal['IdTAApplication'] ),'default',true));
		
	}
	public function examinerCompositionAction(){
		 
		$this->view->title = $this->view->translate("Examination Composition Setup");
	
		$idtaflow = $this->_getParam('IdTAFlow', null);
		$this->view->idtaflow=$idtaflow;
		 
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
			$idtaflow=$formData['IdTAFlow'];
			//$this->view->idauto=$formData['Auto'];
			$data=array('IdPosition'=>$formData['IdPosition'],
						'IdFlow'=>$formData['IdTAFlow'],
						'Sequence'=>$formData['Sequence'],
						'Job'=>$formData['Job'],
						 
						'dt_entry'=>date('Y-m-d H:i:sa')
			);
			if ($formData['IdComposition']=='')
				$idcompo=$this->dbExamCompo->addData($data);
			else $this->dbExamCompo->updateData($data, $formData['IdComposition']);
			
			
		}
		$docflow=$this->dbflow->getFlowListByIdFlow($idtaflow);
		$this->view->docflow=$docflow;
		$this->view->Compisitions=$this->dbExamCompo->getPositionName();
		$this->view->ExamComp=$this->dbExamCompo->getDataByIdFlow($idtaflow);
	}
	
	public function deleteExaminerCompositionAction(){
		$idcompo = $this->_getParam('idcomposition', null);
		$idtaflow=$this->_getParam('idTAFlow');
		$this->view->idcompo=$idcompo;
		$this->dbExamCompo->deleteData($idcompo);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'examination', 'action'=>'examiner-composition', 'IdTAFlow'=>$idtaflow ),'default',true));
		
	}
}