<?php

class Assistant_MarkEntryController extends Zend_Controller_Action { //Controller for the User Module

	public function indexAction() {
		
		//title
		$this->view->title= $this->view->translate("Mark Entry - Group List");
		$auth = Zend_Auth::getInstance();
		$this->view->idstaff = $auth->getIdentity()->IdStaff;
	
		//semester
		$semesterDB = new GeneralSetup_Model_DbTable_Semestermaster();
		$semesterList = $semesterDB->fnGetSemestermasterList();
		$this->view->semester_list = $semesterList;
		
		$programDb = new Registration_Model_DbTable_Program();
		$this->view->program_list = $programDb->getData();
		
		$branchDb=new GeneralSetup_Model_DbTable_Branchofficevenue();
		$this->view->branch_list= $branchDb->fnGetAllBranchList();
	}
	
    public function viewComponentAction(){
    	
    	$auth = Zend_Auth::getInstance();
    	$this->view->role = $auth->getIdentity()->IdRole;
    	
    	$this->view->title=$this->view->translate("Mark Entry - Component List");
    	
    	if($this->_getParam('msg')==1){
    		$this->view->noticeSuccess = $this->view->translate("Data has been saved");
    	}
    	
    	$idSemester = $this->_getParam('idSemester');
    	$idProgram = $this->_getParam('idProgram');
    	$idBranch = $this->_getParam('idBranch');
    	$idSubject = $this->_getParam('idSubject');
    	$idGroup = $this->_getParam('id');
    	
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram = $idProgram;
    	$this->view->idBranch = $idBranch;
    	$this->view->idSubject = $idSubject;
    	$this->view->idGroup = $idGroup;
		    	
    	//get info semester
    	$semesterDB = new GeneralSetup_Model_DbTable_Semestermaster();
    	$this->view->semester = $semesterDB->fngetSemestermainDetails($idSemester);
    	
    	//get info program
    	$programDB = new GeneralSetup_Model_DbTable_Program();
    	$this->view->program = $programDB->fngetProgramData($idProgram);
    	
    	//get branch
    	if ($idBranch!='') {
    		$branchDb=new GeneralSetup_Model_DbTable_Branchofficevenue();
    		$this->view->branch= $branchDb->getData($idBranch);
    	} else $this->view->branch=array('BranchName'=>'All');
    	 
    	//get info subject
    	$subjectDB = new GeneralSetup_Model_DbTable_Subjectmaster();
    	$this->view->subject = $subjectDB->getData($idSubject);
    	
    	//get course group
		$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);			
		$this->view->group = $group;
    	    	
