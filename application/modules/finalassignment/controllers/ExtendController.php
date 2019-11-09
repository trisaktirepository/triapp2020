<?php  
class Finalassignment_ExtendController extends Zend_Controller_Action { 
	
	private $dbProposal;
	private $dbExtend;
	private $dbApplication;
	private $dbStaff; 
	private $dbApproval;
	
	public function init() { //initialization function
		//$this->_gobjlog = Zend_Registry::get('log'); //instantiate log object
		$this->fnsetObj();
	}
	
	
	public function fnsetObj() {
		$this->dbApplication=new Finalassignment_Model_DbTable_Application();
		$this->dbExtend=new Finalassignment_Model_DbTable_Extend();
		$this->dbProposal=new Finalassignment_Model_DbTable_Proposal();
		$this->dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->dbApproval=new Finalassignment_Model_DbTable_Approval();
	}
	
	public function staffExtendListAction() {
	
	
		$status = $this->_getParam('status', null);
		$idSupervisor= $this->_getParam('id', null);
		if ($idSupervisor==null) {
			$auth = Zend_Auth::getInstance();
			$idSupervisor=$auth->getIdentity()->IdStaff;
		}
		$this->view->supervisor=$idSupervisor;
		 
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
		$this->view->title = $this->view->translate("Propose to Extend");
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
			if ($formData['IdTAExtend']=='') {
				$data=array('IdTAApplication'=>$formData['IdTAApplication'],
							'IdStudentRegistration'=>$formData['IdStudentRegistration'],
							'ExtendCode'=>$formData['ExtendCode'],
							'remark'=>$formData['remark'],
							'dt_entry'=>date('Y-m-d H:i:sa'),
							'Id_User'=>$auth->getIdentity()->appl_id
				);
				$idtaextend=$this->dbExtend->addData($data);
				$this->view->idtaextend=$idtaextend;
				//add workflow
				$student = $studentRegDB->getStudentInfo($idPengaju);
				$this->dbApproval->initWorkflow($student, 'ExtendTA', $idtaapplication);
			
			}
			else {
				$data=array( 
						'ExtendCode'=>$formData['ExtendCode'],
						'remark'=>$formData['remark'],
				);
				$this->view->idtaextend=$formData['IdTAExtend'];
				$this->dbExtend->updateData($data, $formData['IdTAExtend']);
			}
			//generate report
			$this->generateStudentExtendPdf($idPengaju, $idtaapplication);
			
		}
		
		$student = $studentRegDB->getStudentInfo($idPengaju);
		$this->view->student=$student;
		$this->view->extend_list=$this->dbExtend->fnGetExtendType();
		$proposal=$this->dbProposal->fnGetProposalByOwner($idPengaju,$idtaapplication,null);
		$extends=$this->dbExtend->getDataByStudent($idPengaju, $idtaapplication);
		//echo var_dump($extends);exit;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'ExtendTA',$student['IdLandscape']);
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		if ($extends) $this->view->idtaextend=$extends['IdTAExtend'];
		$this->view->extend=$extends;
		$this->view->proposal=$proposal;
		//status persetujuan
	}
	public function studentExtendPdfAction() {
		  
		$auth = Zend_Auth::getInstance();
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$idPengaju= $this->_getParam('proposedby', null);
		$idtaapplication= $this->_getParam('idtaapplication', null);
		$this->generateStudentExtendPdf($idPengaju, $idtaapplication);
	}
	public function generateStudentExtendPdf($idPengaju,$idtaapplication) {
			
		global $student;
		global $extend;
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
		$extend=$this->dbExtend->getDataByStudent($idPengaju, $idtaapplication);
	
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$staff=$dbStaff->getData($extend['Approved_by']);
			
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
		$html_template_path = DOCUMENT_PATH."/template/student_extend_pdf.html";
	
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
		$output_directory_path = DOCUMENT_PATH."/student/finalassignment/extend";
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "extend_".$student['registrationId'].".pdf";
	
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
	
		file_put_contents($output_file_path, $dompdf);
	
		$this->view->file_path = $output_file_path;
		//save file address
		$db = new Finalassignment_Model_DbTable_Extend();
		$db->updateData( array('printed_url'=>'/documents/student/finalassignment/extend/'.$output_filename),$extend['IdTAExtend']);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'student-proposal-list', 'proposedby'=>$student['IdStudentRegistration'] ),'default',true));
	
		//status persetujuan
	}
}