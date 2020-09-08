<?php 

class Finalassignment_ProposalController  extends Zend_Controller_Action  { 
	
	private $dbProposal;
	private $dbTaApplication;
	private $dbProgram;
	private $dbNilaiSyarat;
	private $dbApproval;
	private $dbFlow;
	private $dbFlowmain;
	private $dbStaff;
	private $dbDeandecree;
	private $dbSupervisor;
	private $dbSupervisormaster;
	private $dbStdRegSubject;
	private $dbExtend;
	private $dbChange;
	private $dbReq;
	private $dbReqStudent;
	private $dbexaminerComp;
	
	public function init() { //initialization function
		//$this->_gobjlog = Zend_Registry::get('log'); //instantiate log object
		$this->fnsetObj();
	}
	
	
	public function fnsetObj() {
		$this->dbReqStudent=new Finalassignment_Model_DbTable_DocRequisiteStudent();
		$this->dbSupervisormaster=new Finalassignment_Model_DbTable_Supervisormaster();
		$this->dbProposal=new Finalassignment_Model_DbTable_Proposal();
		$this->dbTaApplication=new Finalassignment_Model_DbTable_Application();
		$this->dbProgram=new App_Model_General_DbTable_Program();
		$this->dbNilaiSyarat=new Finalassignment_Model_DbTable_SubjectPrerequisite();
		$this->dbApproval=new Finalassignment_Model_DbTable_Approval();
		$this->dbFlow=new Finalassignment_Model_DbTable_Flow();
		$this->dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$this->dbStaff=new App_Model_General_DbTable_Staffmaster();
		$this->dbSupervisor=new Finalassignment_Model_DbTable_Supervisor();
		$this->dbDeandecree=new Finalassignment_Model_DbTable_DeanDecree();
		$this->dbStdRegSubject=new Examination_Model_DbTable_StudentRegistrationSubject();
		$this->dbExtend=new Finalassignment_Model_DbTable_Extend();
		$this->dbChange=new Finalassignment_Model_DbTable_Change();
		$this->dbReq=new Finalassignment_Model_DbTable_DocRequisiteDetail();
		$this->dbexaminerComp=new Finalassignment_Model_DbTable_Examination();
	}
	
	
	public function staffProposalListAction() {
	
		
		$status = $this->_getParam('status', null);
		$idPengaju= $this->_getParam('id', null);
		if ($idPengaju==null) {
			$auth = Zend_Auth::getInstance();
			$idPengaju=$auth->getIdentity()->IdStaff;
		}
		
		$this->view->proposedby=$idPengaju;
		if($status){
			if($status==1){
				$this->view->noticeSuccess = "Proposal Created";
			}else
			if($status==0){
				$this->view->noticeError = "Unable to create Proposal";
			}
		}
		
		//title
		$this->view->title= $this->view->translate("Final Assignment Title Proposal");
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->view->staff=$dbStaff->getData($idPengaju);
		$proposal_list = $this->dbProposal->fnGetAllProposalByOwner($idPengaju,'Staff');
		 
		$approval_list = $this->dbApproval->fnGetAllProposalShouldApproval($idPengaju);
		foreach ($approval_list as $key=>$item) {
			$idFlowMain=$item['IdTAFlowMain'];
			$flowmain=$this->dbFlowmain->getFlowName($idFlowMain);
			$approval_list[$key]['Stage']=$flowmain['Stage'];
		}
		$this->view->proposal_list = $proposal_list;
		$this->view->approval_list=$approval_list;
		
			
	}
	public function deanDecreeListAction() {
	
		$auth = Zend_Auth::getInstance();
		$this->view->title= $this->view->translate("Dean Decree Setup");
		$dbDecree=new Finalassignment_Model_DbTable_DeanDecree();
		if ($this->getRequest()->isPost()) {
		
			$formData = $this->getRequest()->getPost();
			$formData['Id_User']=$auth->getIdentity()->iduser;
			$data = array(
					'decree' => $formData['dialog_nomor'],
					'dt_effective'=> $formData['dialog_decree_date'],
					'IdSemesterMain'=>$formData['IdSemester'],
					'IdProgram'=>$formData['IdProgram'],
					'active'=> '1' ,
					'dt_entry' => date('Y-m-d H:i:sa'),
					'Id_User' =>  $formData['Id_User']
			);
			$dbDecree->addData($data);
		}
		$dbprogram=new App_Model_General_DbTable_Program();
		$this->view->program_list=$dbprogram->fnGetProgramList();
		$dbsem=new App_Model_General_DbTable_Semestermaster();
		$this->view->semester_list=$dbsem->fnGetSemestermasterListNoCheck();
		$this->view->deandecree_list=$dbDecree->getData();
			
	}
	public function deanDecreeNewAction() {
	
	
		$this->view->title= $this->view->translate("Dean Decree: Create");

		if ($this->getRequest()->isPost()) {
		
			$formData = $this->getRequest()->getPost();
		}
		$dbDecree=new Finalassignment_Model_DbTable_DeanDecree();
		$this->view->deandecree_list=$dbDecree->getData();
			
	}
	public function studentProposalListAction() {
	
	
		$status = $this->_getParam('status', null);
		$idPengaju= $this->_getParam('proposedby', null);
		
		if ($idPengaju==null) {
			//get applicant profile
			$auth = Zend_Auth::getInstance();
			
			//print_r($auth->getIdentity());
			 
			$appl_id = $auth->getIdentity()->appl_id;
			$idPengaju = $auth->getIdentity()->registration_id;
		}
		$this->view->proposedby=$idPengaju;
		
		if($status){
			if($status==1){
				$this->view->noticeSuccess = "Proposal Created";
			}else
			if($status==0){
				$this->view->noticeError = "Unable to create Proposal";
			}
		}
	
		//title
		$this->view->title= $this->view->translate("Final Assignment Title Proposal");
		$dbStudent=new Examination_Model_DbTable_StudentRegistration();
		$student=$dbStudent->getStudentInfo($idPengaju);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($idPengaju);
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student['IdLandscape']);
		
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
		//=================
		if ($subject_prerequisite) {
			$proposal_list = $this->dbProposal->fnGetAllProposalByOwner($idPengaju,'Student');
			
			//is approved?
			$app=$this->dbProposal->isApproved($idPengaju);
			//echo var_dump($app);
			if ($app) {
				//disable other proposal
				foreach ($proposal_list as $key=>$value) {
					 
					if ($value['IdTAApplication']!=$app['IdTAApplication']) unset($proposal_list[$key]);
					else {
						//echo 'sama';
						if ($value['TGL_selesai']=='0000-00-00 00:00:00' || $value['TGL_selesai']=='') {
							$proposal_list[$key]['Approved']='1';
							$expired=$this->dbProposal->isExpired($value['IdTAApplication']);
							if ($expired) {
								//cek took extend before if yes change title
								$extends=$this->dbExtend->isExtend($idPengaju,$value['IdTAApplication'] );
								//echo var_dump($extends);exit;
								if ($extends)
									$proposal_list[$key]['Change']="1";
								else
									$proposal_list[$key]['Expired']="1";
							} else {
								//chek if there is approved advisor
								if ($this->dbSupervisor->isSupervised($value['IdTAApplication']))
									$proposal_list[$key]['Supervision']="1";
								else $proposal_list[$key]['Supervision']="0";
							}
						} else {
							$proposal_list[$key]['ApplyExam']="1";
						}
					}
				}	 
				//echo var_dump($proposal_list);exit;
			}
			//echo var_dump($proposal_list);exit;
			$this->view->proposal_list = $proposal_list;
			//get staff proposal that suitable to student spesification
			$proposaloffer=$this->dbProposal->fnGetAllStaffProposalSuitableForStudent($student['IdStudentRegistration']);
			foreach($proposaloffer as $key=>$item) {
				$application=$this->dbTaApplication->getApplicationByStudent($student['IdStudentRegistration'],$item['IdTA']);
				if ($application) {
					$proposaloffer[$key]['IdStudentRegistration']=$application['IdStudentRegistration'];
					$proposaloffer[$key]['IdTAApplication']=$application['IdTAApplication'];
					$proposaloffer[$key]['doc_url']=$application['doc_url'];
					$proposaloffer[$key]['printed_url']=$application['printed_url'];
				}
				
			}
			foreach($proposaloffer as $key=>$item) {
				if ($app) {
					if (!isset($item['IdTAApplication'])) unset($proposaloffer[$key]);
					else if ($item['IdTAApplication']!=$app['IdTAApplication']) unset($proposaloffer[$key]);
					else if ($value['TGL_selesai']=='0000-00-00 00:00:00') {
								if ($this->dbProposal->isExpired($item['IdTAApplication'])) {
									$extends=$this->dbExtend->isExtend($idPengaju,$item['IdTAApplication'] );
									if ($extends)
										$proposaloffer[$key]['Change']="1";
									else
										$proposaloffer[$key]['Expired']="1";
									 
								} else $proposal_list[$key]['Supervision']="1";
					} else {
						$proposal_list[$key]['ApplyExam']="1";
					}
				}
			}
			
			$this->view->openStaffProposal_list = $proposaloffer;
			
			
		}
		else 
		{
			$this->view->openStaffProposal_list=array();
			$this->view->proposal_list=array();
		}
			
	}
	
	public function indexStudentAction() {
		
		$this->view->title = $this->view->translate("Student Records");
		$auth = Zend_Auth::getInstance();
		$form = new Registration_Form_SearchStudent();
		$this->view->form = $form;
	
		if ($this->getRequest()->isPost()) {
				
			$formData = $this->getRequest()->getPost();
				
			$studentRegDB = new Registration_Model_DbTable_Studentregistration();
			$student_list = $studentRegDB->getListStudent($formData);
				
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_list));
			//$paginator->setItemCountPerPage($this->gintPageCount);
			$paginator->setItemCountPerPage(1000);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
				
			$form->populate($formData);
				
			$this->view->paginator = $paginator;
		}else{
			if($auth->getIdentity()->IdRole=="445"){
				$studentRegDB = new Registration_Model_DbTable_Studentregistration();
				$student_list = $studentRegDB->getListStudent();
				 
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_list));
				//$paginator->setItemCountPerPage($this->gintPageCount);
				$paginator->setItemCountPerPage(1000);
				$paginator->setCurrentPageNumber($this->_getParam('page',1));
				$this->view->paginator = $paginator;
			}
		}
	
	}
	
	public function applicationAction() {
		
		$step = $this->_getParam('step', 1);
		$this->view->step = $step;
		//$auth = Zend_Auth::getInstance();
		$this->view->title= $this->view->translate("Proposal of FinalAssignment/Thesis/Desertation Title - Create New");
		$ses_proposal = new Zend_Session_Namespace('sis');
		$ses_proposal->setExpirationSeconds(900);
		$idProposal = $this->_getParam('idProposal', null); 
		$proposedBy = $this->_getParam('proposedby', null); 
		$edit = $this->_getParam('edit', 0);
		if ($edit > 0 ) {
			// initialization
			$proposal=$this->dbProposal->fnGetProposal($idProposal);
			$dbCollege=new GeneralSetup_Model_DbTable_Program();
			$coll=$dbCollege->fngetProgramData($proposal['IdProgram']);
			$ses_proposal->idproposal=$idProposal;
			$ses_proposal->proposal=$proposal;
			$ses_proposal->faculty=$coll['IdCollege'];
			$ses_proposal->program=$proposal['IdProgram'];
			$ses_proposal->branch=$proposal['IdBranch'];
			$ses_proposal->major=$proposal['IdMajor'];
			$ses_proposal->identity=array('idstatus'=>'Staff','IdPengaju'=>$proposal['IdPengaju']);
			$subjects=$this->dbNilaiSyarat->getAllSubjectPrerequisite($idProposal);
			foreach ($subjects as $index=>$item) {
				$grade=$item['Grade_min'];
				$subname=$item['BahasaIndonesia'];
				$subcode=$item['SubCode'];
				$data[$item['IdSubject']]=array('IdSubject'=>$item['IdSubject'],'SubjectName'=>$subname,'SubCode'=>$subcode,'grade_name'=>$grade);
			}
			$ses_proposal->subject=$data;
			$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
		
		} else {
			if (!isset($ses_proposal->identity)){
				$ses_proposal->identity=array('idstatus'=>'Staff','IdPengaju'=>$proposedBy);
			}
		}
		 //echo $ses_proposal->program;
		
		if($step==1){ //STEP 1
			//echo 'step1';echo var_dump($ses_proposal->proposal);
			if ($this->getRequest()->isPost()) {
				 
				$formData = $this->getRequest()->getPost();
				$ses_proposal->faculty=$formData['IdCollege'];
				
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$this->view->facultylist=$dbCollege->fngetCollegemasterDetails();
			
			if (isset($ses_proposal->faculty)){
				
				$this->view->faculty= $ses_proposal->faculty;
				 
			}
						
		} else
		if ($step==2){ //STEP 2 Program
			//echo 'test3';echo var_dump($ses_proposal->proposal);exit;
			if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_proposal->program=$formData['IdProgram'];
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>3),'default',true));
			}
			$dbProgram=new GeneralSetup_Model_DbTable_Program();
			$this->view->programlist=$dbProgram->getProgram($ses_proposal->faculty);
			if (isset($ses_proposal->program)){
				//echo $ses_proposal->program;exit;
				$this->view->program= $ses_proposal->program;
				 
			}
    	
		}else
		if ($step==3){ //STEP 3 Branch
			
			if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			if(!isset($ses_proposal->program)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_proposal->branch=$formData['IdBranch'];
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>4),'default',true));
			
			}
			$program=$ses_proposal->program;
			$dbProgram=new GeneralSetup_Model_DbTable_Branchofficevenue();
			$this->view->branchlist=$dbProgram->fnGetStdBranchList($program);
				
			if (isset($ses_proposal->branch)){
		
				$this->view->branch= $ses_proposal->branch;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
		
			//echo var_dump($ses_servqual_setup->grp_question);exit;
				
		}else
		if ($step==4){ //STEP 4 Majoring
				
			if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			if(!isset($ses_proposal->program)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			if(!isset($ses_proposal->branch)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>3),'default',true));
			}
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_proposal->major=$formData['IdMajor'];
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>5),'default',true));
					
			}
			$program=$ses_proposal->program;
			$dbMajor=new GeneralSetup_Model_DbTable_ProgramMajoring();
			$this->view->majorlist=$dbMajor->getAllMajoringList($program);
			//echo var_dump($this->view->majorlist);exit;
			if (isset($ses_proposal->major)){
		
				$this->view->major= $ses_proposal->major;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
		
			//echo var_dump($ses_servqual_setup->grp_question);exit;
		
		}else
		if ($step==5){ //STEP 5 proposal
		
			if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			if(!isset($ses_proposal->program)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			if(!isset($ses_proposal->branch)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>3),'default',true));
			}
			if(!isset($ses_proposal->major)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>4),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				
				//echo var_dump($formData);exit;
				if (!isset($formData['title_bahasa'])) {
					$ses_proposal->proposal==array();
					//echo var_dump($ses_proposal);exit;
					$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>5),'default',true));
					
				} else 
				{
					$ses_proposal->proposal=$formData;
					$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>6),'default',true));
			
				}
			}
			
			//get subject in course group based on Program and Branch
			$this->view->IdPengaju=$ses_proposal->identity['IdPengaju'];
			$this->view->proposedAs=$ses_proposal->identity['idstatus'];
			
			if (isset($ses_proposal->proposal)){
				//echo var_dump($ses_proposal->proposal);exit;
				$this->view->proposal= $ses_proposal->proposal;
				//$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>5),'default',true));
				//echo var_dump($ses_proposal->proposal);exit;
			}
		
			//echo var_dump($ses_servqual_setup->grp_question);exit;
		
		}
		else
		if ($step==6){ //STEP 6 Subject Prerequisite
		
			if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			if(!isset($ses_proposal->program)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			if(!isset($ses_proposal->branch)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>3),'default',true));
			}
			if(!isset($ses_proposal->major)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>4),'default',true));
			}
			if(!isset($ses_proposal->proposal)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>5),'default',true));
			}
				
			if ($this->getRequest()->isPost()) {
				$data=array();
				$formData = $this->getRequest()->getPost();
				$ses_proposal->proposal['cgpa_min']=$formData['cgpa_min'];
				$ses_proposal->proposal['cgpa_max']=$formData['cgpa_max'];
				foreach ($formData['subject'] as $index=>$item) {
					$grade=$formData['grade'][$index];
					$subname=$formData['SubName'][$index];
					$subcode=$formData['SubCode'][$index];
					$data[$item]=array('IdSubject'=>$item,'SubjectName'=>$subname,'SubCode'=>$subcode,'grade_name'=>$grade);	
				}
				$ses_proposal->subject=$data;
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>7),'default',true));
			}
			$dbLandscape=new GeneralSetup_Model_DbTable_Landscapesubject();
			$this->view->subject_list=$dbLandscape->getActiveLandscapeSubjects($ses_proposal->program);		
			
			if (isset($ses_proposal->subject)){
				$this->view->cgpa_min=$ses_proposal->proposal['cgpa_min'];
				$this->view->cgpa_max=$ses_proposal->proposal['cgpa_max'];
				$this->view->subject= $ses_proposal->subject;
				//echo var_dump($ses_proposal->subject);
			}
		 
		}
		else 
		 if ($step==7 ) { //Confirmation
		 	if(!isset($ses_proposal->faculty)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>1),'default',true));
			}
			if(!isset($ses_proposal->program)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>2),'default',true));
			}
			if(!isset($ses_proposal->branch)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>3),'default',true));
			}
			if(!isset($ses_proposal->major)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>4),'default',true));
			}
			if(!isset($ses_proposal->proposal)){
				$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'application', 'step'=>5),'default',true));
			}
			if ($this->getRequest()->isPost()) {
					 
					$this->saveProposal('Staff');
					$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'staff-proposal-list','id'=>$ses_proposal->identity['IdPengaju']),'default',true));
			 
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$dbProgram = new GeneralSetup_Model_DbTable_Program();
			$dbMajor=new GeneralSetup_Model_DbTable_ProgramMajoring();
			$dbBranch = new GeneralSetup_Model_DbTable_Branchofficevenue();
			$this->view->program= $dbProgram->fngetProgramData($ses_proposal->faculty);
			$this->view->major=$dbMajor->getInfo($ses_proposal->major);
			$this->view->branch = $dbBranch->getData($ses_proposal->branch);
			$this->view->subject =  $ses_proposal->subject;
			$this->view->proposal =  $ses_proposal->proposal; 
			
			//echo var_dump($this->view->groups);exit;
		}
		
	}
	
	private function saveProposal($by=null){
		 
		//data from configuration screen
		$auth = Zend_Auth::getInstance();
		$ses_proposal = new Zend_Session_Namespace('sis');
	
		if(
		!isset($ses_proposal->faculty) ||
		!isset($ses_proposal->program) ||
		!isset($ses_proposal->branch) ||
		!isset($ses_proposal->major) ||
		!isset($ses_proposal->proposal)
		){
			 
			throw new Exception("No data of survey ");
			exit;
		}
		//save proposal
		$proposal=$ses_proposal->proposal;
		$proposal['IdProgram']=$ses_proposal->program;
		$proposal['IdBranch']=$ses_proposal->branch;
		$proposal['IdMajor']=$ses_proposal->major;
		$proposal['Id_User']=$auth->getIdentity()->iduser;
		$data['Id_User']=$auth->getIdentity()->iduser;
		unset($proposal['idProposal']);
		
		//echo var_dump($proposal);exit;
		if (isset($ses_proposal->idproposal) && $ses_proposal->idproposal!='0' ) {
			$this->dbProposal->updateData($proposal,$ses_proposal->idproposal);
			$this->dbNilaiSyarat->deleteDataAll( $ses_proposal->idproposal );
			 
			$data['IdTA']= $ses_proposal->idproposal;
			foreach ($ses_proposal->subject as $item) {
				
				$data['IdSubject']=$item['IdSubject'];
				if ($by=='Staff') {
					$data['grade_min']=$item['grade_name'];
					$data['grade_name']=null;
				}
				else {
					$data['grade_min']=null;
					$data['grade_name']=$item['grade_name'];
				}
				$this->dbNilaiSyarat->addData($data);
			}
		}else {
			$data = array(
					'title_bahasa' => $proposal['title_bahasa'],
					'title' => $proposal['title'],
					'problem1' => $proposal['problem1'],
					'problem2'=> $proposal['problem2'],
					'problem3' => $proposal['problem3'],
					'ABSTRAK' => $proposal['abstrak'],
					'cgpa_min' => $proposal['cgpa_min'],
					'cgpa_max' => $proposal['cgpa_max'],
					'STATUS_PENGAJU' => $proposal['STATUS_PENGAJU'],
					'IdPengaju' => $proposal['IdPengaju'],
					'IdProgram' => $proposal['IdProgram'],
					'IdMajor' => $proposal['IdMajor'],
					'IdBranch' => $proposal['IdBranch'],
					'dt_update' => date('Y-m-d H:i:sa'),
					'Id_User' =>  $proposal['Id_User']
			);
			$idTa=$this->dbProposal->addData($data);
			$data=array();
			$data['IdTA']=$idTa;
			$data['Id_User']=$auth->getIdentity()->iduser;
			foreach ($ses_proposal->subject as $item) {
				$data['IdSubject']=$item['IdSubject'];
				if ($by=='Staff') {
					$data['grade_min']=$item['grade_name'];
					$data['grade_name']=null;
				}
				else {
					$data['grade_min']=null;
					$data['grade_name']=$item['grade_name'];
				}
				$this->dbNilaiSyarat->addData($data);
			}
		}
		 
	}
	public function studentApplyStaffProposalAction() {
		$this->view->title = $this->view->translate("Apply Offered Proposal by Staff");
		$auth = Zend_Auth::getInstance();
		$appliedby = $this->_getParam('appliedby', null);
		$idTA = $this->_getParam('idta', null);
		$this->view->idta=$idTA;
		$this->view->appliedby=$appliedby;
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			$idTA=$formData['idProposal'];
			$this->view->idta=$idTA;
			$this->view->appliedby=$formData['appliedby'];
			if ($formData['IdTAApplication']=='' ) {
				
				$appliedby=$formData['appliedby'];
				$postData['IdStudentRegistration']=$formData['appliedby'];
				$postData['IdTA']=$formData['idProposal'];
				$postData['Id_User']= $auth->getIdentity()->iduser;
				$postData['IdStatusApproval']=null;
				$postData['dtstart']=null;
				$postData['dtstop']=null;
				//$postData['IdProcess']=null;
				$postData['remark']=null;
				$postData['IdProcess']=null;
				$postData['IdSemStart']=null;
				$postData['IdSemStop']=null;
				$postData['Id_User']=$auth->getIdentity()->iduser;
				$idApplication=$this->dbTaApplication->addData($postData);
				//set workflow
				$idflowmain=$formData['IdFlowMain'];
				$flow=$this->dbFlow->getFlowByFlowMainSeq($idflowmain,1);
				$indays=$flow['indays']." days";
				$Data['IdTAApplication']=$idApplication;
				$Data['IdTAFlow']=$flow['IdTAFlow'];
				$Data['Sequence']=1;
				$Data['StaffAs']=$flow['StaffAs'];
				$Data['remark']='';
				$Data['ApprovalStatus']="0";
				$Data['Approved_by']=$flow['IdStaffApproving'];
				$Data['dtlatest']=date('Y-m-d', strtotime(' +' . $indays . ' days'));
				$Data['Id_User']=$auth->getIdentity()->iduser;
				$this->dbApproval->addData($Data);
				$this->view->idta=$idTA;
				 
	
			
			}
				
		}
		if ($this->dbApproval->isApproved($idTA)) $this->view->approved='1';
		else $this->view->approved='0';
		 
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($appliedby);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($appliedby);
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student['IdLandscape']);
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
		//=================
		$this->view->prerequisites=$subject_prerequisite;
		$proposal=$this->dbProposal->fnGetProposalByStudent($idTA,$appliedby);
		$status='';
		if (!$proposal) {
			$status='1';
			$proposal=$this->dbProposal->fnGetProposal($idTA);
		}
		$this->view->proposal=$proposal;
		//get nilai related
		$nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($idTA);
		foreach($nilaisyarat as $key=>$item) {
			$id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($appliedby, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}
		$this->view->subject_list=$nilaisyarat;
		if ($status=='')
			$this->view->process_status=$this->dbApproval->fnGetOpenStatusProcess($proposal['IdTAApplication']);
		else 
			$this->view->process_status=array();
	}
	
	public function studentApplicationOldAction() {
		$this->view->title = $this->view->translate("Propose Final Assingment/Thesis/Disertasion");
		$auth = Zend_Auth::getInstance();
		$proposedBy = $this->_getParam('proposedby', null);
		$idApplication = $this->_getParam('idtaapplication', 0);
		
		if ($this->getRequest()->isPost()) {
		
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			$proposedBy=$formData['IdPengaju'];
			if ($formData['idProposal']=='') {
				$formData['Id_User']= $auth->getIdentity()->iduser;
				$formData['cgpa_min']="";
				$formData['cgpa_max']="";
				$idProposal=$this->dbProposal->addData($formData);
				
				$postData['IdStudentRegistration']=$formData['IdPengaju'];
				$postData['IdTA']=$idProposal;
				$postData['Id_User']= $auth->getIdentity()->iduser;
				$postData['IdStatusApproval']=null;
				$postData['dtstart']=null;
				$postData['dtstop']=null;
				$postData['IdProcess']=null;
				$postData['remark']=null;
				$postData['IdSemStart']=null;
				$postData['IdSemStop']=null;
				$postData['Id_User']=$auth->getIdentity()->iduser;
				$idApplication=$this->dbTaApplication->addData($postData);
				//set workflow
				$idflowmain=$formData['IdFlowMain'];
				$flow=$this->dbFlow->getFlowByFlowMainSeq($idflowmain,1);
				$indays=$flow['indays']." days";
				$Data['IdTAApplication']=$idApplication;
				$Data['IdTAFlow']=$flow['IdTAFlow'];
				$Data['StaffAs']=$flow['StaffAs'];
				$Data['approval_type']=$flow['approval_type'];
				$Data['Sequence']=1;
				$Data['remark']='';
				$Data['ApprovalStatus']="0";
				$Data['Approved_by']=$flow['IdStaffApproving'];
				$Data['dtlatest']=date('Y-m-d', strtotime(' +' . $indays . ' days'));
				$Data['Id_User']=$auth->getIdentity()->appl_id;
				$this->dbApproval->addData($Data);
				$this->dbNilaiSyarat->deleteDataAll($idProposal);
				if (isset($formData['subject'])) {
					foreach ($formData['subject'] as $index=>$item) {
						$grade=$formData['grade'][$index];
						$data=array('grade_name'=>$grade,
									'IdSubject'=>$item,
									'IdTA'=>$idProposal,
									'Id_User'=>$auth->getIdentity()->appl_id
						);
						//$this->dbNilaiSyarat->deleteDataSubject($idProposal,$item);
						$this->dbNilaiSyarat->addData($data);
					}
				}  
				
			} else {
				
				$proposedBy=$formData['IdPengaju'];
				$formData['Id_User']= $auth->getIdentity()->appl_id;
				$idProposal=$formData['idProposal'];
				$idApplication=$formData['IdTAApplication'];
				$data = array(
						'title_bahasa' => $formData['title_bahasa'],
						'title' => $formData['title'],
						'problem1' => $formData['problem1'],
						'problem2'=> $formData['problem2'],
						'problem3' => $formData['problem3'],
						'ABSTRAK' => $formData['abstrak'], 
						'STATUS_PENGAJU' => $formData['STATUS_PENGAJU'],
						'IdPengaju' => $formData['IdPengaju'],
						'IdProgram' => $formData['IdProgram'],
						'IdMajor' => $formData['IdMajor'],
						'IdBranch' => $formData['IdBranch'],
						'dt_update' => date('Y-m-d H:i:s'),
						'Id_User' =>  $formData['Id_User']
				);
				$this->dbProposal->updateData($data,$idProposal);
				$this->dbNilaiSyarat->deleteDataAll($idProposal);
				if (isset($formData['subject'])) {
					foreach ($formData['subject'] as $index=>$item) {
						$grade=$formData['grade'][$index];
						$data=array('grade_name'=>$grade,
									'IdSubject'=>$item,
									'IdTA'=>$idProposal,
									'Id_User'=>$auth->getIdentity()->appl_id
						);
						//$this->dbNilaiSyarat->deleteDataSubject($idProposal,$item);
						$this->dbNilaiSyarat->addData($data);
					}
				}  
			}
			
		}
		
		if ($this->dbApproval->isApproved($idApplication)) $this->view->approved='1';
		else $this->view->approved='0';
		//echo 'lp';exit;
				
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($proposedBy);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		//get transkrip
		$dbStdRegSub=new Examination_Model_DbTable_StudentRegistrationSubject();
		$transkrip=$dbStdRegSub->getTranscriptList($proposedBy,$student['idTranscriptProfile']);
		$this->view->transkrip=$transkrip;
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student['IdLandscape']);
	// echo var_dump($subject);exit;
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
		//=================
		$this->view->prerequisites=$subject_prerequisite;
		//echo var_dump($subject_prerequisite);exit;
		//$this->view->prerequisiteStatus=$completed;
		$proposal=$this->dbProposal->fnGetProposalByOwner($proposedBy,$idApplication,null);
		$this->view->proposal=$proposal;
		if ($proposal) $nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($proposal['IdTA']);
		else $nilaisyarat=array();
		/*foreach($nilaisyarat as $key=>$item) {
			$id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($proposedBy, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}*/
		$this->view->subject_list=$nilaisyarat;
		$this->view->process_status=$this->dbApproval->fnGetOpenStatusProcess($idApplication);
		
	}
	public function studentApplicationAction() {
		$this->view->title = $this->view->translate("Propose Final Assingment/Thesis/Disertasion");
		$auth = Zend_Auth::getInstance();
		
		$proposedBy = $this->_getParam('proposedby', null);
		$this->view->pengaju=$proposedBy;
		$idApplication = $this->_getParam('idtaapplication', 0);
	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			$proposedBy=$formData['IdPengaju'];
			$idApplication=$formData['IdTAApplication'];
			if ($formData['idProposal']=='') {
				//new proposal by student
				$formData['Id_User']= $auth->getIdentity()->appl_id;
				$formData['cgpa_min']="";
				$formData['cgpa_max']="";
				//echo var_dump($formData);exit;
				$idProposal=$this->dbProposal->addData($formData);
	
				$postData['IdStudentRegistration']=$formData['IdPengaju'];
				$postData['IdTA']=$idProposal;
				$postData['Id_User']= $auth->getIdentity()->appl_id;
				$postData['IdStatusApproval']=null;
				$postData['dtstart']=null;
				$postData['dtstop']=null;
				$postData['IdProcess']=null;
				$postData['remark']=null;
				$postData['IdSemStart']=null;
				$postData['IdSemStop']=null;
				$postData['Id_User']=$auth->getIdentity()->appl_id;
			//	echo var_dump($postData);exit;
				$idApplication=$this->dbTaApplication->addData($postData);
				//set workflow
				$idflowmain=$formData['IdFlowMain'];
				$flow=$this->dbFlow->getFlowByFlowMainSeq($idflowmain,1);
				$indays=$flow['indays']." days";
				$Data['IdTAApplication']=$idApplication;
				$Data['IdTAFlow']=$flow['IdTAFlow'];
				$Data['StaffAs']=$flow['StaffAs'];
				$Data['approval_type']=$flow['approval_type'];
				$Data['Sequence']=1;
				$Data['remark']='';
				$Data['ApprovalStatus']="0";
				$Data['Approved_by']=$flow['IdStaffApproving'];
				$date=date_create(date('Y-m-d'));
				date_add($date,date_interval_create_from_date_string($indays.' days'));
				$Data['dtlatest']=date_format($date,'Y-m-d');
				$Data['Id_User']=$auth->getIdentity()->appl_id;
				$this->dbApproval->addData($Data);
				$this->dbNilaiSyarat->deleteDataAll($idProposal);
				if (isset($formData['subject'])) {
					foreach ($formData['subject'] as $index=>$item) {
						$grade=$formData['grade'][$index];
						$data=array('grade_name'=>$grade,
								'IdSubject'=>$item,
								'IdTA'=>$idProposal,
								'Id_User'=>$auth->getIdentity()->appl_id
						);
						//$this->dbNilaiSyarat->deleteDataSubject($idProposal,$item);
						$this->dbNilaiSyarat->addData($data);
					}
				}
	
			} else {
	
				$proposedBy=$formData['IdPengaju'];
				$formData['Id_User']= $auth->getIdentity()->appl_id;
				$idProposal=$formData['idProposal'];
				$idApplication=$formData['IdTAApplication'];
				$data = array(
						'title_bahasa' => trim($formData['title_bahasa']),
						'title' => trim($formData['title']),
						'problem1' => trim($formData['problem1']),
						'problem2'=> trim($formData['problem2']),
						'problem3' => trim($formData['problem3']),
						'ABSTRAK' => trim($formData['abstrak']),
						'STATUS_PENGAJU' => $formData['STATUS_PENGAJU'],
						'IdPengaju' => $formData['IdPengaju'],
						'IdProgram' => $formData['IdProgram'],
						'IdMajor' => $formData['IdMajor'],
						'IdBranch' => $formData['IdBranch'],
						'dt_update' => date('Y-m-d H:i:s'),
						'Id_User' =>  $formData['Id_User']
				);
				$this->dbProposal->updateData($data,$idProposal);
				$this->dbNilaiSyarat->deleteDataAll($idProposal);
				if (isset($formData['subject'])) {
					foreach ($formData['subject'] as $index=>$item) {
						$grade=$formData['grade'][$index];
						$data=array('grade_name'=>$grade,
								'IdSubject'=>$item,
								'IdTA'=>$idProposal,
								'Id_User'=>$auth->getIdentity()->appl_id
						);
						//$this->dbNilaiSyarat->deleteDataSubject($idProposal,$item);
						$this->dbNilaiSyarat->addData($data);
					}
				}
			}
				
		}
	
		if ($this->dbApproval->isApproved($idApplication)) $this->view->approved='1';
		else $this->view->approved='0';
	
	
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($proposedBy);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		//get transkrip
		$dbStdRegSub=new Examination_Model_DbTable_StudentRegistrationSubject();
		$transkrip=$dbStdRegSub->getTranscriptList($proposedBy,$student['idTranscriptProfile']);
		$this->view->transkrip=$transkrip;
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$this->view->student=$student;
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student['IdLandscape']);
		//echo var_dump($subject);exit;
		if ($subject) {
			$this->view->idflowmain=$subject['IdTAFlowMain'];
			$idsubject=$subject['IdSubject'];
			//get current semester
			$dbsem=new App_Model_General_DbTable_Semestermaster();
			//echo var_dump($student);exit;
			$sem=$dbsem->getCurrentSemesterTA();
			if ($sem) $idsemester=$sem['IdSemesterMaster']; else $idsemester=0;
			//echo $idsemester.'='.$idsubject."-".$student['IdStudentRegistration'];exit;
			//cek KRS
			$dbReg=new App_Model_Exam_DbTable_StudentRegistrationSubject();
			if ($idsemester!=0) {
				if (!$dbReg->isRegistered($student['IdStudentRegistration'], $idsemester, $idsubject)) {
					//cek prerequisite
					$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
					$this->view->prerequisites = $subject_prerequisite;
				} else $this->view->prerequisites=array('0'=>array('completed'=>'1',
																	'SubjectName'=>'','PrerequisiteType'=>'',
																	'PrerequisiteGrade'=>'',
																	'Grade'=>'',
																	'Ok'=>'Prasyarat sudah diperiksa pada saat pengisian KRS'));
			} else $this->view->prerequisites=array('0'=>array('completed'=>'0',
																'SubjectName'=>'',
																'PrerequisiteType'=>'',
																'PrerequisiteGrade'=>'',
																'Grade'=>'',
																'Ok'=>'Semester tidak ada yang aktif, hubungi admin fakultas'));
			//echo var_dump($subject_prerequisite);exit;
			//$this->view->prerequisiteStatus=$completed;
		} else $this->view->prerequisites=array('0'=>array('completed'=>'0','SubjectName'=>'',
																	'PrerequisiteGrade'=>'','PrerequisiteType'=>'',
																	'Grade'=>'',
																	'Ok'=>'Work Flow TA belum dibuat, aplikasi Tugas Akhir/Skripsi belum bisa dilakukan secara online'));
		$proposal=$this->dbProposal->fnGetProposalByOwner($proposedBy,$idApplication,null);
		$this->view->proposal=$proposal;
		if ($proposal) $nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($proposal['IdTA']);
		else $nilaisyarat=array();
		//echo $idApplication.'-'.$proposedBy;echo var_dump($proposal);exit;
		$this->view->subject_list=$nilaisyarat;
		$this->view->process_status=$this->dbApproval->fnGetOpenStatusProcess($idApplication);
	
	}
	
	public function studentApplicationViewAction() {
		$this->view->title = $this->view->translate("Proposed Final Assingment/Thesis/Disertasion");
		$auth = Zend_Auth::getInstance();
		$proposedBy = $this->_getParam('proposedby', null);
		$idtaapplication = $this->_getParam('idtaapplication', null);
		if ($this->dbApproval->isApproved($idtaapplication)) $this->view->approved='1';
		else $this->view->approved='0';
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($proposedBy);
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		if($student["majoring"]=="common"||$student["majoring"]=="bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
		 
		//======================
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
		
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
					
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
		
				$this->view->photo_url  = $photo_url;
			}else{
				$this->view->photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$this->view->photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
		}
		//=========================
		
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student["IdLandscape"]);
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
		//=================
		$this->view->prerequisites=$subject_prerequisite;
		//$this->view->prerequisiteStatus=$completed;
		$this->view->student=$student;
		$this->view->proposal=$this->dbProposal->fnGetProposalByOwner($proposedBy,$idtaapplication,null);
		$nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($this->view->proposal['IdTA']);
		/*foreach($nilaisyarat as $key=>$item) {
			$id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($proposedBy, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}*/
		$this->view->subject_list=$nilaisyarat;
	}	
	public function studentApplicationPdfAction() {
		
		global $prerequisites;
		global $prerequisiteStatus;
		global $proposal;
		global $subject_list;
		//$this->view->title = $this->view->translate("Proposed Final Assingment/Thesis/Disertasion");
		$auth = Zend_Auth::getInstance();
		$proposedBy = $this->_getParam('proposedby', null);
		$idtaapplication = $this->_getParam('idtaapplication', null);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		global $student;
		$student = $studentRegDB->getStudentInfo($proposedBy);
		if($student["majoring"]=="common"||$student["majoring"]=="bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}

		//get info college
		$collegedB = new App_Model_General_DbTable_Collegemaster();
		$college = $collegedB->getFullInfoCollege($student["IdCollege"]);
		
		//======================
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
		//=========================
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowNameByProgram($student['IdProgram'],$student['IdProgramMajoring'],$student['IdBranch'],'Proposal',$student['IdLandscape']);
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$subject);
		//=================
		$prerequisites=$subject_prerequisite;
	 	//echo var_dump($prerequisites);exit;
		//$prerequisiteStatus=$completed;
		
		$proposal=$this->dbProposal->fnGetProposalByOwner($proposedBy,$idtaapplication,null);
		$nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($proposal['IdTA']);
		
		foreach($nilaisyarat as $key=>$item) {
			$id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($proposedBy, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}
		$subject_list=$nilaisyarat;
		//echo var_dump($prerequisites);echo "=======";echo var_dump($nilaisyarat);exit;
		$fieldValues = array(
				'$[PROGRAM]'=>$student["ArabicName"],
				'$[FACULTY]'=>'FAKULTAS '.$student["NamaKolej"],
				'$[NIM]'=>$student["registrationId"],
				'$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[PHOTO]'=>$photo_url,
				'$[ADD]'=>$college["Add1"].' '.$college["Add2"].' '.ucwords(strtolower($college["CityName"])).' '.$college["StateName"],
				'$[PHONE]'=>$college["Phone1"],
				'$[EMAIL]'=>$college["Email"],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
		);
		//filesysrtem
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path
		$html_template_path = DOCUMENT_PATH."/template/student_application_pdf.html";
		
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
		$output_directory_path = DOCUMENT_PATH."/student/finalassignment/proposal";
		
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "proposal_".$student['registrationId'].".pdf";
		
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		//save file address
		$db = new Finalassignment_Model_DbTable_Application();
		$db->updatefile( array('printed_url'=>'/documents/student/finalassignment/proposal/'.$output_filename),$proposal['IdTAApplication']);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'student-proposal-list', 'proposedby'=>$student['IdStudentRegistration'] ),'default',true));
		
	}
	
	public function staffApprovalPdfAction() {
		$idtaapproval=$this->_getParam('id', null);
		$idstaff=$this->_getParam('idstaff', null);
		$proposedBy = $this->_getParam('proposedby', null);
		$idtaapplication = $this->_getParam('idtaapplication', null);
		$this->generateStaffApprovalPdf($idtaapproval,$idstaff,$proposedBy,$idtaapplication);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal', 'action'=>'staff-proposal-list', 'id'=>$idstaff ),'default',true));
	}
	
	public function generateStaffApprovalPdf($idtaapproval,$idstaff,$proposedBy,$idtaapplication) {
	
		global $prerequisites;
		global $prerequisiteStatus;
		global $proposal;
		global $subject_list;
		global $staff;
		global $change;
		global $proposal_new;
		global $supervisors;
		//$this->view->title = $this->view->translate("Proposed Final Assingment/Thesis/Disertasion");
		$auth = Zend_Auth::getInstance();
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$staff=$dbStaff->getData($idstaff);
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$approvals=$this->dbApproval->getData($idtaapproval);
		$flow=$this->dbFlow->getData($approvals['IdTAFlow']);
		$idflowmain=$flow['IdTAFlowMain'];
		global $student;
		$student = $studentRegDB->getStudentInfo($proposedBy);
		if($student["majoring"]=="common"||$student["majoring"]=="bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
			
		//======================
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
				$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
		}
		//=========================
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		$flowmain=$this->dbFlowmain->getFlowName($idflowmain);
		//cek prerequisite
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student,$flowmain);
		//=================
		$prerequisites=$subject_prerequisite;
		//$prerequisiteStatus=$completed;
	
		$proposal=$this->dbProposal->fnGetProposalByOwner($proposedBy,$idtaapplication,$idtaapproval);
		if ($flowmain['StageCode']=='Change') {
			$proposal_new=$this->dbChange->getDataByStudent($proposedBy, $idtaapplication);
		}
		$approvelstaff=$this->dbApproval->getApprovedStaff($idtaapplication);
		$proposal['approvalstaff']=$approvelstaff;
		
		$proposal['ActivityName']=$flowmain['ActivityName'];
		//echo var_dump($flowmain);
		//echo var_dump($proposal);exit;
		//propose to dean
		$proposestaff=$this->dbApproval->getProposedApproval($idtaapplication);
		$proposal['proposestaff']=$proposestaff;
		//echo var_dump($approvelstaff);exit;
		$idTaFlow=$proposal['IdTAFlow'];
		$flow=$this->dbFlow->getData($idTaFlow);
		//get supervisor
		$supervisors=$this->dbSupervisor->getAllSupervisor($idtaapplication);
		foreach ( $supervisors as $key=>$item) {
			$staffsuper=$this->dbStaff->getData($item['IdStaff']);
			$supervisors[$key]['Staff']=$staffsuper;
		}
		
		//-=============
		//get dean decree
		$iddecree=$proposal['Dean_Decree'];
		$proposal['deandecree']=$this->dbDeandecree->getData($iddecree);
		
		$nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($proposal['IdTA']);
		foreach($nilaisyarat as $key=>$item) {
			$id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($proposedBy, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}
		$subject_list=$nilaisyarat;
	
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
		$html_template_path = DOCUMENT_PATH."/template/".$flow['report_profile'];
	
		$html = file_get_contents($html_template_path);
			
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		 
		$output_directory_path = DOCUMENT_PATH."/student/finalassignment/proposal";
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "app".$idtaapproval."_".$student['registrationId'].".pdf";
	
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
	
		file_put_contents($output_file_path, $dompdf);
	
		$this->view->file_path = $output_file_path;
		//save file address
		$db = new Finalassignment_Model_DbTable_Approval();
		$db->updateFile(array('printed_url'=>'/documents/student/finalassignment/proposal/'.$output_filename),$idtaapproval);
		
	
	}
	public function indexStaffAction() {
		$this->view->title="Staff Master"; 	//title
		$lobjform = new App_Form_Search (); //intialize search lobjstaffmasterForm
		$this->view->lobjform = $lobjform; //send the lobjstaffmasterForm object to the view
	
		$lobjStaffmaster = new GeneralSetup_Model_DbTable_Staffmaster(); //staffmaster model object
	
		 
	
		$lobjLevelList = $lobjStaffmaster->fnGetLevelList();
		$lobjform->field5->addMultiOptions($lobjLevelList);
		if($this->gobjsessionsis->UserCollegeId == '0') {
			$larrresult = $lobjStaffmaster->fngetStaffDetails(); //get staffmaster details
		} else {
			$larrresult = $lobjStaffmaster->fngetUserStaffDetails($this->gobjsessionsis->UserCollegeId); //get staffmaster details
		}
	
		if(!$this->_getParam('search'))
			unset($this->gobjsessionsis->staffpaginatorresult);
		 
		$lintpagecount = $this->gintPageCount;
		$lobjPaginator = new App_Model_Common(); // Definitiontype model
		$lintpage = $this->_getParam('page',1); // Paginator instance
	
		$sessionID = Zend_Session::getId();
		$lobjStaffmaster->fnDeleteTempStaffSubjectsDetailsBysession($sessionID);
	
		if(isset($this->gobjsessionsis->staffpaginatorresult)) {
			$this->view->paginator = $lobjPaginator->fnPagination($this->gobjsessionsis->staffpaginatorresult,$lintpage,$lintpagecount);
		} else {
			$this->view->paginator = $lobjPaginator->fnPagination($larrresult,$lintpage,$lintpagecount);
		}
		if ($this->_request->isPost () && $this->_request->getPost ( 'Search' )) {
			$larrformData = $this->_request->getPost ();
			if ($lobjform->isValid ($larrformData)) {
				if($this->gobjsessionsis->UserCollegeId == '0'|| $this->gobjsessionsis->IdRole=='1') {
					$larrresult = $lobjStaffmaster->fnSearchStaff($lobjform->getValues ());
				} else {
					$larrresult = $lobjStaffmaster->fnSearchUserStaff($lobjform->getValues (),$this->gobjsessionsis->UserCollegeId); //get staffmaster details
				}
					
				$this->view->paginator = $lobjPaginator->fnPagination($larrresult,$lintpage,$lintpagecount);
				$this->gobjsessionsis->staffpaginatorresult = $larrresult;
			}
		}
		if ($this->_request->isPost () && $this->_request->getPost ( 'Clear' )) {
			//$this->_redirect($this->view->url(array('module'=>'generalsetup' ,'controller'=>'staffmaster', 'action'=>'index'),'default',true));
			$this->_redirect( $this->baseUrl . '/finalassignment/proposal/index-staff');
		}
	
	}
	public function approvalAction() {
		$stage = $this->_getParam('stage', null);
		$StaffAs = $this->_getParam('staffas', null);
		$this->view->StaffAs=$StaffAs;
		$this->view->title = $this->view->translate($stage." : Approval by ".$StaffAs);
		$idTaApproval = $this->_getParam('idTaApproval', null);
		
		$staff = $this->_getParam('staff', null);
		$auth = Zend_Auth::getInstance();
		
		if ($this->getRequest()->isPost()) {
		
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData); exit;
			 
			$idTaApproval=$formData['IdTaApproval'];
			$formData['Id_User']=$auth->getIdentity()->iduser;
			$id=$formData['IdPengaju'];
			if (isset($formData['supervisor'])) {
				$formData['Approved_by']=$formData['supervisor'];
				 
			}
			$stsacc=true;
			if (isset($formData['req'])) {
				//isnrt requisite
				for ($i=1;$i<=$formData['req'];$i++) {
					if ($formData['take'.$i] =='') {
						$stsacc=false;break;
					}
				}
				if ($stsacc) {
					
					//save prerequisite
					for ($i=1;$i<=$formData['req'];$i++) {
						$idreq=$formData['take'.$i];
						$postData=array('IdTAApplication'=>$formData['IdTAApplication'],
								'IdTAApproval'=>$formData['IdTaApproval'],
								'IdStudentRegistration'=>$formData['IdStudentRegistration'],
								'IdSyarat'=>$idreq,
								'dt_entry'=>date('Y-m-d h:i:sa'),
								'Id_User'=>$auth->getIdentity()->iduser
						);
						$this->dbReqStudent->addData($postData);
					}
				}
			}
			//echo var_dump($formData);exit;
			if ($stsacc) {
				if ($this->dbApproval->updateData($formData, $idTaApproval)) 
					$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal','action'=>'staff-proposal-list','id'=>$id),'default',true));
			}
				
		}
		//get approval staff for this proposal
		$approvals=$this->dbApproval->getData($idTaApproval);
		$this->view->statusapproval=$approvals['ApprovalType'];
		//echo var_dump($approvals);exit;
		$idTaFlow=$approvals['IdTAFlow'];
		$proposal=$this->dbApproval->fnGetProposal($idTaApproval);
		
		$proposedBy=$proposal['IdStudentRegistration'];
		$dbGrade=new Examination_Model_DbTable_StudentGrade();
		$gradestatus=$dbGrade->getStudentGradeInfo($proposedBy);
		$dbStudent=new Registration_Model_DbTable_Studentregistration();
		$student=$dbStudent->SearchStudent(array('Idstudentreg'=>$proposedBy));
		$student=$student[0];
		//======================
		$student['credithours']=$gradestatus['sg_cum_credithour'];
		$student['cgpa']=$gradestatus['sg_cgpa'];
		$student['credithoursall']=$gradestatus['sg_all_cum_credithour'];
		$student['cgpaall']=$gradestatus['sg_all_cgpa'];
		// get prerequisite
		$dbFlow=new Finalassignment_Model_DbTable_Flow();
		$flow=$dbFlow->getData($idTaFlow);
		$dbFlowmain=new Finalassignment_Model_DbTable_FlowMain();
		$subject=$dbFlowmain->getFlowName($flow['IdTAFlowMain']);
		$this->view->StageCode=$subject['StageCode'];
		$this->view->idflowmain=$subject['IdTAFlowMain'];
		
		$subject_prerequisite=$this->dbProposal->getPrerequsiteProposal($student, $subject);
		
		$status=$this->dbApproval->fnGetOpenStatusProcess($proposal['IdTAApplication']);
		
		foreach ($status as $key=>$item) {
			$idFlowMain=$item['IdTAFlowMain'];
			$flowmain=$this->dbFlowmain->getFlowName($idFlowMain);
			$status[$key]['Stage']=$flowmain['Stage'];
			$status[$key]['StageCode']=$flowmain['StageCode'];
		}
		$this->view->process_status=$status;
		$this->view->prerequisites=$subject_prerequisite;
		//$this->view->prerequisiteStatus=$completed;
		$this->view->student=$student;
		$this->view->IdProgram=$student['IdProgram'];
		$this->view->proposal=$proposal; 
		//cek for next approval
		$this->view->lastapproval='';
		$flow=$this->dbFlow->getNextFlow($proposal['IdTAFlowMain'], $proposal['Sequence']);
		
		if ($flow) {
			$this->view->nofspan=$flow['n_of_span'];
			$this->view->approvaltype=$flow['approval_type'];
			if ($flow['IdStaffApproving']=='0' && ($flow['ProcessName']=='Persetujuan Pembimbing')  ) {
				$this->view->cosupervisor_list=$this->dbSupervisormaster->getSupervisorData();
				$this->view->supervisor_list=$this->dbSupervisormaster->getSupervisorData('736');
				//echo var_dump($this->view->staff_list);exit;
			} else {
				$this->view->supervisor_list=array();
				$this->view->cosupervisor_list=array();
			}
		} else {
			//last approval
			
			$this->view->lastapproval='1';
		}
		$nilaisyarat=$this->dbNilaiSyarat->getAllSubjectPrerequisite($proposal['IdTA']);
		foreach($nilaisyarat as $key=>$item) {
			 $id=$item['IdTANilaiSyarat'];
			///get nilai
			$nilai=$this->dbStdRegSubject->getHighestMarkofAllSemesterNoStatus($proposedBy, $item['IdSubject']);
			$nilaisyarat[$key]['Grade_name']=$nilai['grade_name'];
		}
		$this->view->subject_list=$nilaisyarat;
		$this->view->reasonapproval=$this->dbChange->fnGetReasonType();
		$requisite=$this->dbReq->getDataByIdFlow($idTaFlow);
		//echo $idTaFlow;echo var_dump($requisite);exit;
		if ($requisite) $this->view->docrequisite=$requisite; else $this->view->docrequisite=null;
		///examination
		$dbExam=new Finalassignment_Model_DbTable_Examination();
		$exam=$dbExam->getDataByStudent($proposedBy, $proposal["IdTAApplication"]);
		if ($exam) $this->view->examapp=$exam; else $this->view->examapp=array();
		$examprofile=$this->dbexaminerComp->getDataByStudent($proposedBy,$proposal["IdTAApplication"] );
		if ($examprofile) {
			$this->view->examprofile=$examprofile;
			//get examiner list
			$this->examiner_list=$this->dbSupervisormaster->getExaminerData('Examiner');
			$this->panitera_list=$this->dbSupervisormaster->getExaminerData('Panitera');
		
		}
	}
	 
	public function supervisorSetupAction() {
		
		$this->view->title="Staff Master"; 	//title
		$lobjform = new App_Form_Search (); //intialize search lobjstaffmasterForm
		$this->view->lobjform = $lobjform; //send the lobjstaffmasterForm object to the view
	
		$lobjStaffmaster = new GeneralSetup_Model_DbTable_Staffmaster(); //staffmaster model object
	 
		$lobjLevelList = $lobjStaffmaster->fnGetLevelList();
		$lobjform->field5->addMultiOptions($lobjLevelList);
		$this->view->IdDeanDecree= $this->_getParam('iddeandecree', null);
		 
		$supervisorSts=$this->dbSupervisor->fnGetSupervisorStatus();
		$examiner=$this->dbSupervisor->fnGetExaminerStatus();
		$this->view->supervisorSts_list=$supervisorSts;
		$this->view->examinerSts_list=$examiner;
		 
		if ($this->_request->isPost () && $this->_request->getPost ( 'Search' )) {
			$larrformData = $this->_request->getPost ();
			if ($lobjform->isValid ($larrformData)) {
				if($this->gobjsessionsis->UserCollegeId == '0'|| $this->gobjsessionsis->IdRole=='1') {
					$larrresult = $lobjStaffmaster->fnSearchStaff($lobjform->getValues ());
				} else {
					$larrresult = $lobjStaffmaster->fnSearchUserStaff($lobjform->getValues (),$this->gobjsessionsis->UserCollegeId); //get staffmaster details
				}
					
				 $this->view->staff_list=$larrresult;
			}
		}
		else
		if ($this->_request->isPost () && $this->_request->getPost ( 'Clear' )) {
			//$this->_redirect($this->view->url(array('module'=>'generalsetup' ,'controller'=>'staffmaster', 'action'=>'index'),'default',true));
			$this->_redirect( $this->baseUrl . '/finalassignment/proposal/supervisor-setup');
		} else {
			 
				if ($this->_request->isPost()) {
					
					$formData = $this->getRequest()->getPost();
					$auth = Zend_Auth::getInstance();
			//echo var_dump($formData);exit;
			$postData['IdDeanDecree'] = $formData["IdDeanDecree"];
			 
			foreach ($formData["IdStaff"] as $idstaff) {
				$postData['IdStaff']=$idstaff;
				if ($formData["supervisor"][$idstaff]!='')
					 	$postData['Status_supervisor']=$formData['supervisor'][$idstaff];
				else $postData['Status_supervisor']=null;
				if (($formData["examiner"][$idstaff]!='')) 
					$postData['Status_examiner']=$formData['examiner'][$idstaff];
				else $postData['Status_examiner']=null;
					$postData['Id_User']=$auth->getIdentity()->appl_id;
				 
					if ($formData['IdTASupervisormaster'][$idstaff]!='') {
						$postData['active']="1";
						$id=$formData['IdTASupervisormaster'][$idstaff];
						echo $id;
						$this->dbSupervisor->updateData($postData, $id );
					} else
						if ($postData['Status_supervisor']!=null || $postData['Status_examiner']!=null)
							$this->dbSupervisor->addData($postData);
					 
			 
					
			}//end for
		 
			$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'proposal','action'=>'dean-decree-list'),'default',true));
	
		 
				}
					
		}
	
	}
	
	public function uploadLetterAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
	
		 
		$auth = Zend_Auth::getInstance();
		$iduser=$auth->getIdentity()->appl_id;
		$this->view->iduser=$iduser;
		 
		if ($this->getRequest ()->isPost ()) {
	
			$formData = $_POST;
			$idapproval=$formData['idTAApproval'];
			$apath = DOCUMENT_PATH."/student/finalassignment/".$formData['Approved_by'];
			 
			//create directory to locate file
			if (!is_dir($apath)) {
				//echo($apath);exit;
				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
			}
	
			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
					
				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
					
				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
					$flnamephoto = date('Ymdhs')."_".$idapproval.$ext_photo;
					$path_photograph = $apath."/".$flnamephoto;
					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
	
						$upd_photo = array(
								 
								'doc_url' => $path_photograph
						);
						 $this->dbApproval->updateFile($upd_photo, $idapproval);
						 
					}
	
				}
					
			}
	
			//}
	
		}
		$this->_redirect("/finalassignment/proposal/staff-proposal-list/id/".$formData['Approved_by']);
	
	}
	public function uploadStdApplicationAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
	
			
		$auth = Zend_Auth::getInstance();
		$iduser=$auth->getIdentity()->appl_id;
		$this->view->iduser=$iduser;
			
		if ($this->getRequest ()->isPost ()) {
	
			$formData = $_POST;
			$idapplication=$formData['idTAApplication'];
			$apath = DOCUMENT_PATH."/student/finalassignment/".$formData['IdStudentRegistration'];
	
			//create directory to locate file
			if (!is_dir($apath)) {
				//echo($apath);exit;
				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
			}
	
			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
					
				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
					
				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
					$flnamephoto = date('Ymdhs')."_".$idapproval.$ext_photo;
					$path_photograph = $apath."/".$flnamephoto;
					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
	
						$upd_photo = array(
									
								'doc_url' => $path_photograph
						);
						$this->dbTaApplication->updatefile($upd_photo, $idapplication);
							
					}
	
				}
					
			}
	
			//}
	
		}
		$this->_redirect("/finalassignment/proposal/student-proposal-list/id/".$formData['IdStudentRegistration']);
	
	}
	function getFileExtension($filename){
		return substr($filename, strrpos($filename, '.'));
	}
	 
	 
}