    	//get component
    	$markDistributionDB =  new Examination_Model_DbTable_Marksdistributionmaster();
		$list_component = $markDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch);	    
    	$this->view->rs_component = $list_component;
    	//print_r($list_component);
    	
    }
    
    public function viewComponentScriptAction(){
    	 
    	$auth = Zend_Auth::getInstance();
    	$this->view->role = $auth->getIdentity()->IdRole;
    	 
    	$this->view->title=$this->view->translate("Exam Script Proposal - Component List");
    	 
    	if($this->_getParam('msg')==1){
    		$this->view->noticeSuccess = $this->view->translate("Data has been saved");
    	}
    	 
    	$idSemester = $this->_getParam('idSemester');
    	$idProgram = $this->_getParam('idProgram');
    	$idBranch = $this->_getParam('idBranch');
    	$idSubject = $this->_getParam('idSubject');
    	$idGroup = $this->_getParam('id');
    	 
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram = $idProgram;
    	$this->view->idBranch = $idBranch;
    	$this->view->idSubject = $idSubject;
    	$this->view->idGroup = $idGroup;
    	 
    	//get info semester
    	$semesterDB = new GeneralSetup_Model_DbTable_Semestermaster();
    	$this->view->semester = $semesterDB->fngetSemestermainDetails($idSemester);
    	 
    	//get info program
    	$programDB = new GeneralSetup_Model_DbTable_Program();
    	$this->view->program = $programDB->fngetProgramData($idProgram);
    	 
    	//get branch
    	if ($idBranch!='') {
    		$branchDb=new GeneralSetup_Model_DbTable_Branchofficevenue();
    		$this->view->branch= $branchDb->getData($idBranch);
    	} else $this->view->branch=array('BranchName'=>'All');
    
    	//get info subject
    	$subjectDB = new GeneralSetup_Model_DbTable_Subjectmaster();
    	$this->view->subject = $subjectDB->getData($idSubject);
    	 
    	//get course group
    	$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
    	$group = $courseGroupDb->getInfo($idGroup);
    	$this->view->group = $group;
    	//get Script
    	$dbScript=new Examination_Model_DbTable_ExamScriptMain();
    	
    	//get component
    	$markDistributionDB =  new Examination_Model_DbTable_Marksdistributionmaster();
    	$list_component = $markDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch);
    	foreach ($list_component as $key=>$value) {
    		$script=$dbScript->getExamScriptList($idSemester,$idProgram,$idSubject,$value['IdComponentType'],$idBranch,'');
    		if ($script) {
    			//echo var_dump($script); 
    			$list_component[$key]['IdScript']=$script[0]['IdScript'];
    			$list_component[$key]['IdDistributionMaster']=$script[0]['IdDistributionMaster'];
    			$list_component[$key]['url']=$script[0]['url'];
    		}
    		else {
    			$list_component[$key]['IdScript']='';
    			$list_component[$key]['IdDistributionMaster']='';
    			$list_component[$key]['url']='';
    		}
    		
    	}
    	$this->view->rs_component = $list_component;
    	//echo var_dump($list_component);exit;
    	
       
    }
    
	public function studentListAction(){
    	
    	$this->view->title=$this->view->translate("Mark Entry");
    
    	$auth = Zend_Auth::getInstance();
    	$this->view->role = $auth->getIdentity()->IdRole;
    	
    	$IdMarksDistributionMaster = $this->_getParam('id');    	
    	$gid = $this->_getParam('gid');
    	$idSemester = $this->_getParam('idSemester');
    	$idProgram = $this->_getParam('idProgram');
    	$idBranch = $this->_getParam('idBranch');
    	$idSubject = $this->_getParam('idSubject');
    	
    	$this->view->IdMarksDistributionMaster = $IdMarksDistributionMaster;    	
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram = $idProgram;
    	$this->view->idBranch = $idBranch;
    	$this->view->idSubject = $idSubject;
		
    	//get info semester
    	$semesterDB = new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester = $semesterDB->fngetSemestermainDetails($idSemester);
    	
    	//get info program
    	$programDB = new GeneralSetup_Model_DbTable_Program();
    	$this->view->program = $programDB->fngetProgramData($idProgram);
    	
    	//get branch
    	if ($idBranch!='') {
    		$branchDb=new GeneralSetup_Model_DbTable_Branchofficevenue();
    		$this->view->branch= $branchDb->getData($idBranch);
    	} else $this->view->branch=array('BranchName'=>'All');
    	
    	//get info subject
    	$subjectDB = new GeneralSetup_Model_DbTable_Subjectmaster();
    	$this->view->subject = $subjectDB->getData($idSubject);
    	
    	//get info group
    	$courseGroupDb = new GeneralSetup_Model_DbTable_CourseGroup();
		$this->view->group = $courseGroupDb->getInfo($gid);
		
		$publishDb = new Examination_Model_DbTable_PublishMark();
		$this->view->publish = $publishDb->getData($idProgram,$idSemester,$idSubject,$gid,$IdMarksDistributionMaster,1);
    	    	
    	//get main component info
    	$markDistributionDB = new Examination_Model_DbTable_Marksdistributionmaster();
    	$maincomponent = $markDistributionDB->getInfoComponent($IdMarksDistributionMaster);
    	$this->view->main_component = $maincomponent;
    	
    	//get list component item    	
	  	$oCompitem = new Examination_Model_DbTable_Marksdistributiondetails();	
	  	$component_item = $oCompitem->getListComponentItem($IdMarksDistributionMaster);
	  	$this->view->component_item = $component_item;
	  	$this->view->total_item = count($component_item);
	  	
	  	$staffDb = new GeneralSetup_Model_DbTable_Staffmaster();
	  	$this->view->staff = $staffDb->getAcademicStaff();   
    	
    	//get list student yg register  semester,program,subject di atas
    	//keluarkan aje dulu list student filter by group or lecturer later
    	
        $form = new Examination_Form_MarkEntrySearchStudent(array('idSemesterx'=>$idSemester,'idSubjectx'=>$idSubject));
     	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$studentDB = new Examination_Model_DbTable_StudentRegistration();
	    	// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$formData);
	    	$students = $studentDB->getStudentAttendExambyCourseGroup($idProgram,$gid,$idSubject,$idSemester,$IdMarksDistributionMaster,$formData,$idBranch);
	    	
    	}else{ 
    	
	    	$studentDB = new Examination_Model_DbTable_StudentRegistration();
	    	// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster);
	    	$students = $studentDB->getStudentAttendExambyCourseGroup($idProgram,$gid,$idSubject,$idSemester,$IdMarksDistributionMaster,null,$idBranch);	    		
	    	
    	} 
     	
     	
    	//check if component is attendance component.
    	//echo $maincomponent['IdComponentType'];
    	//exit;
    	if ($maincomponent['IdComponentType']==50) {
    		// get attendance mark from attendance percentage
    		$percentage=$maincomponent['Percentage'];
    		$marks=$maincomponent['Marks'];
    		$studenmark=new Examination_Model_DbTable_StudentMarkEntry();
    		foreach ($students as $student) {
    			$idregistration=$student['IdStudentRegistration'];
    			$IdStudentRegSubjects=$student['IdStudentRegSubjects'];
    			
    			$studenmark->insertMarkOfAttendance($idSemester, $idSubject, $gid, $idregistration,$IdStudentRegSubjects, $IdMarksDistributionMaster, $percentage,$marks);
    		}
    		//echo 'Hello';exit;
    	}
     	    	
		$this->view->students = $students;
		
    	
    }
    
     
    
    public function studentListAllAssistantAction(){
    
    	$this->view->title=$this->view->translate("Mark Entry");
    
    	$auth = Zend_Auth::getInstance();
    	 
    	$identrier=$auth->getIdentity()->registration_id;
    	 
    	$gid = $this->_getParam('id');
    	$idSemester = $this->_getParam('idSemester');
    	$idProgram = $this->_getParam('idProgram');
    	$idBranch = $this->_getParam('idBranch','');
    	$idSubject = $this->_getParam('idSubject');
    	$idStaff = $this->_getParam('idstaff');
    	//$this->view->IdMarksDistributionMaster = $IdMarksDistributionMaster;
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram = $idProgram;
    	$this->view->idBranch = $idBranch;
    	$this->view->idSubject = $idSubject;
    	$this->view->idstaff = $idStaff;
    	$this->view->idGroup = $gid;
    
    	//get info semester
    	$semesterDB = new  App_Model_General_DbTable_Semestermaster();
    	$this->view->semester = $semesterDB->fngetSemestermainDetails($idSemester);
    
    	//get info program
    	$programDB = new App_Model_General_DbTable_Program();
    	$this->view->program = $programDB->fngetProgramData($idProgram);
 
    	 
    	//get info subject
    	$subjectDB = new App_Model_General_DbTable_Subjectmaster();
    	$this->view->subject = $subjectDB->getData($idSubject);
    
    	//get info group
    	$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
    	$group=$courseGroupDb->getInfo($gid);
    	$this->view->group = $group;
    	//$idBranch=$group['BranchCreator'];
    	$idProgram=$group['ProgramCreator'];
    	
    	 
    	 
    	//get branch base on course group
    	$branchcourse=$courseGroupDb->getCourseBranch($gid);
    	$this->view->branchcourse=$branchcourse;
    
    	//get component
    	$markDistributionDB =  new App_Model_Exam_DbTable_Marksdistributionmaster();
    	$list_component = $markDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch);
    	$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
    	//echo var_dump($list_component);exit;
    	//get list component item
    	$oCompitem = new App_Model_Exam_DbTable_Marksdistributiondetails();
    	$operatorDb=new App_Model_Exam_DbTable_MarkOperator();
    	$dbheader=new Assistant_Model_DbTable_MarkdistributionDetailHeader();
    	
    	//---
    	$staffDb = new GeneralSetup_Model_DbTable_Staffmaster();
    	$this->view->staff = $staffDb->getAcademicStaff();
     
    	$form = new Assistant_Form_MarkEntrySearchStudent(array('idSemesterx'=>$idSemester,'idSubjectx'=>$idSubject));
    	$this->view->form = $form;
    
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		 
    		$studentDB = new App_Model_Exam_DbTable_StudentRegistration();
    		// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$formData);
    		$students = $studentDB->getStudentAttendExambyCourseGroupAllComponent($idProgram,$gid,$idSubject,$idSemester,$list_component,$formData,$idBranch);
    
    	}else{
    		 
    		$studentDB = new App_Model_Exam_DbTable_StudentRegistration();
    		// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster);
    		$students = $studentDB->getStudentAttendExambyCourseGroupAllComponent($idProgram,$gid,$idSubject,$idSemester,$list_component,null,$idBranch);
    
    	}
    	 
    	$this->view->students = $students;
    	$lstd=array();
    	foreach ($students as $value) {
    		 $lstd[]=$value['IdStudentRegistration'];
    	}
    	if ($lstd!=array()) $lstd=implode(",", $lstd); else $lstd=null;
    	foreach ($list_component as $key=>$value) {
    		$idmark=$value['IdMarksDistributionMaster'];
    		$component_item = $oCompitem->getListComponentItem($value['IdMarksDistributionMaster']);
    		if ($component_item) {
    			foreach ($component_item as $index=>$item) {
    				$idmarkdetail=$item['IdMarksDistributionDetails'];
    				$operator=$operatorDb->getDataByGroupComp($gid,$idmark,$idmarkdetail);
    				if ($operator) {
    					$component_item[$index]['EntrierFullName']=$this->getName($operator['FullName'], $operator['BackSalutation'], $operator['FrontSalutation']);
    					$component_item[$index]['VerFullName']=$this->getName($operator['VerFullName'], $operator['VerBack'], $operator['VerFront']);
    					if ($operator['Entrier']!='')
    						$component_item[$index]['entrier']=$operator['Entrier'];
    					else $component_item[$index]['entrier']=$group['IdLecturer'];
    					if ($operator['Verifier']!='')
    						$component_item[$index]['verifier']=$operator['Verifier'];
    					else $component_item[$index]['verifier']=$group['VerifyBy'];
    				} else {
    					$component_item[$index]['EntrierFullName']=$group["FullName"];
    					$component_item[$index]['VerFullName']='';
    					$component_item[$index]['entrier']=$group['IdLecturer'];
    					$component_item[$index]['verifier']=$group['VerifyBy'];
    				}
    			}
    			$list_component[$key]['item']=$component_item;
    			$list_component[$key]['nitem']=count($component_item);
    		} else {
    			$operator=$operatorDb->getDataByGroupComp($gid,$idmark,null);
    			if ($operator) {
    				$list_component[$key]['EntrierFullName']=$this->getName($operator['FullName'], $operator['BackSalutation'], $operator['FrontSalutation']);
    				$list_component[$key]['VerFullName']=$this->getName($operator['VerFullName'], $operator['VerBack'], $operator['VerFront']);
    				if ($operator['Entrier']!='')
    					$list_component[$key]['entrier']=$operator['Entrier'];
    				else $list_component[$key]['entrier']=$group['IdLecturer'];
    				if ($operator['Verifier']!='')
    					$list_component[$key]['verifier']=$operator['Verifier'];
    				else $list_component[$key]['verifier']=$group['VerifyBy'];} else {
    					$list_component[$key]['EntrierFullName']=$group["FullName"];
    					$list_component[$key]['VerFullName']='';
    					$list_component[$key]['entrier']=$group['IdLecturer'];
    					$list_component[$key]['verifier']=$group['VerifyBy'];
    				}
    				$list_component[$key]['item']=array();
    				$list_component[$key]['nitem']=0;
    		}
    	
    		//get detail entry if any
    		// group detail if any
    		$header=$dbheader->fnGetMarksDistributionDetailHeaderByMaster($idmark,null);
    		if ($header) {
    			$groupdetail=$courseGroupDb->getGroupListPerEntrierClass($idSubject,$idSemester,$lstd,$identrier,$header['Entrier_mode']);
    			$list_component[$key]['detail'][$idmark]=$groupdetail;
    		} else $list_component[$key]['detail'][$idmark]=array();
    	}
    	
    	$this->view->rs_component = $list_component;
    
    }
    
    public function studentListDetailAllAction(){
    
    	$this->view->title=$this->view->translate("Mark Entry: Detail of");
    	$session = new Zend_Session_Namespace('sis');
    	$auth = Zend_Auth::getInstance();
    	 
    	$idstaff = $auth->getIdentity()->registration_id;
    
    	 
    	$gid = $this->_getParam('gid');
    	$idSemester = $this->_getParam('idSemester');
    	$idProgram = $this->_getParam('idProgram');
    	$idBranch = $this->_getParam('idBranch');
    	$idSubject = $this->_getParam('idSubject');
    	$idStaff = $this->_getParam('idstaff');
    	 
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram = $idProgram;
    	$this->view->idBranch = $idBranch;
    	$this->view->idSubject = $idSubject;
    	$this->view->idstaff = $idstaff;
    	$this->view->idGroup = $gid;
    	
    	$idMarkDistMaster = $this->_getParam('idMaster');
    	$idGroupDetail = $this->_getParam('detailid');
    	$this->view->idMarkDistMaster = $idMarkDistMaster;
    	 
    	$dbDisDetail=new App_Model_Exam_DbTable_MarkdistributionDetail();
    	//cek for component
    	 
    	//get info distribution
    	$dbdist = new App_Model_Exam_DbTable_Marksdistributionmaster();
    	$this->view->markdist=$dbdist->fnGetMarksDistributionMasterById($idMarkDistMaster);
    	//
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$dbstaff=new App_Model_General_DbTable_Staffmaster();
    	$dbHeader=new App_Model_Exam_DbTable_MarkdistributionDetailHeader();
    	 
    	$header=$dbHeader->fnGetMarksDistributionDetailHeaderByMaster($idMarkDistMaster);
    	if ($header) {
    		if ($header['Entrier_mode']=="0") {
    			$entry=$dbStd->SearchStudentRegistration(array('IdStudentRegistration'=>$header['Entry_by']));
    			$entry=$entry['registrationId'].' '.$entry['student_name'];
    		}
    		else $entry=$dbstaff->getStaffFullName($header['Entry_by']);
    		$header['EntryFullName']=$entry;
    		$header['VerifyName']=$dbstaff->getStaffFullName($header['Verified_by']);
    		$this->view->header=$header;
    		//get component detail
    		$calmode=$dbDisDetail->fnGetCalculationType();
    		//echo var_dump($header);exit;
    		$this->view->calmode=$calmode;
    		$this->view->IdMarkDetailHeader=$header['IdMarkDistributionDetailHeader'];
    	}
    	 
    	//get info group
    	$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
    	$group=$courseGroupDb->getInfo($gid);
    	$this->view->group = $group;
    	
    	//get info group detail
    	$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
    	$groupdetail=$courseGroupDb->getInfo($idGroupDetail);
    	$this->view->groupdetail = $groupdetail;
    	 
     
    	//get component
    	$markDistributionDB =  new App_Model_Exam_DbTable_MarkdistributionDetail();
    	$list_component = $markDistributionDB->fnGetMarksDistributionDetailAll($idMarkDistMaster);
    	 
    	foreach ($list_component as $key=>$value) {
    		$idmark=$value['IdMarksDistributionMaster'];
    	   	$list_component[$key]['EntrierFullName']=$groupdetail["FullName"];
    		$list_component[$key]['VerFullName']='';
    		$list_component[$key]['entrier']=$groupdetail['IdLecturer'];
    		$list_component[$key]['verifier']=$groupdetail['VerifyBy'];
    				 
    		$list_component[$key]['item']=array();
    		$list_component[$key]['nitem']=0;
    		 
    
    	}
    	 
    	$this->view->rs_component=$list_component;
    	
    	$staffDb = new App_Model_General_DbTable_Staffmaster();
    	$this->view->staff = $staffDb->getAcademicStaff();
     
    	$form = new Assistant_Form_MarkEntrySearchStudent(array('idSemesterx'=>$idSemester,'idSubjectx'=>$idSubject));
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		 
    		$studentDB = new Assistant_Model_DbTable_StudentRegistration();
    		// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$formData);
    		$students = $studentDB->getStudentAttendExambyCourseGroupAllComponent($idProgram,$idGroupDetail,$idSubject,$idSemester,$list_component,$formData,$idBranch);
    
    	}else{
    		 
    		$studentDB = new Assistant_Model_DbTable_StudentRegistration();
    		// $students = $studentDB->getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster);
    		$students = $studentDB->getStudentAttendExambyCourseGroupAllComponent($idProgram,$idGroupDetail,$idSubject,$idSemester,$list_component,null,$idBranch);
    
    	}
     
    	$this->view->students = $students;
    	//echo var_dump($students);exit;
    
    
    }
    
     
    
    public function saveMarkAction(){
    	
    	
    	$auth = Zend_Auth::getInstance();
    	$cms_calculation = new Cms_ExamCalculation();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			//get main component info
    		$markDistributionDB = new Examination_Model_DbTable_Marksdistributionmaster();
    		$main_component = $markDistributionDB->getInfoComponent($formData["IdMarksDistributionMaster"]);
    
    	
			//get list component item    	
	  	    $oCompitem = new Examination_Model_DbTable_Marksdistributiondetails();	
	  	    $component_item = $oCompitem->getListComponentItem($formData["IdMarksDistributionMaster"]);
	  	   
  	    
			//echo '<pre>';
    		//print_r($formData);
    		
    		
    		for($i=0; $i<count($formData['IdStudentRegistration']); $i++){

    			$IdMarksDistributionMaster = $formData["IdMarksDistributionMaster"];
	    		$IdStudentRegistration = $formData["IdStudentRegistration"][$i];
	    		$IdStudentMarksEntry = $formData["IdStudentMarksEntry"][$IdStudentRegistration];
    		
    			
    			
	    				    			
	    			//1st: Save Main Mark
	    			
	    			$data["IdStudentRegistration"] = $IdStudentRegistration;
	    			$data["IdStudentRegSubjects"] = $formData['IdStudentRegSubjects'][$IdStudentRegistration];  
		    		$data["IdSemester"] = $formData["idSemester"];
			    	$data["Course"] = $formData["idSubject"];   
			    	$data["IdMarksDistributionMaster"] = $IdMarksDistributionMaster;
			    	$data["MarksTotal"] = $formData["MarksTotal"]; 
			    	$data["Component"]   =  $main_component["IdComponentType"];
			    	$data["ComponentItem"]   =  ''; 				
			    	$data["Instructor"] = '';    		
			    	$data["AttendanceStatus"] = '';
			    	$data["MarksEntryStatus"] = $formData["MarksEntryStatus"]; // id Type= 93    		
			    	$data["UpdUser"] = $auth->getIdentity()->iduser;
			    	$data["UpdDate"] = date('Y-m-d H:i:s');			    	
			    	$data["Remarks"] = $formData["Remarks"];

			    	//jika status approve add info dibawah
			    	if(isset($formData["ApprovedBy"]) && $formData["ApprovedBy"]!=''){
			    		$data["ApprovedBy"] = $formData["ApprovedBy"];  
			    		$data["ApprovedOn"] = date('Y-m-d H:i:s');  
			    	}
		    				    	
    			    			
	    			if(count($component_item)==0){ //bujang xde anak
	    				 
	    				$markObtained = $formData['mark'][$IdStudentRegistration];
	    				$data["TotalMarkObtained"] = $markObtained;
	    				$data["FinalTotalMarkObtained"]=$cms_calculation->calculateMark($markObtained,$main_component["Marks"],$main_component["Percentage"]);
	    				
	    				if(isset($markObtained) && $markObtained!=''){
			    				//insert di sini
					    		$markDB = new Examination_Model_DbTable_StudentMarkEntry();
					    		if(isset($IdStudentMarksEntry) && $IdStudentMarksEntry!=''){
					    			$markDB->updateData($data,$IdStudentMarksEntry);	
					    			//echo '<br>update<br>';    		  
					    		}else{	    			
					    			$IdStudentMarksEntry = $markDB->addData($data);
					    			//echo '<br>save<br>';
					    		}
	    				}//end isset mark jika x empty masukkan data
	    				
			    		
	    			}else{ 
	    				//jika ada anak
	    				$TotalMarkObtained = '';
	    				
	    				
	    				foreach($component_item as $item){
	    					
	    					$IdMarksDistributionDetails = $item["IdMarksDistributionDetails"];	
			    			$ItemMarkObtained = $formData['mark'][$IdStudentRegistration][$IdMarksDistributionDetails];
			    			
			    			//calculation total mark start here
			    			//check percentage for item ni
			    			$item_percentage = $item["Percentage"];
			    			$item_fullmark   = $item["Weightage"];
			    			
			    			if(isset($ItemMarkObtained) && $ItemMarkObtained!=''){
			    				if($TotalMarkObtained=='') {			    								    					
			    					$TotalMarkObtained = $cms_calculation->calculateMark($ItemMarkObtained,$item_fullmark,$item_percentage);
			    						    				
			    				}else{			    					
			    					$ItemMarkObtained = $cms_calculation->calculateMark($ItemMarkObtained,$item_fullmark,$item_percentage);
			    					$TotalMarkObtained = $TotalMarkObtained+$ItemMarkObtained;
			    				}
			    				
			    				//to get component final mark by main component percentage
			    				$FinalTotalMarkObtained= $cms_calculation->calculateFinalMark($TotalMarkObtained,$main_component["Percentage"]);
			    				
	    					}			    			
	    				}
	    				
	    					    				
	    					if($TotalMarkObtained!=''){
			    				
			    				$data["TotalMarkObtained"] = $TotalMarkObtained; 
			    				$data["FinalTotalMarkObtained"] = $FinalTotalMarkObtained; 
			    				
			    				//insert di sini
					    		$markDB = new Assistant_Model_DbTable_StudentMarkEntry();
					    		if(isset($IdStudentMarksEntry) && $IdStudentMarksEntry!=''){
					    			$markDB->updateData($data,$IdStudentMarksEntry);	
					    			//echo '<br>update<br>';    		  
					    		}else{	    			
					    			$IdStudentMarksEntry = $markDB->addData($data);
					    			//echo '<br>save<br>';
					    		}					    		
			    			}//en total mark obtained
	    			}	    				
		    		    			
	    			//end info main mark entry
	    			
	    				
		    		
		    		
		    		//2nd : Save Details Component Mark
					//jika mark ini utk component item punya mark so kene masukkan mark dalam detail mark entry juga
					
    				if(count($component_item)>0){
    					
    					$TotalMarkObtained = 0;
    				
			    		foreach($component_item as $item){
			    			
			    			$IdMarksDistributionDetails = $item["IdMarksDistributionDetails"];	
			    			$ItemMarkObtained = $formData['mark'][$IdStudentRegistration][$IdMarksDistributionDetails];
			    			$FinalMarksObtained = $cms_calculation->calculateMark($ItemMarkObtained,$item["Weightage"],$item["Percentage"]);
			    			
			    						    					    
			    			$dataItem["IdStudentMarksEntry"] = $IdStudentMarksEntry;   
				    		$dataItem["Component"] = $main_component["IdComponentType"];   
				    		$dataItem["ComponentItem"]   =  '';
				    		$dataItem["ComponentDetail"] = $IdMarksDistributionDetails;
				    		$dataItem["MarksObtained"] = $ItemMarkObtained;
				    		$dataItem["FinalMarksObtained"] = $FinalMarksObtained;
		    				$dataItem["TotalMarks"] = $item["Weightage"]; //jumlah penuh soalan
		    				$dataItem["UpdUser"] = $auth->getIdentity()->iduser;
				    		$dataItem["UpdDate"] = date('Y-m-d H:i:s');
				    			
			    			//check mark dah pernah entry ke sebelum ni?
			    		    $markDetailsDB = new Examination_Model_DbTable_StudentDetailsMarkEntry();
			    			$markEntryDetails = $markDetailsDB->checkMarkEntry($IdStudentMarksEntry,$IdMarksDistributionDetails);
			    		
							if(isset($markEntryDetails["IdStudentMarksEntryDetail"]) && $markEntryDetails["IdStudentMarksEntryDetail"]!=''){
			    				 $markDetailsDB->updateData($dataItem,$markEntryDetails["IdStudentMarksEntryDetail"]);
			    				 //echo '<br>update details<br>';
			    			}else{	    			
			    				 $markDetailsDB->addData($dataItem);
			    				 //echo '<br>save details<br>';
			    			}
			    			
			    				  
			    			
			    		}//end foreach item
		    		
    				}//end count component
    						  
		    		
    			}//end for
    			
    		}//end if post
    		
    		//echo '</pre>';
  
    		//redirect
			$this->_redirect($this->view->url(array('module'=>'examination','controller'=>'mark-entry', 'action'=>'view-component','idSemester'=>$formData["idSemester"],'idProgram'=>$formData["idProgram"],'idSubject'=>$formData["idSubject"],'id'=>$IdMarksDistributionMaster,'idDetail'=>$formData["idDetail"],'msg'=>1),'default',true));
		   
    	
    }
    
    
	 
	public function groupListAssistantAction(){
	
		$auth = Zend_Auth::getInstance();
		$this->view->title = "ASSISTANT: Mark Entry - Course Group List";
	
		$programDb = new Assistant_Model_DbTable_StudentMarkEntryDetail();
		
		$this->view->idstaff=$auth->getIdentity()->registration_id;
		$this->view->program = $programDb->getDataProgram($this->view->idstaff);
		//get info semester
		$semesterDB = new App_Model_General_DbTable_Semestermaster();
		$this->view->semester = $semesterDB->fnGetSemestermasterList();
		$dbBranch=new App_Model_General_DbTable_Branchofficevenue();
		$this->view->branchlist=$dbBranch->fnGetAllBranchList();
		if ($this->getRequest()->isPost()) {
				
			$formData = $this->getRequest()->getPost();
	
			//$idProgram = $formData["idProgram"];
			//$this->view->idProgram = $idProgram;
				
			$idSemester = $formData["idSemester"];
			$this->view->idSemester = $idSemester;
				
			//$idbranch = $formData["idBranch"];
			
			//if ($idbranch=='') $idBranch=0;  
			//$this->view->idBranch = $idbranch;
			
			$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
			$groups = $courseGroupDb->getCourseTaggingGroupList($idSemester);//,$idProgram,null,$idbranch);
			$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
			
			foreach($groups as $i=>$group){
				//$branch=$dbbranch->getData($group['BranchCreator']);
				//if ($branch) $groups[$i]['Branch']=$branch['BranchName'];//['BranchName'];
				//else 
				$groups[$i]['Branch']='No Branch';
				$courseGroupStudent = new Assistant_Model_DbTable_CourseGroupStudent();
				$total_student = $courseGroupStudent->getStudentbyGroup($group["IdCourseTaggingGroup"]);
				 
				$groups[$i]['total_student']=count($total_student);
			}
			$groupss['grp']=$groups;
			 
				
			$this->view->list_groups = $groupss;
				
			 
		}
	
		 
	   
	}
	
	function ajaxGetBranchAction() {


		$this->_helper->layout()->disableLayout();
		 
		$dbGen = new Assistant_Model_DbTable_CourseGroup();
		$auth = Zend_Auth::getInstance();
		
		$program_id = $this->_getParam('program_id', null);
		 
		$semester_id = $this->_getParam('semester_id', null); 
		
		$branch=$dbGen->getCourseBranchByProgram($semester_id,$program_id);
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('view', 'html');
		$ajaxContext->initContext();
			
		$ajaxContext->addActionContext('view', 'html')
		->addActionContext('form', 'html')
		->addActionContext('process', 'json')
		->initContext();
		
		$json = Zend_Json::encode($branch);
		
		echo $json;
		exit();
		
		//}
		
	}
	function ajaxSaveMarkAction(){
		
		$this->_helper->layout()->disableLayout();
		
		$cms_calculation = new Cms_ExamCalculation();
		$markDB = new Examination_Model_DbTable_StudentMarkEntry();
		$markDetailsDB = new Examination_Model_DbTable_StudentDetailsMarkEntry();
		$studentRegistarationDB = new Examination_Model_DbTable_StudentRegistration(); 
		$auth = Zend_Auth::getInstance();
		
		$program_id = $this->_getParam('idProgram', null);
		$idbranch= $this->_getParam('idBranch', null);
		$semester_id = $this->_getParam('idSemester', null);		
		$subject_id = $this->_getParam('idSubject', null);
		$idMaster = $this->_getParam('idMaster', null);
		$IdStudentRegistration = $this->_getParam('id', null);
		$IdStudentMarksEntry = $this->_getParam('idMark', null);
		$IdStudentRegSubjects = $this->_getParam('idRegSub', null);
		$markObtained = $this->_getParam('markObtained', null);
		$TotalMarkObtained = $this->_getParam('tmo', null);
		$IdStudentMarksEntryDetail = $this->_getParam('idMarkEntryDetail', null);
		$IdMarksDistributionDetails = $this->_getParam('idDetail', null);
		$idExamGroup = $this->_getParam('idExamGroup', null);
				
		$student = $studentRegistarationDB->getStudentLandscapeInfo($IdStudentRegistration);
		if ($idbranch==null || $idbranch=='') $idbranch=$student['IdBranch'];
		
		//if ($this->getRequest()->isPost()) {
		
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();
		
			
			//get main component info
    		$markDistributionDB = new Examination_Model_DbTable_Marksdistributionmaster();
    		$main_component = $markDistributionDB->getInfoComponent($idMaster);
    
    	
			//get list component item    	
	  	    $oCompitem = new Examination_Model_DbTable_Marksdistributiondetails();	
	  	    $component_item = $oCompitem->getListComponentItem($idMaster);
	  	     			    				    			
    			//1st: Save Main Mark
    			
    			$data["IdStudentRegistration"] = $IdStudentRegistration;
    			$data["IdStudentRegSubjects"] =$IdStudentRegSubjects;  
	    		$data["IdSemester"] = $semester_id;
		    	$data["Course"] = $subject_id;   
		    	$data["IdMarksDistributionMaster"] = $idMaster;
		    	$data["MarksTotal"] = $main_component["Marks"]; 
		    	$data["Component"]   =  $main_component["IdComponentType"];
		    	$data["ComponentItem"]   =  ''; 				
		    	$data["Instructor"] = '';    		
		    	$data["AttendanceStatus"] = '';
		    	$data["MarksEntryStatus"] = 407; // 407=>ENTRY ( id Type= 93 )  		
		    	$data["UpdUser"] = $auth->getIdentity()->iduser;
		    	$data["UpdDate"] = date('Y-m-d H:i:s');	
		    	$data["exam_group_id"] = $idExamGroup;		    	
		    	
			    	
    			    			
    			if(count($component_item)==0){ //bujang xde anak
    				 
	    				$markDB = new Examination_Model_DbTable_StudentMarkEntry();
	    				
	    				$data["TotalMarkObtained"] = $markObtained;
	    				$data["FinalTotalMarkObtained"]=$cms_calculation->calculateMark($markObtained,$main_component["Marks"],$main_component["Percentage"]);
	    				
	    				if(isset($markObtained) && $markObtained!=''){
					    		if($markDetailsDB->checkMarkEntry($IdStudentMarksEntry, $IdMarksDistributionDetails)   && $IdStudentMarksEntry!=''){
					    			$markDB->updateData($data,$IdStudentMarksEntry);				    		   		  
					    		}else{	    			
					    			$IdStudentMarksEntry = $markDB->addData($data);				    			
					    		}
	    				}else{    				
	    						//delete sebab null ibarat no mark
						 		$markDB->deleteData($IdStudentMarksEntry);
						 		$IdStudentMarksEntry='x';
	    				}
    				
		    		
    			}else{ 
    			    	    				
    				if($TotalMarkObtained!=''){
		    					    				
	    				$FinalTotalMarkObtained= $cms_calculation->calculateFinalMark($TotalMarkObtained,$main_component["Percentage"]);
	    				
	    				$data["TotalMarkObtained"] = $TotalMarkObtained; 
	    				$data["FinalTotalMarkObtained"] = $FinalTotalMarkObtained; 
	    							    	
			    		if(isset($IdStudentMarksEntry) && $IdStudentMarksEntry!=''){
			    			//echo 'update';	
			    			$markDB->updateData($data,$IdStudentMarksEntry);				    			   		  
			    		}else{	    		
			    			//echo 'add';	
			    			$IdStudentMarksEntry = $markDB->addData($data);			    			
			    		}	
			    					    						    		
		    		}else{
		    			
		    			//delete sebab null ibarat no mark
		    			$markDB = new Examination_Model_DbTable_StudentMarkEntry();
					 	$markDB->deleteData($IdStudentMarksEntry);
					 	$IdStudentMarksEntry='x';
					 	
		    		}//end total mark obtained
		    		 
		    		//2nd : Save Details Component Mark    			
    				
		  	  		$oCompitem = new Examination_Model_DbTable_Marksdistributiondetails();	
		  	    	$item = $oCompitem->getDataComponentItem($IdMarksDistributionDetails);	  	     			
	    			
		  	    	$FinalMarksObtained = $cms_calculation->calculateMark($markObtained,$item["Weightage"],$item["Percentage"]);				    			
		  	    	
	    			$dataItem["IdStudentMarksEntry"] = $IdStudentMarksEntry;   
					$dataItem["Component"] = $main_component["IdComponentType"];   
					$dataItem["ComponentItem"]   =  '';
					$dataItem["ComponentDetail"] = $IdMarksDistributionDetails;
					$dataItem["MarksObtained"] = $markObtained;
					$dataItem["FinalMarksObtained"] = $FinalMarksObtained;
			    	$dataItem["TotalMarks"] = $item["Weightage"]; //jumlah penuh soalan bukan pemberat
			    	$dataItem["UpdUser"] = $auth->getIdentity()->iduser;
					$dataItem["UpdDate"] = date('Y-m-d H:i:s');
					   			
	    			
					if($markDetailsDB->checkMarkEntry($IdStudentMarksEntry, $IdMarksDistributionDetails) && $IdStudentMarksEntryDetail!=''){
						
						 if($markObtained==''){
						 	//delete sebab null ibarat no mark
						 	$markDetailsDB->deleteData($IdStudentMarksEntryDetail);
						 	$IdStudentMarksEntryDetail='x';
						 }else{
	    					 $markDetailsDB->updateData($dataItem,$IdStudentMarksEntryDetail);	    					
						 }
	    				 
	    			}else{	    			
	    				 $IdStudentMarksEntryDetail  = $markDetailsDB->addData($dataItem);
	    			}
			    		
    			}//end info mark entry    				
	    		    			
    		
	  	    
    			
    			//nak update markah keseluruhan campur semua mark distribution component yg ada
    			//cari mark distribution bagi semester,subject,program
    			//calculate total mark based on component if all component already there
    			
    		if ($markDB->isAllComponentHasMarkPerMhs($idMaster, $IdStudentRegSubjects)) {
    			$markDB->getStudentTotalMarkPerMhs($idMaster,$IdStudentRegistration,$IdStudentRegSubjects,true); 
    			$msg="component complete";
    		} else {
    			$msg="component not complete";
    			$markDB->getStudentTotalMarkPerMhs($idMaster,$IdStudentRegistration,$IdStudentRegSubjects,false);
    		}
    				
    			
				//update parent mark  for block landscape 
    			if($student["LandscapeType"]==44){//block    
	    			$info['IdLandscape']=$student["IdLandscape"];
	    			$info['IdSubject']=$subject_id;
	    			$info['IdStudentRegistration']=$IdStudentRegistration;
	    			$info["IdProgram"]=$student["IdProgram"];
	    			$info["IdSemester"]=$semester_id;
	    						
	    			$cms_calculation->getParentGrade($info);
    			}
    		//get total mark and soforth
    		$grade=$markDB->getGrade($IdStudentRegSubjects);
    		
    		if ($grade) {
    			$grades=array('IdStudentMarksEntry'=>$IdStudentMarksEntry,
    						'IdStudentMarksEntryDetail'=>$IdStudentMarksEntryDetail,
    						'final_course_mark'=>$grade['final_course_mark'],
    						'grade_name'=>$grade['grade_name'],
    						'exam_status'=>$grade['exam_status'],
    						'grade_status'=>$grade['grade_status'],
    						'msg'=>$msg
    			);
    		} else 
    			$grades=array('IdStudentMarksEntry'=>$IdStudentMarksEntry,
    						'IdStudentMarksEntryDetail'=>$IdStudentMarksEntryDetail,
    						'final_course_mark'=>0,
    						'grade_name'=>'',
    						'exam_status'=>'',
    						'grade_status'=>'',
    						'msg'=>$msg
    		);
			
			$ajaxContext->addActionContext('view', 'html')
						->addActionContext('form', 'html')
						->addActionContext('process', 'json')
						->initContext();
		
			$json = Zend_Json::encode($grades);
		
			echo $json;
			exit();
		
		//}
	}
	
	function ajaxSaveMarkDetailAction(){
	
		$this->_helper->layout()->disableLayout();
	
		$cms_calculation = new Cms_ExamCalculation();
		$markDB = new Assistant_Model_DbTable_StudentMarkEntryDetail();
		//$markDetailsDB = new App_Model_Exam_DbTable_StudentDetailsMarkEntry();
		$studentRegistarationDB = new App_Model_Exam_DbTable_StudentRegistration();
		$auth = Zend_Auth::getInstance();
		
		$idSemester = $this->_getParam('idSemester', null);
		$idSubject = $this->_getParam('idSubject', null);
		$idMaster = $this->_getParam('idMarkMaster', null);
		$idMasterDetail = $this->_getParam('idMasterDetail', null);
		$idStudentRegSubjects=$this->_getParam('idstdregsubject', null);
		$IdStudentRegistration = $this->_getParam('id', null);
		$IdStudentMarksDetail = $this->_getParam('idStdMarkDetail', null); 
		$IdStudentMarksEntry = $this->_getParam('idStdMarkEntry', null);
		$markObtained = $this->_getParam('markObtained', null);
		//$TotalMarkObtained = $this->_getParam('tmo', null);
		$idExamGroup = $this->_getParam('idExamGroup', null);
	 
		$student = $studentRegistarationDB->getStudentLandscapeInfo($IdStudentRegistration);
		 
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('view', 'html');
		$ajaxContext->initContext();
	
		//echo $idMasterDetail;exit;	
		//get main component info
		$markDistributionDB = new App_Model_Exam_DbTable_MarkdistributionDetail();
		$main_component = $markDistributionDB->fnGetMarksDistributionDetail($idMasterDetail);
	
		//get headwr
		$dbheader=new Assistant_Model_DbTable_MarkdistributionDetailHeader();
		$header=$dbheader->fnGetMarksDistributionDetailHeaderByMaster($idMaster);
		if ($header['CalMode']=='Rerata') $main_component["Percentage"]=100;
		//1st: Save Main Mark
	 	$markfinal=$cms_calculation->calculateMark($markObtained,$main_component["Marks"],$main_component["Percentage"]);
		$data["IdStudentRegistration"] = $IdStudentRegistration; 
		$data["IdSemester"] = $idSemester;
		$data["Course"] = $idSubject;
		$data["IdMarksDistributionMaster"]=$idMaster;
		$data["IdMarksDistributionDetail"] = $idMasterDetail;
		$data["MarksTotal"] = $main_component["Marks"];
		$data["Component"]   =  $main_component["IdComponentType"];
		$data["ComponentItem"]   =  '';
		$data["Instructor"] = '';
		$data["AttendanceStatus"] = '';
		$data["MarksEntryStatus"] = 407; // 407=>ENTRY ( id Type= 93 )
		$data["UpdUser"] = $auth->getIdentity()->appl_id;
		$data["UpdDate"] = date('Y-m-d H:i:s'); 
		$data["TotalMarkObtained"] = $markObtained;
		$data["FinalTotalMarkObtained"]=$markfinal;
		$data["IdStudentRegSubjects"]=$idStudentRegSubjects;
		$markDB = new Assistant_Model_DbTable_StudentMarkEntryDetail();
		//echo var_dump($data);exit;
		if(isset($markObtained) && $markObtained!=''){
			
				$row=$markDB->checkMarkEntry($IdStudentRegistration, $idMasterDetail, $idSemester,$idMaster);
				//echo var_dump($row);exit;
				if(!$row ){
					 
					$IdStudentMarksDetail = $markDB->addData($data);
				}else{
					//echo "update";exit;
					$markDB->updateData($data,$row['IdStudentMarkDetail']);
				}
		}else{
				 
				//delete sebab null ibarat no mark
				$markDB->deleteData($IdStudentMarksDetail);
				$IdStudentMarksEntry='x';
		}
	
	  
		$grades=$markDB->getStudentTotalMarkPerMhs($idMaster,$IdStudentRegistration,true);
		 	
		//update tbl_studentregsubjects_detail
		
		$dbStdRegDet=new Assistant_Model_DbTable_Studentregsubjects();
		$data=array('final_course_mark'=>$grades['markall']);
		$dbStdRegDet->updateData($data, $idStudentRegSubjects);
		
		$ajaxContext->addActionContext('view', 'html')
		->addActionContext('form', 'html')
		->addActionContext('process', 'json')
		->initContext();
	
		$json = Zend_Json::encode(array('markdetail'=>$markObtained,'markdetailfinal'=>$markfinal,'markall'=>$grades["markall"],'markallfinal'=>$grades["markallfinal"],'IdStudentMarksDetail'=>$IdStudentMarksDetail));
		
		echo $json;
		exit();
	
		//}
	}
	

