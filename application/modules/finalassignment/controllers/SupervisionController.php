<?php 
class Finalassignment_SupervisionController extends Zend_Controller_Action {

	private $dbProposal;
	private $dbSupervision;
	private $dbSupervisor;
	private $dbApp;
	
	public function init() { //initialization function
		//$this->_gobjlog = Zend_Registry::get('log'); //instantiate log object
		$this->fnsetObj();
	}
	
	
	public function fnsetObj() {
		$this->dbApp=new Finalassignment_Model_DbTable_Application();
		$this->dbSupervision=new Finalassignment_Model_DbTable_Supervision();
		$this->dbProposal=new Finalassignment_Model_DbTable_Proposal();
		$this->dbSupervisor=new Finalassignment_Model_DbTable_Supervisor();
	}
	
	public function supervisionIndexAction() {
		$this->view->title = $this->view->translate("Supervision Activity");
		$idApplication = $this->_getParam('idApplication', null);
		$idstaff = $this->_getParam('IdStaff', null);
		$this->view->idstaff=$idstaff;
		$this->view->IdTAApplication=$idApplication;
		$auth = Zend_Auth::getInstance();
		$type = $this->_getParam('type', 'staff');
		$this->view->type=$type;
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
			$idApplication=$formData['IdTAApplication'];
			$this->view->IdTAApplication=$idApplication;
			if ($formData['type']=='Student') {
				 
				$data = array(
						'IdTAApplication'=>$idApplication,
						'Student_Note'=> $formData['stdnote'], 
						'chapter'=> $formData['chapter'], 
						'IdStaff'=>$formData['supervisor']
				); 
			} else {
				$data = array(
						'IdTAApplication'=>$idApplication,
						'Supervisor_Note'=> $formData['supervisornote'],
						'Percent_progress'=> $formData['progress'],
						'chapter_progress'=> $formData['chapter_progress'],
						//'IdStaff'=>$formData['IdStaff'] 
				);
			}
			
			if ($formData['IdTASupervision']=='') {
				$data['dt_entry']=date('Y-m-d H:i:sa');
				$data['Id_User']=$auth->getIdentity()->appl_id;
				if (!isset($formData['stdnote'])) $data['Student_Note']='';
				if ($formData['type']!='Student') $data['IdStaff']=$formData['IdStaff'];
				$this->dbSupervision->addData($data);
				
			} else {
				if ($formData['type']!='Student') {
					$data['dt_update'] = date('Y-m-d H:i:sa');
					$data['dt_bimbing'] = date('Y-m-d H:i:sa');
				}
				$this->dbSupervision->updateData($data, $formData['IdTASupervision']);
			}
		}
		$supervision=$this->dbSupervisor->getAllSupervisor($idApplication);
		foreach ($supervision as $key=>$item) {
			$idSupervisor=$item['IdStaff'];
			$supervision[$key][$idSupervisor]=$this->dbSupervision->getSupervisionByStaff($idApplication, $idSupervisor);
		}
		//echo var_dump($supervision);exit;
		$this->view->supervision_list=$supervision;
		$this->view->chapter_list=$this->dbSupervision->fnGetChapterName();
		$this->view->proposal=$this->dbApp->getApplication($idApplication);
	}
	
	public function supervisionViewAction() {
		$this->view->title = $this->view->translate("Supervision Activity");
		$idApplication = $this->_getParam('idApplication', null);
		$type = $this->_getParam('type', 'staff');
		$this->view->type=$type;
		$idstaff = $this->_getParam('IdStaff', null);
		
		$this->view->idstaff=$idstaff;
		$this->view->IdTAApplication=$idApplication;
		$auth = Zend_Auth::getInstance();
		$supervision=$this->dbSupervisor->getAllSupervisor($idApplication);
		foreach ($supervision as $key=>$item) {
			$idSupervisor=$item['IdStaff'];
			$supervision[$key][$idSupervisor]=$this->dbSupervision->getSupervisionByStaff($idApplication, $idSupervisor);
		}
		//echo var_dump($supervision);exit;
		$this->view->supervision_list=$supervision;
		$this->view->chapter_list=$this->dbSupervision->fnGetChapterName();
		$application=$this->dbApp->getApplication($idApplication);
		$dbStd=new Registration_Model_DbTable_Studentregistration();
		$student=$dbStd->SearchStudent(array('Idstudentreg'=>$application['IdStudentRegistration']));
		$application['registrationId']=$student[0]['registrationId'];
		$application['student_name']=$student[0]['student_name'];
		$this->view->proposal=$application;
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
			$this->_redirect( $this->baseUrl . '/finalassignment/supervision/index-staff');
		}
	
	}
	public function staffSupervisionListAction() {
	
	
		$status = $this->_getParam('status', null);
		$idSupervisor= $this->_getParam('id', null);
		if ($idSupervisor==null) {
			$auth = Zend_Auth::getInstance();
			$idSupervisor=$auth->getIdentity()->iduser;
		}
		$this->view->supervisor=$idSupervisor;
		 
		//title
		$this->view->title= $this->view->translate("Final Assignment/Thesis/Disertation Supervision");
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$this->view->staff=$dbStaff->getData($idSupervisor);
		$supervision_list = $this->dbSupervisor->getOpenSupervisionByStaff($idSupervisor); 
		$dbStd=new Registration_Model_DbTable_Studentregistration();
		foreach ($supervision_list as $key=>$item) {
			$student=$dbStd->SearchStudent(array('Idstudentreg'=>$item['IdStudentRegistration']));
			//echo var_dump($supervision_list);exit;
			$student=$student[0];
			$supervision_list[$key]['Student']=$student;
			$supervision_list[$key]['Progress']=$this->dbSupervision->getProgressByStudent($item['IdTAApplication'], $item['IdStudentRegistration']);
		}
		$this->view->opensupervision_list = $supervision_list;
		$supervision_list = $this->dbSupervisor->getFinishSupervisionByStaff($idSupervisor);
		 
		foreach ($supervision_list as $key=>$item) {
			$student=$dbStd->SearchStudent(array('Idstudentreg'=>$item['IdStudentRegistration']));
			$student=$student[0];
			$supervision_list[$key]['Student']=$student;
			$supervision_list[$key]['Progress']=$this->dbSupervision->getProgressByStudent($item['IdTAApplication'], $item['IdStudentRegistration']);
		}
		$this->view->finishsupervision_list = $supervision_list;
			
	}
	
	public function supervisionDeleteListAction() {
	
	
		$status = $this->_getParam('status', null);
		$idTASupervisor= $this->_getParam('IdTASupervisor', null);
		$idApplication=$this->_getParam('IdTAApplication', null);
		$this->dbSupervision->deleteData($idTASupervisor);
		$this->_redirect($this->view->url(array('module'=>'finalassignment','controller'=>'supervision','action'=>'supervision-index','idApplication'=>$idApplication),'default',true));
	}
	
	public function uploadEvidenceAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
	
			
		$auth = Zend_Auth::getInstance();
		$iduser=$auth->getIdentity()->iduser;
		$this->view->iduser=$iduser;
			
		if ($this->getRequest ()->isPost ()) {
	
			$formData = $_POST;
			$idsupervision=$formData['dialogIdTASupervision'];
			$idTAApplication=$formData['IdTAApplication'];
			$apath = DOCUMENT_PATH."/student/finalassignment/".$formData['IdStaff'];
	
			//create directory to locate file
			if (!is_dir($apath)) {
				//echo($apath);exit;
				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
			}
	
			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
					
				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
					
				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
					$flnamephoto = date('Ymdhs')."_".$idsupervision.$ext_photo;
					$path_photograph = $apath."/".$flnamephoto;
					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
	
						$upd_photo = array(
									
								'doc_url' => $path_photograph
						);
						$this->dbSupervision->updateFile($upd_photo, $idsupervision);
							
					}
	
				}
					
			}
	 
	
		}
		$this->_redirect("/finalassignment/supervision/supervision-index/idApplication/".$formData['IdTAApplication']."/IdStaff/".$formData['IdStaff']);
	
	}
	function getFileExtension($filename){
		return substr($filename, strrpos($filename, '.'));
	}
	
	public function supervisionDeleteAction() {
		$idtasupervision = $this->_getParam('IdTASupervision', null);
		$idtaapplication= $this->_getParam('IdTAApplication', null);
		$idstaff= $this->_getParam('idstaff', null);
		$type= $this->_getParam('type', null);
		$this->dbSupervision->deleteData($idtasupervision);
		$this->_redirect("/finalassignment/supervision/supervision-index/idApplication/".$idtaapplication."/IdStaff/".$idstaff."/type/".$type);
		
	}
}

?>