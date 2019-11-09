<?php 

class SkpmController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction() {
    
    	$this->view->title = $this->view->translate("Student Records");
    	$auth = Zend_Auth::getInstance();
    	$form = new GeneralSetup_Form_SearchStudent();
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
    	} else {
    
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
    
    public function skoreMainAction(){
    	$this->view->title = "Score Setup: Main";
    	$dbScoreMain=new App_Model_Skpm_DbTable_ScoreMain();
    	$this->view->scoremain_list=$dbScoreMain->getData();
    }
    
    public function skoreViewAction(){
    	$this->view->title = "Score Setup: Detail";
    	$idmain= $this->_getParam('id');
    	$this->view->idmain=$idmain;
    	$auth = Zend_Auth::getInstance();
    	$iduser=$auth->getIdentity()->iduser;
    	$this->view->iduser=$iduser;
    	$dbScoreMain=new App_Model_Skpm_DbTable_Score();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$this->view->idmain=$formData['idScoreMain'];
    		$iduser=$auth->getIdentity()->iduser;
    		$data=array(
    				'ActivityName'=>$formData['activity'],
    				'role'=>$formData['role'],
    				'level'=>$formData['level'],
    				'field'=>$formData['field'],
    				'achievment'=>$formData['achievment'],
    				'Document'=>$formData['document'],
    				'IdScoreMain'=>$formData['idScoreMain'],
    				'skore'=>$formData['skore'],
    				'dt_entry' => date('Y-m-d H:i:sa'),
    				'IdUser' =>  $iduser,
    				
    		);
    		if ($formData['idScore']!='') $dbScoreMain->updateData($data, $formData['idScore']);
    		else $dbScoreMain->addData($data);
    	}
    	
    	$this->view->score_list=$dbScoreMain->getDataByIdMain($idmain);
    	$dbHonors=new App_Model_Skpm_DbTable_Academic();
    	$this->view->level_list = $dbHonors->fnGetLevelHonors();
    	$this->view->field_list = $dbHonors->fnGetFieldsActivitySetup();
    	$this->view->role_list = $dbHonors->fnGetRole();
    	$this->view->achievment_list = $dbHonors->fnGetAchievment();
    }
    
    public function deleteSkoreAction(){
    
    	$id = $this->_getParam('id', 0);
    	$idmain = $this->_getParam('idmain', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Score(); 
    
    	$dbHonors->deleteData($id);
    
    	$this->_redirect("/default/skpm/skore-view/id/".$idmain);
    
    
    
    
    }
    public function addScoreMainAction(){
    
    	$this->view->title = "Score Main Setup: New";
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	$iduser=$auth->getIdentity()->iduser;
    	$this->view->iduser=$iduser;
    	
    	 
    	 
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$data=array('IdSemesterMain'=>$formData['IdSemesterMain'],
    				'IdProgram'=>$formData['IdProgram'],
    				'IdBranch'=>$formData['IdBranch'],
    				'remark'=>$formData['remark'] 
    		);
    		$dbMain=new App_Model_Skpm_DbTable_ScoreMain();
    		$id=$dbMain->addData($data);
    
    		$this->_redirect("/default/skpm/skore-main");
    
    
    	}
    	$dbProgram=new GeneralSetup_Model_DbTable_Program();
    	$dbBranch=new GeneralSetup_Model_DbTable_Branchofficevenue();
    	$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
    	
    	//get program
    	$this->view->program_list=$dbProgram->getAllProgramByRole();
    	//get branch
    	$this->view->branch_list=$dbBranch->fnGetAllBranchList();
    	//get semester
    	$this->view->semester_list=$dbSem->fnGetAllSemestermasterList();
    	 
    	 
    
    }
    
    public function skpmAction()
    {
    	 
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	 
    	//print_r($auth->getIdentity());
    	 
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	
    	$this->view->title = "Perekaman Kegiatan Mahasiswa FK dalam SKPM ";
    	$this->view->registrationId = $registration_id;
    	$dbStudent=new App_Model_Registration_DbTable_Studentregistration();
    	$std=$dbStudent->getStudentRegistrationDetail($registration_id);
    	$this->view->nim = $std['registrationId'];
    	$this->view->studentname =$std['appl_fname'].' '.$std['appl_lname'];
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester_list=$dbGeneral->getCountableSemester();
    	//echo var_dump($this->view->semester_list);exit;
    }
    public function viewResultAction()
    {
    
    	$this->_helper->layout->disableLayout();
    
    	$this->view->title = "Hasil SKPM";
    	$this->view->registrationId = $this->_getParam('id');
    	$this->view->nim = $this->_getParam('nim');
    	$this->view->studentname = $this->_getParam('name');
    	$idsemester = $this->_getParam('IdSemesterMain');
    	$this->view->semesterid=$idsemester;
    	//get Utama
    	$dbAcademic=new App_Model_Skpm_DbTable_Academic();
    	$this->view->utama=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Utama',null,$idsemester,null);
    	//get ilmiah
    	$this->view->academic_list=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Penunjang','1',$idsemester,null);
    	 
    	//get minat
    	$this->view->minat_list=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Penunjang','2',$idsemester,null);
    	//get organisasi
    	$this->view->org_list=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Penunjang','3',$idsemester,null);
    	//echo var_dump($this->view->org_list);exit;
    	//get pkm
    	$this->view->pkm_list=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Penunjang','4',$idsemester,null);
    	//get other
    	$this->view->other_list=$dbAcademic->getDataMainbyStudent($this->view->registrationId, 'Penunjang','5',$idsemester,null);
    }
    
    public function cetakDraftSkpmAction()
    {
    
    	$this->_helper->layout->disableLayout();
    	global $nim;
    	global $semester;
    	global $registrationId;
    	global $studentname;
    	global $org_list;
    	global $pkm_list;
    	global $academic_list;
    	global $minat_list;
    	global $other_list; 
    	global $utama;
    	global $stdadvisor;
    	
    	$registrationId = $this->_getParam('id');
    	$semesterid = $this->_getParam('IdSemesterMain');
    	 
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	if ($semesterid>0)  {
	    	$row=$dbGeneral->fngetSemestermainDetails($semesterid);
	    	$semester=$row[0]['SemesterMainName'];
    	} else $semester='Semester Kumulatif';
    	
    	$nim = $this->_getParam('nim');
    	$studentname = $this->_getParam('name');
    	//get student advisor
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$advisor=$dbStd->getStudentAdvisor($registrationId);
    	if ($advisor) {
    		if ($advisor['FullName']=='') $name=$advisor['AdvisorName']; else $name=$advisor['FullName'];
    		if ($advisor['GelarDepan']!='') $name=$advisor['GelarDepan'].' '.$name;
    		if ($advisor['GelarBelakang']!='') $name=$name.', '.$advisor['GelarBelakang'];
    		$stdadvisor['Name']=$name;
    		$stdadvisor['NIK']='NIK :'.$advisor['StaffId'].'/USAKTI';
    	} else {
    		$stdadvisor['Name']="-";
    		$stdadvisor['NIK']="-";
    	}
    	//get Utama
    	 
    	$dbAcademic=new App_Model_Skpm_DbTable_Academic();
    	$utama=$dbAcademic->getDataMainbyStudent($registrationId, 'Utama',null,$semesterid,null);
    	//get ilmiah
    	$academic_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','1',$semesterid,null);
    
    	//get minat
    	$minat_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','2',$semesterid,null);
    	//get organisasi
    	$org_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','3',$semesterid,null);
    	//echo var_dump($this->view->org_list);exit;
    	//get pkm
    	$pkm_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','4',$semesterid,null);
    	//get other
    	$other_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','5',$semesterid,null);
    	
    	
    	require_once 'dompdf_config.inc.php';
    	
    	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    	$autoloader->pushAutoloader('DOMPDF_autoload');
    	
    	//template path
    	$html_template_path = DOCUMENT_PATH."/template/cetak-draft-skpm.html";
    	
    	$html = file_get_contents($html_template_path);
    	 
    		
    	$dompdf = new DOMPDF();
    	$dompdf->load_html($html);
    	$dompdf->set_paper('a4', 'potrait');
    	$dompdf->render();
    	
    	//echo $html;exit;
    	$output_directory_path = DOCUMENT_PATH."/student/skpm";
    	
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775,true);
    	}
    	//output filename
    	$output_filename = "skpm_".$nim.".pdf";
    	
    	//$dompdf = $dompdf->output();
    	$dompdf->stream($output_filename);		
    		
    	//to rename output file
    	$output_file_path = $output_directory_path.'/'.$output_filename;
    	
    	file_put_contents($output_file_path, $dompdf);
    	
    	$this->view->file_path = $output_file_path;
    	//save file address
    	exit;
    	
    
    
    
    }
    public function cetakSkpmAction()
    {
    
    	$this->_helper->layout->disableLayout();
    	global $nim;
    	global $semester;
    	global $registrationId;
    	global $studentname;
    	global $org_list;
    	global $pkm_list;
    	global $academic_list;
    	global $minat_list;
    	global $other_list; 
    	global $utama;
    	global $stdadvisor;
    	
    	$registrationId = $this->_getParam('id');
    	$semesterid = $this->_getParam('IdSemesterMain');
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	if ($semesterid>0)  {
	    	$row=$dbGeneral->fngetSemestermainDetails($semesterid);
	    	$semester=$row[0]['SemesterMainName'];
    	} else $semester='Semester Kumulatif';
    	
    	$nim = $this->_getParam('nim');
    	$studentname = $this->_getParam('name');
    	//get student advisor
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$advisor=$dbStd->getStudentAdvisor($registrationId);
    	if ($advisor) {
    		if ($advisor['FullName']=='') $name=$advisor['AdvisorName']; else $name=$advisor['FullName'];
    		if ($advisor['GelarDepan']!='') $name=$advisor['GelarDepan'].' '.$name;
    		if ($advisor['GelarBelakang']!='') $name=$name.', '.$advisor['GelarBelakang'];
    		$stdadvisor['Name']=$name;
    		$stdadvisor['NIK']='NIK :'.$advisor['StaffId'].'/USAKTI';
    	} else {
    		$stdadvisor['Name']="-";
    		$stdadvisor['NIK']="-";
    	}
    	//get Utama
    	$dbAcademic=new App_Model_Skpm_DbTable_Academic();
    	$utama=$dbAcademic->getDataMainbyStudent($registrationId, 'Utama',null,$semesterid,'1');
    	//get ilmiah
    	$academic_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','1',$semesterid,'1');
    
    	//get minat
    	$minat_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','2',$semesterid,'1');
    	//get organisasi
    	$org_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','3',$semesterid,'1');
    	//echo var_dump($this->view->org_list);exit;
    	//get pkm
    	$pkm_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','4',$semesterid,'1');
    	//get other
    	$other_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','5',$semesterid,'1');
    	
    	
    	require_once 'dompdf_config.inc.php';
    	
    	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    	$autoloader->pushAutoloader('DOMPDF_autoload');
    	
    	//template path
    	$html_template_path = DOCUMENT_PATH."/template/cetak-skpm.html";
    	
    	$html = file_get_contents($html_template_path);
    	 
    		
    	$dompdf = new DOMPDF();
    	$dompdf->load_html($html);
    	$dompdf->set_paper('a4', 'potrait');
    	$dompdf->render();
    	
    	//echo $html;exit;
    	$output_directory_path = DOCUMENT_PATH."/student/skpm";
    	
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775,true);
    	}
    	//output filename
    	$output_filename = "skpm_".$nim.".pdf";
    	
    	//$dompdf = $dompdf->output();
    	$dompdf->stream($output_filename);		
    		
    	//to rename output file
    	$output_file_path = $output_directory_path.'/'.$output_filename;
    	
    	file_put_contents($output_file_path, $dompdf);
    	
    	$this->view->file_path = $output_file_path;
    	//save file address
    	exit;
    	}
    	
    	public function cetakSkpmTranskripAction()
    	{
    	
    		$this->_helper->layout->disableLayout();
    		global $nim;
    		global $semester;
    		global $registrationId;
    		global $studentname;
    		global $org_list;
    		global $pkm_list;
    		global $academic_list;
    		global $minat_list;
    		global $other_list;
    		global $utama;
    		global $stdadvisor;
    		
    		$registrationId = $this->_getParam('id');
    		$semesterid = $this->_getParam('IdSemesterMain');
    		
    		$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    		if ($semesterid>0)  {
		    	$row=$dbGeneral->fngetSemestermainDetails($semesterid);
		    	$semester=$row[0]['SemesterMainName'];
    		} else $semester='Semester Kumulatif';
    	
    		$nim = $this->_getParam('nim');
    		$studentname = $this->_getParam('name');
    		 
    		//get student
    		
    		$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    		$std=$dbStd->getStudentRegistrationDetail($registrationId);
    		//echo var_dump($std);exit;
    		$dbPrg=new App_Model_General_DbTable_Program();
    		$program=$dbPrg->fngetProgramData($std['IdProgram']);
    		 
    		$deanDB = new App_Model_General_DbTable_Deanmaster();
    		$dean = $deanDB->getCollegeDean($program['IdCollege']);
    		 
    			
    		//get salutatuion
    		$definationsDb = new App_Model_General_DbTable_Definationms();
    		$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
    		$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
    		
    		$deanName=$dean['Fullname'];
    		if (isset($FrontSalutation['DefinitionDesc'])) {
    			$deanName=$FrontSalutation['DefinitionDesc'].' '.$deanName;
    		}
    		if (isset($BackSalutation['DefinitionDesc'])) {
    			$deanName=$deanName.', '.$BackSalutation['DefinitionDesc'];
    		}
    		
    		$stdadvisor['Name']=$deanName;
    		$stdadvisor['NIK']='NIK : '.$dean['StaffId'].'/USAKTI';
    		//get Utama
    		$dbAcademic=new App_Model_Skpm_DbTable_Academic();
    		$utama=$dbAcademic->getDataMainbyStudent($registrationId, 'Utama',null,$semesterid,'1');
    		//get ilmiah
    		$academic_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','1',$semesterid,'1');
    	
    		//get minat
    		$minat_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','2',$semesterid,'1');
    		//get organisasi
    		$org_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','3',$semesterid,'1');
    		//echo var_dump($this->view->org_list);exit;
    		//get pkm
    		$pkm_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','4',$semesterid,'1');
    		//get other
    		$other_list=$dbAcademic->getDataMainbyStudent($registrationId, 'Penunjang','5',$semesterid,'1');
    		 
    		 
    		require_once 'dompdf_config.inc.php';
    		 
    		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    		$autoloader->pushAutoloader('DOMPDF_autoload');
    		 
    		//template path
    		$html_template_path = DOCUMENT_PATH."/template/cetak-skpm-transkrip.html";
    		 
    		$html = file_get_contents($html_template_path);
    	
    	
    		$dompdf = new DOMPDF();
    		$dompdf->load_html($html);
    		$dompdf->set_paper('a4', 'potrait');
    		$dompdf->render();
    		 
    		//echo $html;exit;
    		$output_directory_path = DOCUMENT_PATH."/student/skpm";
    		 
    		//create directory to locate file
    		if (!is_dir($output_directory_path)) {
    			mkdir($output_directory_path, 0775,true);
    		}
    		//output filename
    		$output_filename = "skpm_".$nim.".pdf";
    		 
    		//$dompdf = $dompdf->output();
    		$dompdf->stream($output_filename);
    	
    		//to rename output file
    		$output_file_path = $output_directory_path.'/'.$output_filename;
    		 
    		file_put_contents($output_file_path, $dompdf);
    		 
    		$this->view->file_path = $output_file_path;
    		//save file address
    		exit;
    	}
    	
    public function studentinfoAction()
    {
    	$this->view->title = "";
    	$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
    	
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    	$this->view->registrationId = $this->_getParam('id');
    	$this->view->nim = $this->_getParam('nim');
    	$this->view->studentname = $this->_getParam('name');
    	$student_grade = $studentGradeDB->getStudentGradeInfo($this->view->registrationId);
    	$this->view->student_grade = $student_grade;
    	
    }
    
    
    public function organisasiAction()
    {
    
    	$this->view->title = "";
    	
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	
    	//print_r($auth->getIdentity());
    	
    	$iduser=$auth->getIdentity()->registration_id;
    	$this->view->iduser=$iduser;
    	$idsemester = $this->_getParam('IdSemesterMain');
    	$this->view->semesterid=$idsemester;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	
    	$dbOrg=new App_Model_Skpm_DbTable_Organisasi();
    	
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$this->view->idOrganisasi = $formData['idOrganisasi'];
    		$formData['id_user']= $iduser;
    		$registration_id=$formData['idStudentRegistration'];
    		$nim=$formData['nim'];
    		$studentname=$formData['studentname'];
    		//echo var_dump($formData);exit;
    		$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    		$std=$dbStd->getStudentRegistrationDetail($registration_id);
    		$dbScore=new App_Model_Skpm_DbTable_Score();
    		
    		$post=array(
    				'level'=>$formData['level_org'],
    				'field'=>$formData['field_org'],
    				'role'=>$formData['role_org'],
    				//'achievment'=>$formData['achievment_org'],
    				'IdProgram'=>$std['IdProgram'],
    				'IdBranch'=>$std['IdBranch'],
    				 
    		);
    		//echo var_dump($std);
    		//echo var_dump($post);exit;
    		$row=$dbScore->getScore($post);
    		if ($row) $formData['score']=$row['skore'];
    		if (isset($formData['idOrganisasi']) && $formData['idOrganisasi']!='') {
    	
    			$dbOrg->updateData($formData, $formData['idOrganisasi']);
    		}
    		else {
    	
    			$dbOrg->addData($formData);
    		}
    	
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab5");
    	
    	
    	}
    	$organisasis=$dbOrg->getDatabyStudent($registration_id,$idsemester);
    	
    	$dbupload=new App_Model_Skpm_DbTable_UploadFile();
    	foreach ($organisasis as $key=>$value) {
    		$idhonor=$value['idOrganisasi'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '103');
    		$path=$files['pathupload'];
    		$organisasis[$key]['path']=$path;
    	}
    	
    	$dbAcad=new App_Model_Skpm_DbTable_Academic();
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester_list=$dbGeneral->getCountableSemester();
    	$this->view->organisasis = $organisasis;
    	$this->view->level_list = $dbOrg->fnGetLevelHonors(); 
    	$this->view->year_list = $dbOrg->fnGetyear();
    	$this->view->field_list = $dbAcad->fnGetFieldsActivity("4");
    	$this->view->role_list = $dbAcad->fnGetRole("1");
    	$this->view->category_list = $dbAcad->fnGetCategory();
    	$this->view->bukti_list = $dbAcad->fnGetBukti(array("2","9"));
    }
     
     
    public function academicAction(){
    
    	$this->view->title = "";
    
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	$iduser=$auth->getIdentity()->registration_id;
    	$this->view->iduser=$iduser;
    	
    	$registration_id = $this->_getParam('id');
    	
    	$this->view->IdStudentRegistration = $registration_id;
    	$idsemester = $this->_getParam('IdSemesterMain');
    	$this->view->semesterid=$idsemester;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors=new App_Model_Skpm_DbTable_Academic();
    	
    	
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idHonors = $formData['idHonor'];
    			$formData['idUser']= $iduser;
    			$registration_id=$formData['idStudentRegistration'];
    			$nim=$formData['nim'];
    			$dbStd=new Registration_Model_DbTable_Studentregistration();
    			$std=$dbStd->getData($registration_id);
    			$studentname=$formData['studentname'];
    			$dbScore=new App_Model_Skpm_DbTable_Score();
    			$post=array(
    					'level'=>$formData['level'],
    					'field'=>$formData['field'],
    					'role'=>$formData['role'],
    					'achievment'=>$formData['achievment'],
    					'IdProgram'=>$std['IdProgram'],
    					'IdBranch'=>$std['IdBranch'],
    			);
    			$row=$dbScore->getScore($post);
    			if ($row) $formData['score']=$row['skore'];
    			if (isset($formData['idHonor']) && $formData['idHonor']!='') {
    				
    				$dbHonors->updateData($formData, $formData['idHonor']);
    			}
    			else {
    				
    				$dbHonors->addData($formData);
    			}
    			 
    			$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab2");
    		
    		
    	}

    	
    	$honors=$dbHonors->getDatabyStudent($registration_id,$idsemester);
    	 
    	$dbupload=new App_Model_Skpm_DbTable_UploadFile();
    	foreach ($honors as $key=>$value) {
    		$idhonor=$value['idIlmiah'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '101');
    		$path=$files['pathupload'];
    		$honors[$key]['path']=$path;
    	}
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester_list=$dbGeneral->getCountableSemester();
    	$this->view->honors = $honors;
    	$this->view->level_list = $dbHonors->fnGetLevelHonors(array('2','3'));
    	$this->view->field_list = $dbHonors->fnGetFieldsActivity("0");
    	$this->view->role_list = $dbHonors->fnGetRole(array('2','3','9'));
    	$this->view->achievment_list = $dbHonors->fnGetAchievment();
    	$this->view->bukti_list = $dbHonors->fnGetBukti(array("1","9"));
    	$this->view->category_list = $dbHonors->fnGetCategory();
    	 
    }
    public function academicApprovedAction(){
    
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Academic();
    	 
    	$dbHonors->approvedData($iduser, $idhonor);
    	 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab2");
    
    
    
    	 
    
    }
    public function academicRejectAction(){
    
    	 
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Academic();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors->rejectData($iduser, $idhonor);
   
    		 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab2");
    
    
    	
    
    
    }
    public function deleteAcademicAction(){
    
    	
        	 
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Academic();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    
        $dbHonors->deleteData($idhonor);
    		 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab2");
    
     
    	 
    
    }
    public function otherAction(){
    
    	$this->view->title = "";
    
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	$iduser=$auth->getIdentity()->registration_id;
    	$this->view->iduser=$iduser;
    	$idsemester = $this->_getParam('IdSemesterMain');
    	$this->view->semesterid=$idsemester;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors=new App_Model_Skpm_DbTable_Other();
    	$dbAcad=new App_Model_Skpm_DbTable_Academic();
    	 
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$this->view->idHonors = $formData['idHonor'];
    		$formData['idUser']= $iduser;
    		$registration_id=$formData['idStudentRegistration'];
    		$nim=$formData['nim'];
    		$studentname=$formData['studentname'];
    		$dbStd=new Registration_Model_DbTable_Studentregistration();
    		$std=$dbStd->getData($registration_id);
    		$studentname=$formData['studentname'];
    		$dbScore=new App_Model_Skpm_DbTable_Score();
    		$post=array(
    				'level'=>$formData['level_other'],
    				'field'=>$formData['field_other'],
    				'role'=>$formData['role_other'],
    				'achievment'=>$formData['achievment_other'],
    				'IdProgram'=>$std['IdProgram'],
    				'IdBranch'=>$std['IdBranch'],
    				 
    		);
    		$row=$dbScore->getScore($post);
    		if ($row) $formData['score']=$row['skore'];
    		if (isset($formData['idOther']) && $formData['idOther']!='') {
    
    			$dbHonors->updateData($formData, $formData['idOther']);
    		}
    		else {
    
    			$dbHonors->addData($formData);
    		}
    
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab6");
    
    
    	}
    
    	 
    	$honors=$dbHonors->getDatabyStudent($registration_id,$idsemester);
    
    	$dbupload=new App_Model_Skpm_DbTable_UploadFile();
    	foreach ($honors as $key=>$value) {
    		$idhonor=$value['idOther'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '105');
    		$path=$files['pathupload'];
    		$honors[$key]['path']=$path;
    	}
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester_list=$dbGeneral->getCountableSemester();
    	$this->view->others = $honors;
    	$this->view->level_list = $dbAcad->fnGetLevelHonors();
    	$this->view->category_list = $dbAcad->fnGetCategory();
    	$this->view->field_list = $dbAcad->fnGetFieldsActivity();
    	$this->view->role_list = $dbAcad->fnGetRole(array('8','9'));
    	$this->view->achievment_list = $dbAcad->fnGetAchievment();
    	$this->view->bukti_list = $dbAcad->fnGetBukti(array("5","9"));
    	 
    
    }
    public function otherApprovedAction(){
    
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Other();
    
    	$dbHonors->approvedData($iduser, $idhonor);
    
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab6");
    
    
    
    
    
    }
    public function otherRejectAction(){
    
    
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Other();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors->rejectData($iduser, $idhonor);
    	 
    	 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab6");
    
    
    	 
    
    
    }
    public function deleteOtherAction(){
    
    	 
    
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Other();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    
    	$dbHonors->deleteData($idhonor);
    	 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab6");
    
    	 
    
    
    }
    public function talentAction(){
    
    	$this->view->title = "";
    
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	$iduser=$auth->getIdentity()->registration_id;
    	$this->view->iduser=$iduser;
    	$idsemester = $this->_getParam('IdSemesterMain');
    	$this->view->semesterid=$idsemester;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors=new App_Model_Skpm_DbTable_Talent();
    	$dbAcad=new App_Model_Skpm_DbTable_Academic();
    	 
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$this->view->idHonors = $formData['idHonor_minat'];
    		$formData['idUser']= $iduser;
    		$registration_id=$formData['idStudentRegistration'];
    		$nim=$formData['nim'];
    		$studentname=$formData['studentname'];
    		$dbStd=new Registration_Model_DbTable_Studentregistration();
    		$std=$dbStd->getData($registration_id);
    		$dbScore=new App_Model_Skpm_DbTable_Score();
    		$post=array(
    				'level'=>$formData['level_minat'],
    				'field'=>$formData['field_minat'],
    				'role'=>$formData['role_minat'],
    				'achievment'=>$formData['achievment_minat'],
    				'IdProgram'=>$std['IdProgram'],
    				'IdBranch'=>$std['IdBranch'],
    				 
    		);
    		$row=$dbScore->getScore($post);
    		if ($row) $formData['score']=$row['skore'];
    		if (isset($formData['idHonor_minat']) && $formData['idHonor_minat']!='') {
    
    			$dbHonors->updateData($formData, $formData['idHonor_minat']);
    		}
    		else {
    
    			$dbHonors->addData($formData);
    		}
    
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab3");
    
    
    	}
    
    	
    	$honors=$dbHonors->getDatabyStudent($registration_id,$idsemester);
    	 
    	$dbupload=new App_Model_Skpm_DbTable_UploadFile();
    	
    	foreach ($honors as $key=>$value) {
    		$idhonor=$value['idMinat'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '102');
    		$path=$files['pathupload'];
    		$honors[$key]['path']=$path;
    	}
    	$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester_list=$dbGeneral->getCountableSemester();
    	$this->view->honors = $honors;
    	$this->view->level_list = $dbAcad->fnGetLevelHonors();
    	$this->view->field_list = $dbAcad->fnGetFieldsActivity("1");
    	$this->view->role_list = $dbAcad->fnGetRole(array('4','9'));
    	$this->view->achievment_list = $dbAcad->fnGetAchievment();
    	$this->view->category_list = $dbAcad->fnGetCategory();
    	$this->view->bukti_list = $dbAcad->fnGetBukti(array("4","9"));
    
    }
    public function talentApprovedAction(){
    
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Talent();
    
    	$dbHonors->approvedData($iduser, $idhonor);
    
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab3");
    
    
    
    
    
    }
    public function talentRejectAction(){
    
    
    	$auth = Zend_Auth::getInstance();
    	$iduser = $auth->getIdentity()->registration_id;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Talent();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    	$dbHonors->rejectData($iduser, $idhonor);
    	 
    	 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab3");
    
    
    	 
    
    
    }
    public function deleteTalentAction(){
    
    	 
    
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpm_DbTable_Talent();
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
    
    	$dbHonors->deleteData($idhonor);
    	 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab3");
    
    	 
    
    
    }
    public function uploadCertificateAction(){
    	/*
    	 * check session for transaction
    	*/
    	$auth = Zend_Auth::getInstance();
    	 
    	
    	$auth = Zend_Auth::getInstance();
    	$iduser=$auth->getIdentity()->registration_id;
    	$this->view->iduser=$iduser;
    	 
    	$registration_id = $this->_getParam('id');
    	$this->view->IdStudentRegistration = $registration_id;
    	$nim = $this->_getParam('nim');
    	$this->view->nim = $nim;
    	$studentname = $this->_getParam('name');
    	$this->view->studentname = $studentname;
     
    	if ($this->getRequest ()->isPost ()) {
    		
    		$formData = $_POST;
    		$docname=$formData['document_name'];
    		$Idregistration=$formData['idStudentRegistrationUp'];
    		$uploadfileDB = new App_Model_Skpm_DbTable_UploadFile();
    		$idhonor=$formData['items_id'];
    		$redirect=$formData['redirect_path'];
    		$type=$formData['type'];
    		
    			$apath = DOCUMENT_PATH."/student/skpm/".$Idregistration."/".$docname;
    			
    			//create directory to locate file
    			if (!is_dir($apath)) {
    				//echo($apath);exit;
    				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
    			}
    			 
    			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
    					
    				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
    					
    				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
    					$flnamephoto = date('Ymdhs')."_".$docname.$ext_photo;
    					$path_photograph = $apath."/".$flnamephoto;
    					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
    
	    					$upd_photo = array(
	    							'auf_idStudentRegistration' => $Idregistration,
	    							'auf_Items'=>$idhonor,
	    							'auf_file_name' => $flnamephoto,
	    							'auf_file_type' => $type,
	    							'auf_upload_date' => date("Y-m-d h:i:s"),
	    							'auf_upload_by' => $iduser,
	    							'pathupload' => $path_photograph
	    					);
	    					$files=$uploadfileDB->getFileItems($Idregistration,$idhonor,$type);
	    				   if (!$files) 
	    						$uploadfileDB->addData($upd_photo);
	    					else 
	    						$uploadfileDB->updateData($upd_photo,$files['auf_id']);
    					}
    
    				}
    					
    			}
    			 
    			//}
    			 
    		}
    		$this->_redirect($redirect);
    		
    	}
    	function getFileExtension($filename){
    		return substr($filename, strrpos($filename, '.'));
    	}
    	
    	public function organisasiApprovedAction(){
    	
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    	
    		//print_r($auth->getIdentity());
    	
    		$iduser = $auth->getIdentity()->registration_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Organisasi();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		$this->view->studentname = $studentname;
    		$dbOrg->approvedData($iduser, $idOrg);
    	
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab5");
    	
    	
    	
    	
    	
    	}
    	public function organisasiRejectAction(){
    	
    	
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    	
    		//print_r($auth->getIdentity());
    	
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Organisasi();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		$this->view->studentname = $studentname;
    		$dbOrg->rejectData($registration_id, $idOrg);
    		 
    		 
    				 
    	$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab5");
    	}
    	
    	public function deleteOrganisasiAction(){
    	
    		$this->view->title = "";
    	
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    	
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Organisasi();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		$this->view->studentname = $studentname;
    	
    		$dbOrg->deleteData($idOrg);
    		$url="/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab5";
    		//echo $url;exit;
    		$this->_redirect($url);
    	
    	}
    	 
    	
    	public function pkmAction()
    	{
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$iduser=$auth->getIdentity()->registration_id;
	    	$this->view->iduser=$iduser;
	    	$idsemester = $this->_getParam('IdSemesterMain');
	    	$this->view->semesterid=$idsemester;
	    	$registration_id = $this->_getParam('id');
	    	$this->view->IdStudentRegistration = $registration_id;
	    	$nim = $this->_getParam('nim');
	    	$this->view->nim = $nim;
	    	$studentname = $this->_getParam('name');
	    	$this->view->studentname = $studentname;
	    	
    		$dblang=new App_Model_Skpm_DbTable_Pkm();
    		 
    		if ($this->getRequest()->isPost()) {
    	
    	
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idSoftskill = $formData['idSoftskill'];
    			$formData['id_user']= $iduser;
    			$registration_id=$formData['idStudentRegistration'];
    			$nim=$formData['nim'];
    			$studentname=$formData['studentname'];
    			$dbStd=new Registration_Model_DbTable_Studentregistration();
    			$std=$dbStd->getData($registration_id);
    			$dbScore=new App_Model_Skpm_DbTable_Score();
    			$post=array(
    					'level'=>$formData['level_pkm'],
    					'field'=>$formData['field_pkm'],
    					'role'=>$formData['role_pkm'],
    					'achievment'=>$formData['achievment_pkm'],
    					'IdProgram'=>$std['IdProgram'],
    					'IdBranch'=>$std['IdBranch'],
    				 
    			);
    			$row=$dbScore->getScore($post);
    			if ($row) $formData['score']=$row['skore'];
    			if (isset($formData['idSoftskill']) && $formData['idSoftskill']!='') {
    					
    				$dblang->updateData($formData, $formData['idSoftskill']);
    			}
    			else {
    					
    				$dblang->addData($formData);
    			}
    	
    			$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab4");
    	
    	
    		}
    		$softskills=$dblang->getDatabyStudent($registration_id,$idsemester);
    		 
    		$dbupload=new App_Model_Skpm_DbTable_UploadFile();
    		foreach ($softskills as $key=>$value) {
    			$idhonor=$value['idSoftskill'];
    			$files=$dbupload->getFileItems($registration_id,$idhonor, '104');
    			$path=$files['pathupload'];
    			$softskills[$key]['path']=$path;
    		}
    		$dbAcad=new App_Model_Skpm_DbTable_Academic();
    		$this->view->softskills = $softskills;
    		$this->view->field_list = $dbAcad->fnGetFieldsActivity("3");
    		$this->view->role_list = $dbAcad->fnGetRole('9');
    		$this->view->level_list = $dbAcad->fnGetLevelHonors();
    		$this->view->category_list = $dbAcad->fnGetCategory();
    		$dbGeneral=new App_Model_General_DbTable_Semestermaster();
    		$this->view->bukti_list = $dbAcad->fnGetBukti(array("3","9"));
    		$this->view->semester_list=$dbGeneral->getCountableSemester();
    	}
    	
    	public function pkmApprovedAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$iduser = $auth->getIdentity()->registration_id;
    		 
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Pkm();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		$dbOrg->approvedData($iduser, $idlang);
    		 
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab4");
    		 
    		 
    		 
    		 
    		 
    	}
    	public function pkmRejectAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$iduser = $auth->getIdentity()->registration_id;
    		 
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Pkm();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		$dbOrg->rejectData($iduser, $idlang);
    		 
    		 
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab4");
    	}
    	 
    	public function deletePkmAction(){
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpm_DbTable_Pkm();
    		$registration_id = $this->_getParam('id');
    		$this->view->IdStudentRegistration = $registration_id;
    		$nim = $this->_getParam('nim');
    		$this->view->nim = $nim;
    		$studentname = $this->_getParam('name');
    		 
    		$dbOrg->deleteData($idlang);
    		 
    		$this->_redirect("/default/skpm/skpm/id/".$registration_id."/name/".$studentname."/nim/".$nim."/#Tab4");
    		 
    	}
    	 
    	public function approvalListAction(){
    		$this->view->title = $this->view->translate("SKPM Approval");
    		$auth = Zend_Auth::getInstance();
    		$form = new GeneralSetup_Form_SearchStudent();
    		$this->view->form = $form;
    		$dbhonor=new App_Model_Skpm_DbTable_Honors();
    		$dborg=new App_Model_Skpm_DbTable_Organisasi();
    		$dblang=new App_Model_Skpm_DbTable_Languange();
    		$dbSoft=new App_Model_Skpm_DbTable_Softskill();
    		$dbInternship=new App_Model_Skpm_DbTable_Internships();
    		if ($this->getRequest()->isPost()) {
    			 
    			$formData = $this->getRequest()->getPost();
    			 
    			$studentRegDB = new Registration_Model_DbTable_Studentregistration();
    			$student_list = $studentRegDB->getListStudent($formData);
    			foreach ($student_list as $key=>$student) {
    				$idstudentregistration=$student['IdStudentRegistration'];
    				// get honor
    				$items=$dbhonor->getDataApprovalbyStudent($idstudentregistration);
    				$unset='0';
    				$status='';
    				foreach ($items as $value) {
    					if ($value['status']=='0') {
    						$status=$value['count'];
    						$unset='1';
    						$status=$status.'/'.count($items);
    					}
    					
    				}
    				$student_list[$key]['Honors']=$status;
    				
    				//get org
    				$items=$dborg->getDataApprovalbyStudent($idstudentregistration);
    				$status='';
    				foreach ($items as $value) {
    					if ($value['status']=='0') {
    						$status=$value['count'];
    						$unset='1';
    						$status=$status.'/'.count($items);
    					}
    					
    				}
    				$student_list[$key]['Organisasi']=$status;
    				
    				//get Language
    				$items=$dblang->getDataApprovalbyStudent($idstudentregistration);
    				$status='';
    				foreach ($items as $value) {
    					if ($value['status']=='0') {
    						$status=$value['count'];
    						$unset='1';
    						$status=$status.'/'.count($items);
    					}
    					
    				}
    				$student_list[$key]['Language']=$status;
    				
    				//get Softskill
    				$items=$dbSoft->getDataApprovalbyStudent($idstudentregistration);
    				$status='';
    				foreach ($items as $value) {
    					if ($value['status']=='0') {
    						$status=$value['count'];
    						$unset='1';
    						$status=$status.'/'.count($items);
    					}
    					
    				}
    				$student_list[$key]['Softskill']=$status;
    				
    				//get Internship
    				$items=$dbInternship->getDataApprovalbyStudent($idstudentregistration);
    				$status='';
    				foreach ($items as $value) {
    					if ($value['status']=='0') {
    						$status=$value['count'];
    						$unset='1';
    						$status=$status.'/'.count($items);
    					}
    					
    				}
    				$student_list[$key]['Internship']=$status;
    				if ($unset=='0') unset($student_list[$key]);
    				
    			}
    			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_list));
    			//$paginator->setItemCountPerPage($this->gintPageCount);
    			$paginator->setItemCountPerPage(1000);
    			$paginator->setCurrentPageNumber($this->_getParam('page',1));
    			 
    			$form->populate($formData);
    			 
    			 
    			$this->view->paginator = $paginator;
    		}
    		
    	}
    	
    	public function studentListAction(){
    		$this->view->title = $this->view->translate("SKPI Printing");
    		$auth = Zend_Auth::getInstance();
    		$form = new default_Form_SkpmSearchForm();
    		$this->view->form = $form;
    		if ($this->getRequest()->isPost()) {
    			 
    			$formData = $this->getRequest()->getPost();
    			$form->populate($formData);
    			$this->view->status=$formData['Approval'];
    			$this->view->idprogram=$formData['IdProgram'];
    			//echo var_dump($formData);
    			$studentRegDB = new Registration_Model_DbTable_Studentregistration();
    			$this->view->student_list = $studentRegDB->getListStudent($formData);
    		}
    	}
    	public function viewSkpmAction(){
    		
    		$this->view->title = $this->view->translate("SKPI Printing");
    		$auth = Zend_Auth::getInstance();
    		//$form = new Graduation_Form_SkpmSearchForm();	
    		//$this->view->form = $form;
    		$dbhonor=new App_Model_Skpm_DbTable_Honors();
    		$dborg=new App_Model_Skpm_DbTable_Organisasi();
    		$dblang=new App_Model_Skpm_DbTable_Languange();
    		$dbSoft=new App_Model_Skpm_DbTable_Softskill();
    		$dbInternship=new App_Model_Skpm_DbTable_Internships();
    		$dbcp=new App_Model_Skpm_DbTable_Cp();
    		$dbFinal=new Finalassignment_Model_DbTable_FinalAssignment();
    		if ($this->getRequest()->isPost()) {
    	
    			$formData = $this->getRequest()->getPost();
    			//$form->populate($formData);
    			$status=$formData['approval'];
    			$this->view->status=$status;
    			//echo var_dump($formData);exit;
    			$studentRegDB = new Registration_Model_DbTable_Studentregistration();
    		
    			$dbCommon = new App_Model_Common();
    			$Dbgraduate = new Graduation_Model_DbTable_Graduation();
    			$dbUniv = new GeneralSetup_Model_DbTable_University();
    			$dbDean = new GeneralSetup_Model_DbTable_Deanmaster();
    			$dbHead = new GeneralSetup_Model_DbTable_Program();
    			
    			$dbBranch=new GeneralSetup_Model_DbTable_Branchofficevenue();
    			$dbLands=new GeneralSetup_Model_DbTable_Landscape();
    			$dbGrade=new Examination_Model_DbTable_Gradesetup();
    			$student_list=$formData['chk'];
    			foreach ( $student_list as $key=>$item) {
    				$student = $studentRegDB->getListStudent(array('IdStudentRegistration'=>$item));
    				$student_list[$key]=$student;
    				//echo var_dump($student);exit;
    			
    				if ($student) {
    					$prg=$dbHead->fnfetchProgramFaculty($student['IdProgram']);
    					$this->view->institution=$prg[0];
	    				$idstudentregistration=$student['IdStudentRegistration'];
	    				$major=$student['IdProgramMajoring'];
	    				$idlandscape=$student['IdLandscape'];
	    				$program=$student['IdProgram'];
	    				$idIntake=$student['IdIntake'];
	    				$student_list[$key]['branch']=$dbBranch->getData($student['IdBranch']);
	    				//============
	    				//get grading scheme
	    				//==================
	    				$lands=$dbLands->getData($idlandscape);
	    				$smt=$lands['IdStartSemester'];
	    				$lintIdGrade= $dbGrade->fnGetgradesetupmain($program, $smt);
	    				$larrGradeList = $dbGrade->fnViewGradeList($lintIdGrade['IdGradeSetUpMain'],"2");
	    				$this->view->larrresult = $larrGradeList;
	    				//=====================
	    				// get final assignment
	    				//======================
	    				$this->view->finals=$dbFinal->fnGetFinalAssigmentStd($idstudentregistration);
	    				
	    				$dtlhr = $student['appl_dob'];
	    				$student_list[$key]['dob']=  $dbCommon->fnCovertDateToLocalFormat($dtlhr);
	    				$student_list[$key]['dobeng']= $dtDobEng = $dbCommon->fnCovertDateToEnglishFormat($dtlhr);
	    				$intakestart = $student['class_start'];
	    				$student_list[$key]['intakedate']=  $dbCommon->fnCovertDateToLocalFormat($intakestart);
	    				$student_list[$key]['intakedateeng']= $dtDobEng = $dbCommon->fnCovertDateToEnglishFormat($intakestart);
	    				$stdname=$student['appl_fname'];
	    				if (isset($student['appl_mname'])) $stdname= $stdname.' '.$student['appl_mname'];
	    				if (isset($student['appl_lname'])) $stdname= $stdname.' '.$student['appl_lname'];
	    				$student_list[$key]['fullname']=$stdname;
	    				$idcol=$student['IdCollege'];
	    				$dean=$dbDean->getCollegeDean($idcol);
	    				$deanname=$dean['Fullname'];
	    				$FSalut = $dbCommon->fnGetSalutation($dean['FrontSalutation']);
	    				$BSalut = $dbCommon->fnGetSalutation($dean['BackSalutation']);
	    				if (isset($FSalut)) $deanname=$FSalut['BahasaIndonesia'].' '.$deanname;
	    				if (isset($BSalut)) $deanname=$deanname.', '.$BSalut['BahasaIndonesia'];
	    				$student_list[$key]['deanname']=$deanname;
	    				$student_list[$key]['deannik']=$dean['StaffId'].'/USAKTI';
	    				$rector=$dbUniv->fneditUniversity(1);
	    				$recname=$rector['Fullname'];
	    				$FSalut = $dbCommon->fnGetSalutation($rector['FrontSalutation']);
	    				$BSalut = $dbCommon->fnGetSalutation($rector['BackSalutation']);
	    				if (isset($FSalut)) $recname=$FSalut['BahasaIndonesia'].' '.$recname;
	    				if (isset($BSalut)) $recname=$recname.', '.$BSalut['BahasaIndonesia'];
	    				$student_list[$key]['rectorname']=$recname;
	    				$student_list[$key]['rectornik']=$rector['StaffId'].'/USAKTI';
	    				//===============================
	    				//get data graduation
	    				//===============================
	    				
	    				$data_arr = $Dbgraduate->getGraduatesNoWis(null, null,$idstudentregistration);
	    				if ($data_arr) {
		    				$id = $data_arr['id'];
		    				$dtYDS = $data_arr['date_of_Yudisium'];
		    				$dtSKR = $data_arr['date_of_skr'];
		    				
		    				$dtYDSLokal = $dbCommon->fnCovertDateToLocalFormat($dtYDS);
		    				$dtSKRLokal = $dbCommon->fnCovertDateToLocalFormat($dtSKR);
		    				$student_list[$key]['YudisiumDate']=$dtYDSLokal;
		    				$student_list[$key]['SKRdate']=$dtSKRLokal;
		    				$dtYDSEng = $dbCommon->fnCovertDateToEnglishFormat($dtYDS);
		    				$dtSKREng = $dbCommon->fnCovertDateToEnglishFormat($dtSKR);
		    				$student_list[$key]['YudisiumDateeng']=$dtYDSEng;
		    				$student_list[$key]['Gelar']= $data_arr['gelarP'];
		    				$student_list[$key]['NIRLT']= $data_arr['NIRL'];
		    				$student_list[$key]['noskpi']= $data_arr['noskpi'];
	    				
	    				} else {
	    					$student_list[$key]['YudisiumDate']=null;
	    					$student_list[$key]['SKRdate']=null;
	    					$student_list[$key]['YudisiumDateeng']=null;
	    					$student_list[$key]['Gelar']=null;
	    					$student_list[$key]['NIRLT']=null;
	    					$student_list[$key]['noskpi']=null;
	    					
	    				}
	    					 
	    				//================
	    				//get CP
	    				//================
	    				$items=$dbcp->getDatabyStudent($program, $idlandscape,$major);
	    				$unset='0';
	    				if ($items) {
	    					$unset='1';
	    					$student_list[$key]['cp']=$items;
	    				} else $student_list[$key]['cp']=array();
	    				// get honor
	    				$items=$dbhonor->getDatabyStudent($idstudentregistration,$status);
	    				if ($items) {
	    						$unset='1';
	    						$student_list[$key]['Honors']=$items;
	    				} else $student_list[$key]['Honors']=array();
	    			
	    				//get org
	    				$items=$dborg->getDatabyStudent($idstudentregistration,$status);
	    				if ($items) {
	    						$unset='1';
	    						$student_list[$key]['Organisasi']=$items;
	    				} else $student_list[$key]['Organisasi']=array();
	    					 
	    			
	    				//get Language
	    				$items=$dblang->getDatabyStudent($idstudentregistration,$status);
	    				if ($items) {
	    					$unset='1';
	    					$student_list[$key]['Language']=$items;
	    				} else $student_list[$key]['Language']=array();
	    				
	    			
	    				//get Softskill
	    				$items=$dbSoft->getDatabyStudent($idstudentregistration,$status);
	    				if ($items) {
	    						$unset='1';
	    						$student_list[$key]['Softskill']=$items;
	    				} else $student_list[$key]['Softskill']=array();
	    					 
	    				//get Internship
	    				$items=$dbInternship->getDatabyStudent($idstudentregistration,$status);
	    				if ($items) {
	    						$unset='1';
	    						$student_list[$key]['Internship']=$items;
	    				} else $student_list[$key]['Internship']=array();
	    					 
	    				//if ($unset=='0') unset($student_list[$key]);
	    				//echo var_dump($student_list);exit;
    				}
    			}
    			//echo var_dump($student_list);exit;
    			$this->view->studentlist=$student_list;
    			
    		}
    	}
    	
    	 
}