public function searchCourseAction(){
		$this->_helper->layout()->disableLayout();
	
		$semester_id = $this->_getParam('semester_id', null);
		$program_id = $this->_getParam('program_id', null);
		$branch_id = $this->_getParam('branch_id', null);
			
		//if ($this->getRequest()->isPost()) {
	
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();
			
			$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
			$subjects=$courseGroupDb->getListofSubject($semester_id, $program_id,$branch_id);
			 
			
			 
			foreach($subjects as $key=>$subject){
					
				$subjects[$key]['SubjectName']=$subject['subject_name'];
				$subjects[$key]['SubCode']=$subject['subject_code'];
				 
				//get total student register this subject
				$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
				$total_student = $subjectRegDB->getTotalRegister($subject["IdSubject"],$semester_id);
				$subjects[$key]["total_student"] = $total_student;
					
				//get total group creates
				$total_group = $courseGroupDb->getTotalGroupByCourse($subject["IdSubject"],$semester_id);
				$subjects[$key]["total_group"] = $total_group;
				$subjects[$key]["IdSemester"] = $semester_id;				
					
				 
					
				 
			}
				
			foreach ($subjects as $index => $subject){
				if($subject["total_student"]>0 && $subject["total_group"]>0){
						
				}else{
					unset($subjects[$index]);
				}
			}
			/*
			 * End search subject
			*/
	
			$ajaxContext->addActionContext('view', 'html')
			->addActionContext('form', 'html')
			->addActionContext('process', 'json')
			->initContext();
	
			$json = Zend_Json::encode($subjects);
	
			echo $json;
			exit();
		//}
	}
	
	function searchCourseGroupOldAction(){
		
		$auth = Zend_Auth::getInstance();
		
		$iduser = $auth->getIdentity()->iduser;
		
		
		$this->_helper->layout()->disableLayout();
		
		$semester_id = $this->_getParam('semester_id', null);
		$program_id = $this->_getParam('program_id', null);
		$subject_id = $this->_getParam('subject_id', null);
		$branch_id = $this->_getParam('branch_id', null);
		$sis_session = new Zend_Session_Namespace('sis');
		
		if ($this->getRequest()->isPost()) {
		
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();
		
			/*
			 * Search Group
			*/		
			$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
			$groups = $courseGroupDb->getMarkEntryGroupList($subject_id,$semester_id);
			
			
			$i=0;
			foreach($groups as $group){
					
				$courseGroupStudent = new Assistant_Model_DbTable_CourseGroupStudent();
				//$total_student = $courseGroupStudent->getTotalStudent($group["IdCourseTaggingGroup"]);
				$total_student = $courseGroupStudent->getTotalStudentViaSubReg($group["IdCourseTaggingGroup"]);
					
				$group["total_student"] = $total_student;
				$groups[$i]=$group;
					
				$i++;
			}
			/*
			 * End search group
			*/
		
			$ajaxContext->addActionContext('view', 'html')
			->addActionContext('form', 'html')
			->addActionContext('process', 'json')
			->initContext();
		
			$json = Zend_Json::encode($groups);
		
			echo $json;
			exit();
		
		}
	}
	
	function searchCourseGroupAction(){
	
		$auth = Zend_Auth::getInstance();
	
		$iduser = $auth->getIdentity()->iduser;
	
	
		$this->_helper->layout()->disableLayout();
	
		$semester_id = $this->_getParam('semester_id', null);
		$program_id = $this->_getParam('program_id', null);
		$subject_id = $this->_getParam('subject_id', null);
		$branch_id = $this->_getParam('branch_id', null);
		$sis_session = new Zend_Session_Namespace('sis');
	
		//if ($this->getRequest()->isPost()) {
	
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();
	
			/*
			 * Search Group
			*/
			$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
			$groups = $courseGroupDb->getCourseTaggingGroupList($semester_id,$program_id,$subject_id,$branch_id);
			$dbBranch=new GeneralSetup_Model_DbTable_Branchofficevenue();
    		$dbStaffmaster=new GeneralSetup_Model_DbTable_Staffmaster();
				
			 
			foreach($groups as $key=>$group){
				if (isset($group['branch_id'])) {
					if ($group['branch_id']=='' || $group['branch_id']==null)  $groups[$key]['BranchName']='No Branch';
					else {
						$branch=$dbBranch->getData($group['branch_id']);
						$groups[$key]['BranchName']=$branch['BranchName'];
					}
				} else $groups[$key]['BranchName']='No Branch';
				$courseGroupStudent = new Assistant_Model_DbTable_CourseGroupStudent();
				//$total_student = $courseGroupStudent->getTotalStudent($group["IdCourseTaggingGroup"]);
				$total_student = $courseGroupStudent->getTotalStudentViaSubReg($group["IdCourseTaggingGroup"]);
					
				$groups[$key]["total_student"] = $total_student;
				//$groups[$key]=$group;
				 
			}
			/*
			 * End search group
			*/
	
			$ajaxContext->addActionContext('view', 'html')
			->addActionContext('form', 'html')
			->addActionContext('process', 'json')
			->initContext();
	
			$json = Zend_Json::encode($groups);
	
			echo $json;
			exit();
	
		//}
	}
	public function editMarkAction(){
		
		$auth = Zend_Auth::getInstance();
		$this->view->iduser = $auth->getIdentity()->iduser;
		$idmark=$this->getRequest()->getParam('idmark');
		//get mark
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			unset($formData['submit']);
			$db=new Examination_Model_DbTable_Remarkapplication();
			
			if (isset($formData['idRemarkingHistory']) && $formData['idRemarkingHistory']!='') {
				$idremark=$formData['idRemarkingHistory'];
				unset($formData['idRemarkingHistory']);
				$db->fnUpdateRemarking($formData,$idremark);
				//echo var_dump($formData);exit;
			}else 
				$idremark=$db->fnInsertRemarking($formData);
			//echo $idremark; exit;
			$this->view->remark=$db->fnGetRemarkingHistory($idremark);
			$idmark=$formData['IdStudentMarksEntry'];
		}
		$db=new Examination_Model_DbTable_StudentMarkEntry();
		$marks=$db->getStudentMark($idmark);
		$db = new Registration_Model_DbTable_Studentregistration();
		$StudentInfo = $db->SearchStudentRegistration(array('IdStudentRegistration'=>$marks['IdStudentRegistration']));
		$marks['StudentName'] = $StudentInfo[0]['student_name'];
		$this->view->student=$marks;
		$this->view->id=$idmark;
		$this->view->title='Edit Mark';
	}
	 
    
    public function subjectListAction() {
        
        $auth = Zend_Auth :: getInstance();
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$menuArray = $dbAdapter->fetchAll("SELECT * FROM tbl_nav_menu WHERE role_id = ".$auth->getIdentity()->IdRole." ORDER BY seq_order ");
		
		$this->view->moduleList = $menuArray;
        
        //GET CURRENT SEMESTER
        $semesterDB = new GeneralSetup_Model_DbTable_Semestermaster();
    	$this->view->semester = $semesterDB->getCurrentSemester();    	
     
        //GET GROUP INVOLVE
        $courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
        $courseGroups = $courseGroupDb->getMarkEntryGroupListBySemester($this->view->semester['IdSemesterMaster']);
        
        $this->view->courseGroups = $courseGroups;
       
        //die;
        //GET SUBJECT
    }
    public function copyMarkAction() {
    	
    	$this->view->title=$this->view->translate("Copy Mark to other Semesster");
    	 
    	$form = new Examination_Form_CopyMark();
    	$this->view->form = $form;
    	 
    	if ($this->getRequest()->isPost()) {
    			
    		$formData = $this->getRequest()->getPost();
    			
    		$idprogram= $formData["IdProgram"];
    		$idsemestersource = $formData["IdSemesterSource"];
    		$idsemesterdest = $formData["IdSemesterDest"];
    		$this->view->idprogram=$idprogram;
    		$this->view->idsemestersource=$idsemestersource;
    		$this->view->idsemesterdest=$idsemesterdest;
    		$dbcouse=new Examination_Model_DbTable_StudentRegistrationSubject();
    		$this->view->courses=$dbcouse->getAllCourseBySemester($idprogram, $idsemesterdest);
    		//$this->copyMarkToOtherBlankSemester($idsemestersource, $idsemesterdest, $idsubject, $idprogram);
    	}
    	
    }
    public function copyMarkExecuteAction() {
    	
    	//get component from source
    	
    	if ($this->getRequest()->isPost()) {
    		 
	    	$formData = $this->getRequest()->getPost();
	    	$idsemestersource=$formData['idSemesterSource'];
	    	$idsemesterdest=$formData['idSemesterDest'];
	    	$idprogram=$formData['idProgram'];
	    	$dbregister = new Examination_Model_DbTable_StudentRegistration();
	    	$dbMark = new Examination_Model_DbTable_StudentMarkEntry();
	    	$dbsource = new Examination_Model_DbTable_Marksdistributionmaster();
	    	//echo var_dump($formData['copy']);exit;
	    	foreach ($formData['copy'] as $copy) {
	    		$idsubject=$copy;
		    	$components = $dbsource->getMarkDistComponent($idsemestersource, $idprogram, $idsubject);
		    	//echo var_dump($components);exit;
		    	foreach ($components as $component) {
		    		//echo var_dump($components);exit;
		    		$idcomponent=$component['IdComponentType'];
		    		$idmarkSource=$component['IdMarksDistributionMaster'];
		    		//get idmarkdist in destination for associate component 
		    		$descomps=$dbsource->getMarkId($idsemesterdest, $idprogram, $idsubject, $idcomponent);
		    		if (!$descomps) {
		    			//insert into markdistribution destination and get the id
		    			
		    			unset($component['IdMarksDistributionMaster']);
		    			$component['semester']=$idsemesterdest;
		    			$idMarkDest=$dbsource->fnAddMarksDisributionMaster($component);
		    			//echo $idMarkDest;exit;
		    		} else $idMarkDest=$descomps['IdMarksDistributionMaster'];
		    		//copy mark to destination
		    		//1.get student from destination
		    		//$component['semester']=$idsemesterdest;
		    		$students=$dbregister->getStudentRegisterSubject($idsemesterdest, $idsubject);
		    		foreach ($students as $student) {
		    			$idstundet=$student['IdStudentRegistration'];
		    			$idregsubject=$student['IdStudentRegSubjects'];
		    			//get mark from source
		    			//echo var_dump($students);exit;
		    			$mark=$dbMark->getStudentMarkByMarkDist($idprogram, $idsemestersource, $idsubject, $idstundet, $idregsubject,$idmarkSource);
		    			//insert into destination id not there
		    			if ($mark) {
			    			$mark['IdMarksDistributionMaster']=$idMarkDest;
			    			$mark['IdSemester']=$idsemesterdest;
			    			$mark['IdStudentRegSubjects']=$idregsubject;
			    			unset($mark['IdStudentMarksEntry']);
			    			unset($mark['ApprovedBy']);
			    			unset($mark['ApprovedOn']);
			    			unset($mark['MarksEntryStatus']);
			    			//chek mark dest
			    			$markdest = $dbMark->checkMarkEntry($idstundet, $idMarkDest, $idregsubject, $idsemesterdest);
			    			//echo var_dump($mark);exit;
			    			if ($markdest) 				 
			    				$dbMark->deleteData($markdest['IdStudentMarksEntry']);
			    			$dbMark->addData($mark);
			  
		    			}
		    		}
		    	}
	   		}
	   		$this->_redirect($this->view->url(array('module'=>'examination','controller'=>'mark-entry', 'action'=>'copy-mark','idsemesterdest'=>$this->idsemesterdest,'idsemestersource'=>$this->idsemestersource,'idprogram'=>$this->idprogram),'default',true));
	    }
	    
    }
    public function getName($full,$back,$front) {
    
    	//get salutatuion
    	$definationsDb = new App_Model_General_DbTable_Definationms();
    	$FrontSalutation = $definationsDb->getData($front);
    	$BackSalutation  = $definationsDb->getData($back);
    
    	$deanName=$full;
    	if (isset($FrontSalutation['DefinitionDesc'])) {
    		$deanName=$FrontSalutation['DefinitionDesc'].' '.$deanName;
    	}
    	if (isset($BackSalutation['DefinitionDesc'])) {
    		$deanName=$deanName.', '.$BackSalutation['DefinitionDesc'];
    	}
    	return $deanName;
    }
   
    
}