<?php  
class Finalassignment_ChangeController extends Zend_Controller_Action { 
	
	private $dbProposal;
	private $dbApproval;
	private $dbApplication;
	private $dbStaff; 
	private $dbFlowmain;
	private $dbChange;
	private $dbSupervisor;  
	
	public function init() { //initialization function
	//	$this->_gobjlog = Zend_Registry::get('log'); //instantiate log object
		$this->fnsetObj();
	}
	
	
	public function fnsetObj() {
		$this->dbApplication=new Finalassignment_Model_DbTable_Application();
		$this->dbApproval=new Finalassignment_Model_DbTable_Approval();
		$this->dbProposal=new Finalassignment_Model_DbTable_Proposal();
		$this->dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$this->dbChange=new Finalassignment_Model_DbTable_Change();
		$this->dbSupervisor=new Finalassignment_Model_DbTable_Supervisor(); 
	}
	
	public function staffChangeListAction() {
	
	
		$status = $this->_getParam('status', null);
		$idSupervisor= $this->_getParam('id', null);
		if ($idSupervisor==null) {
			$auth = Zend_Auth::getInstance();
			$idSupervisor=$auth->getIdentity()->IdStaff;
		}
		$this->view->supervisor=$idSupervisor;
		$flowmain=$this->dbFlowmain->getFlowMainName();
	
		$this->view->flowmain = $flowmain;
		//title
		$this->view->title= $this->view->translate("Final Assignment/Thesis/Disertation Supervision");
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->view->staff=$dbStaff->getData($idSupervisor);
		$supervision_list = $this->dbApproval->fnGetAllSupervisoryShouldApproval($idSupervisor);
		$dbStd=new Registration_Model_DbTable_Studentregistration();
		foreach ($supervision_list as $key=>$item) {
			$student=$dbStd->SearchStudent(array('Idstudentreg'=>$item['IdStudentRegistration']));
			//echo var_dump($supervision_list);exit;
			$student=$student[0];
			$supervision_list[$key]['Student']=$student;
			$supervision_list[$key]['Progress']=$this->dbSupervision->getProgressByStudent($item['IdTAApplication'], $item['IdStudentRegistration']);
		}
		$this->view->opensupervision_list = $supervision_list;
		$supervision_list = $this->dbApproval->fnGetAllSupervisoryApproved($idSupervisor);
			
		foreach ($supervision_list as $key=>$item) {
			$student=$dbStd->SearchStudent(array('Idstudentreg'=>$item['IdStudentRegistration']));
			$student=$student[0];
			$supervision_list[$key]['Student']=$student;
			$supervision_list[$key]['Progress']=$this->dbSupervision->getProgressByStudent($item['IdTAApplication'], $item['IdStudentRegistration']);
		}
		$this->view->finishsupervision_list = $supervision_list;
			
	}
	
	public function indexAction() {
		$this->view->title = $this->view->translate("Propose to Change Topics");
		$auth = Zend_Auth::getInstance();
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$idPengaju= $this->_getParam('proposedby', null);
		$this->view->proposedby=$idPengaju;
		$idtaapplication= $this->_getParam('idtaapplication', null);
		$this->view->idtaapplication=$idtaapplication;
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$idPengaju=$formData['IdStudentRegistration'];
			$idtaapplication=$formData['IdTAApplication'];
			if ($formData['IdTAChange']=='') {
				$data=array('IdTAApplication'=>$formData['IdTAApplication'],
							'IdStudentRegistration'=>$formData['IdStudentRegistration'],
							'ChangeCode'=>$formData['ChangeCode'],
							'problem'=>$formData['problem'],
							'title_bahasa'=>$formData['title_bahasa'],
							'title'=>$formData['title'],
							'dt_entry'=>date('Y-m-d H:i:sa'),
							'Id_User'=>$auth->getIdentity()->appl_id
				);
				
				$idtachange=$this->dbChange->addData($data);
				$this->view->idtachange=$idtachange;
				
				//add workflow
				$student = $studentRegDB->getStudentInfo($idPengaju);
				 
				$this->dbApproval->initWorkflow($student, 'Change', $idtaapplication);
				 
			}
			else {
				$data=array( 
						'ChangeCode'=>$formData['ChangeCode'],
							'problem'=>$formData['problem'],
							'title_bahasa'=>$formData['title_bahasa'],
							'title'=>$formData['title']
				);
				$this->view->idtachange=$formData['IdTAChange'];
				$this->dbChange->updateData($data, $formData['IdTAChange']);
			}
			//generate report
			$this->generateStudentChangePdf($idPengaju, $idtaapplication);
			
		}
		$this->view->change_list=$this->dbChange->fnGetChangeType();
		$student = $studentRegDB->getStudentInfo($idPengaju);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($idPengaju);
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$proposal=$this->dbProposal->fnGetProposalByOwner($idPengaju,$idtaapplication,null);
		$change=$this->dbChange->getDataByStudent($idPengaju, $idtaapplication);
		//echo var_dump($change);exit;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Change',$student['IdLandscape']);
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		if ($change) $this->view->idtachange=$change['IdTAChange'];
		$this->view->proposal_new=$change;
		$this->view->proposal=$proposal;
		
		//status persetujuan
	}
	
	public function studentChangePdfAction() {
		  
		$auth = Zend_Auth::getInstance();
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$idPengaju= $this->_getParam('proposedby', null);
		$idtaapplication= $this->_getParam('idtaapplication', null);
		$this->generateStudentChangePdf($idPengaju, $idtaapplication);
	}
	public function generateStudentChangePdf($idPengaju,$idtaapplication) {
			
		global $student;
		global $proposal;
		global $proposal_new;
		global $staff;
		global $photo_url;
		$auth = Zend_Auth::getInstance();
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($idPengaju);
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
					
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
	
				$photo_url  = $photo_url;
			}else{
				$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
		}
		$proposal=$this->dbProposal->fnGetProposalByOwner($idPengaju, $idtaapplication);
		$proposal_new=$this->dbChange->getDataByStudent($idPengaju, $idtaapplication);
		$flowmain=$this->dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'], 'Change',$student['IdLandscape']);
		$proposal['ActivityName']=$flowmain['ActivityName'];
		
		//getsupervisor===================
		$supervisor=$this->dbSupervisor->getSupervisorByOrder($idtaapplication, 1);
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$staff=$dbStaff->getData($supervisor['IdStaff']);
			
		$fieldValues = array(
				'$[PROGRAM]'=>$student["ArabicName"],
				'$[FACULTY]'=>'FAKULTAS '.$student["CollegeName"],
				'$[NIM]'=>$student["registrationId"],
				'$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[PHOTO]'=>$photo_url,
					
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
		);
		//filesysrtem
		require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
	
		//template path
		$html_template_path = DOCUMENT_PATH."/template/student_change_pdf.html";
	
		$html = file_get_contents($html_template_path);
			
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
	
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/finalassignment/change";
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "change_".$student['registrationId'].".pdf";
	
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
	
		file_put_contents($output_file_path, $dompdf);
	
		$this->view->file_path = $output_file_path;
		//save file address
		$db = new Finalassignment_Model_DbTable_Change();
		$db->updateData( array('printed_url'=>'/documents/student/finalassignment/change/'.$output_filename),$extend['IdTAExtend']);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'student-proposal-list', 'proposedby'=>$student['IdStudentRegistration'] ),'default',true));
	
		//status persetujuan
	}
}