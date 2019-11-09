<?php


class Servqual_SurveyController extends Zend_Controller_Action {

	private $_sis_session;

	public function init(){
		$this->_sis_session = new Zend_Session_Namespace('sis');
	}

	public function indexAction() {

	    $status = $this->_getParam('status', null);
	    
	    if($status){
    	    if($status==1){
    	      $this->view->noticeSuccess = "Survey Created";
    	    }else
            if($status==0){
              $this->view->noticeError = "Unable to create batch invoice";
    	    }
	    }
		//title
		$this->view->title= $this->view->translate("Survey Creation");
		
		$servqualDb = new Servqual_Model_DbTable_Survey();
		$survey_list = $servqualDb->getData();
	
		$this->view->survey_list = $survey_list;
		 
	}
	public function targetAction() {
	
		$status = $this->_getParam('status', null);
		$idsurvey = $this->_getParam('idsurvey',null);
		 
		if($status){
			if($status==1){
				$this->view->noticeSuccess = "Survey Target Created";
			}else
			if($status==0){
				$this->view->noticeError = "Unable to create batch invoice";
			}
		}
		//title
		$this->view->title= $this->view->translate("Survey Target Creation");
	
		$servqualDb = new Servqual_Model_DbTable_SurveyTarget();
		$target_list = $servqualDb->getDataProgramBySurvey($idsurvey);
		
		$this->view->idsurvey=$idsurvey;
		$this->view->target_list = $target_list;
			
	}public function viewTargetAction() {
	
		$status = $this->_getParam('status', null);
		$idsurvey = $this->_getParam('idsurvey',null);
		$idprogram=$this->_getParam('prg',null);
		$this->view->prg = $idprogram;
		$idsemester=$this->_getParam('smt',null);
		$this->view->smt = $idsemester;
		if($status){
			if($status==1){
				$this->view->noticeSuccess = "Survey Target Created";
			}else
			if($status==0){
				$this->view->noticeError = "Unable to create batch invoice";
			}
		}
		//title
		$this->view->title= $this->view->translate("Survey Target Creation");
	
		$servqualDb = new Servqual_Model_DbTable_SurveyTarget();
		$target_list = $servqualDb->getTarget($idsurvey, $idsemester, $idprogram);
		$this->view->idsurvey=$idsurvey;
		$this->view->target_list = $target_list;
			
	}
	
	public function newSurveyAction(){
		//title
		$this->view->title= $this->view->translate("Survey - Create New");
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$dbCommon=new App_Model_Common();
		$dbServqual=new Servqual_Model_DbTable_Servqual();
		$dbSurvey=new Servqual_Model_DbTable_Survey();
		$dbSurveyTarget=new Servqual_Model_DbTable_SurveyTarget();
		$ses_survey = new Zend_Session_Namespace('survey');
		$ses_survey->setExpirationSeconds(900);
		$edit = $this->_getParam('id', null);
		if ($edit>0) {
			// initialization
			$ses_survey->survey=array();
			$ses_survey->target=array();
			$ses_survey->survey=$dbSurvey->getRows($edit);
			$targets=$dbSurveyTarget->getDataBySurvey($edit);
			
			//echo var_dump($target);exit;
			foreach ($targets as $key=>$target) {
				$ses_survey->faculty=$target['IdCollege'];
				$ses_survey->program=$target['IdProgram'];
				$ses_survey->branch=$target['IdBranch'];
				$subjects[$key]=$target['IdSubject'];
				$groups[$target['IdSubject']][]=$target['IdGroup'];
			}
			$ses_survey->selectedsubjects=$subjects;
			$ses_survey->selectedsubjectsgroup=$groups;
		}
		$step = $this->_getParam('step', 1);
		$this->view->step = $step;
		
		
		
		if($step==1){ //STEP 1
			
			//set name of survey
			//$idservqual=$this->getRequest()->getParam('id',null);
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				//echo var_dump($formData);exit;
				if($formData['SurveyName']!=null){
					unset($formData['submit']);
					$ses_survey->survey = $formData;
				
					//redirect
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
				}
				
			}
			//type survey
			
			$typeList=$dbCommon->fnGetTypeOfSurvey();
			//echo var_dump($typeList);exit;
			$this->view->surveytype=$typeList;
			$this->view->semester=$dbSem->getCountableSemester();
			//echo var_dump($this->view->semester);exit;
			$this->view->servqual=$dbServqual->getData();
			
			if(isset($ses_survey->survey)){
				$this->view->survey = $ses_survey->survey;
			}
			
			
		}else
		if($step==2){ //STEP 2
			
			//College
			if(!isset($ses_survey->survey)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->faculty=$formData['IdCollege'];
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>3),'default',true));
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$this->view->facultylist=$dbCollege->fngetCollegemasterDetails();
			
			if (isset($ses_survey->faculty)){
				
				$this->view->faculty= $ses_survey->faculty;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
						
		}else
		if ($step==3){ //STEP 3 Program
			if(!isset($ses_survey->survey)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>2),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->program=$formData['IdProgram'];
				if ($ses_survey->program=='All') 
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>7),'default',true));
				else 
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>4),'default',true));
			}
			$dbProgram=new GeneralSetup_Model_DbTable_Program();
			$this->view->programlist=$dbProgram->getProgram($ses_survey->faculty);
			//echo var_dump($this->view->programlist);exit;
			if (isset($ses_survey->program)){
				
				$this->view->program= $ses_survey->program;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
    		  		
    		//echo var_dump($ses_servqual_setup->grp_question);exit;
			
		}else
		if ($step==4){ //STEP 4 Branch
			if(!isset($ses_survey->survey)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>3),'default',true));
			}
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->branch=$formData['IdBranch'];
				//if ($ses_survey->branch=='All') 
				//	$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>7),'default',true));
				//else 
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>5),'default',true));
			}
			$program=$ses_survey->program;
			$dbProgram=new GeneralSetup_Model_DbTable_Branchofficevenue();
			$this->view->branchlist=$dbProgram->fnGetAllBranchList();
				
			if (isset($ses_survey->branch)){
		
				$this->view->branch= $ses_survey->branch;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
		
			//echo var_dump($ses_servqual_setup->grp_question);exit;
				
		}else
		if ($step==5){ //STEP 5 Subjects
		
			if(!isset($ses_survey->survey)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>3),'default',true));
			}
			if(!isset($ses_survey->branch)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>4),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				
				
				if (!isset($formData['subject'])) {
					$ses_survey->selectedsubjects==array();
					//echo var_dump($ses_survey);exit;
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>7),'default',true));
					
				} else 
				{
					$ses_survey->selectedsubjects=$formData['subject'];
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>6),'default',true));
			
				}
			}
			
			//get subject in course group based on Program and Branch
			$idprogram=$ses_survey->program;
			$idbranch=$ses_survey->branch;
			$idsemester=$ses_survey->survey['IdSemester'];
			//echo $idprogram.'-'.$idsemester.' branh'.$idbranch;exit;
			$dbProgram=new GeneralSetup_Model_DbTable_CourseGroup();
			$this->view->subjectlist=$dbProgram->getCountStudentCourseList($idprogram, $idbranch, $idsemester);
		
			if (isset($ses_survey->selectedsubjects)){
		
				$this->view->selectedsubjects= $ses_survey->selectedsubjects;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
		
			//echo var_dump($ses_servqual_setup->grp_question);exit;
		
		}
		else 
			if ($step==6){ //STEP 6 Groups in Subject
			
				if(!isset($ses_survey->survey)){
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
				}
				if(!isset($ses_survey->faculty)){
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>2),'default',true));
				}
				if(!isset($ses_survey->program)){
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>3),'default',true));
				}
				if(!isset($ses_survey->branch)){
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>4),'default',true));
				}
				if(!isset($ses_survey->selectedsubjects)){
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>4),'default',true));
				}
				if ($this->getRequest()->isPost()) {
						
					$formData = $this->getRequest()->getPost();
					$ses_survey->selectedsubjectsgroup=$formData['IdCourseTaggingGroup'];
					//echo var_dump($ses_survey->selectedsubjectsgroup);echo '--';echo var_dump($ses_survey->selectedsubjects);exit;
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>7),'default',true));
					
				}
					
				//get subject in course group based on Program and Branch
				$idprogram=$ses_survey->program;
				$idbranch=$ses_survey->branch;
				$idsemester=$ses_survey->survey['IdSemester'];
				$subjects = $ses_survey->selectedsubjects;
				//echo var_dump($subjects);exit;
				$dbProgram=new GeneralSetup_Model_DbTable_CourseGroup();
				$dbSubject = new GeneralSetup_Model_DbTable_Subjectprerequisites();
				foreach ($subjects as $key=>$subject) {
					//
					$subjectsGroup[$key]=$dbSubject->fnviewSubject($subject);
					//echo var_dump($subjectsGroup);exit;
					$grps=$dbProgram->getCountStudentBySetOfCourse($idprogram, $idbranch, $idsemester, $subject);
					$subjectsGroup[$key]['Groups']=$grps;
					
				}
				//echo var_dump($subjectsGroup);exit;
				
				$this->view->subjectgroupslist=$subjectsGroup;
				$ses_survey->subjectgroupslist=$subjectsGroup;
				if (isset($ses_survey->selectedsubjectsgroup)){
			
					$this->view->selectedsubjectsgroup= $ses_survey->selectedsubjectsgroup;
					//echo var_dump($ses_servqual_setup->grp_list);exit;
				}
			
				//echo var_dump($ses_servqual_setup->grp_question);exit;
			
			}
			
		
		
		else if ($step==7 ) {
			if(!isset($ses_survey->survey)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>3),'default',true));
			}
			if(!isset($ses_survey->branch)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-survey', 'step'=>4),'default',true));
			}
			if ($this->getRequest()->isPost()) {
				
				$formData = $this->getRequest()->getPost();
				//echo var_dump($formData);exit;
				//if (isset($formData['submit'])) {
					$this->saveSurveySetup();
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'index'),'default',true));
			//	}
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$dbSem = new GeneralSetup_Model_DbTable_Semestermaster();
			$dbProgram = new GeneralSetup_Model_DbTable_Program();
			$dbBranch = new GeneralSetup_Model_DbTable_Branchofficevenue();
			//$this->view->faculty = $dbCollege->getCollege($ses_survey->faculty);
			$this->view->survey = $ses_survey->survey;
			$this->view->program= $dbProgram->fngetProgramData($ses_survey->program);
			$this->view->branch = $dbBranch->getData($ses_survey->branch);
			$this->view->semester = $dbSem->fnGetSemestermaster($ses_survey->survey['IdSemester']);
			$this->view->subjects = $ses_survey->subjectgroupslist;
			$this->view->groups = $ses_survey->selectedsubjectsgroup;
			//echo var_dump($this->view->groups);exit;
		}
		
	}
	public function newTargetAction(){
		//title
		$this->view->title= $this->view->translate("Target - Create New");
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$dbCommon=new App_Model_Common();
		$dbServqual=new Servqual_Model_DbTable_Servqual();
		$dbSurvey=new Servqual_Model_DbTable_Survey();
		//$dbSurvey=new Servqual_Model_DbTable_Survey();
		$dbSurveyTarget=new Servqual_Model_DbTable_SurveyTarget();
		$ses_survey = new Zend_Session_Namespace('survey');
		$ses_survey->setExpirationSeconds(900);
		$edit = $this->_getParam('id', null);
		$add = $this->_getParam('add', null);
		//if ($edit!=null) 
			
		//echo var_dump($ses_survey->survey);exit;
		if ($edit>0) {
			// initialization
			$ses_survey->survey=$dbSurvey->getData($edit);
			$ses_survey->semester=array();
			$ses_survey->target=array();
			$ses_survey->idsurvey=$edit;
			if ($add!='1') {
				
				$targets=$dbSurveyTarget->getDataBySurvey($edit);
				
			//echo var_dump($target);exit;
				foreach ($targets as $key=>$target) {
					$ses_survey->semester=$target['IdSemester'];
					$ses_survey->faculty=$target['IdCollege'];
					$ses_survey->program=$target['IdProgram'];
					$ses_survey->branch=$target['IdBranch'];
					$subjects[$key]=$target['IdSubject'];
					if (isset($target['IdSubject'])) {
						$targets[$target['IdGroup']]=$target['IdSurveyTarget'];
						$groups[$target['IdSubject']][]=$target['IdGroup'];
					}
					else $targets[0]=$target['IdSurveyTarget'];
				}
				$ses_survey->targets=$targets;
				$ses_survey->selectedsubjects=$subjects;
				$ses_survey->selectedsubjectsgroup=$groups;
			}
			else
			{
				unset($ses_survey->semester);
				unset($ses_survey->faculty);
				unset($ses_survey->program);
				unset($ses_survey->branch);
				unset($ses_survey->targets);
				unset($ses_survey->selectedsubjects);
				unset($ses_survey->selectedsubjectsgroup);
			}
				
		}
		
		$step = $this->_getParam('step', 1);
		$this->view->step = $step;
	
	
	
		if($step==1){ //STEP 1
				
			//set name of survey
			//$idservqual=$this->getRequest()->getParam('id',null);
				
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				//echo var_dump($formData);exit;
				if($formData['IdSemester']!=null){
					unset($formData['submit']);
					$ses_survey->semester = $formData['IdSemester'];
					//redirect
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
				}
	
			}
			//type survey
				
			$typeList=$dbCommon->fnGetTypeOfSurvey();
			//echo var_dump($typeList);exit;
			//$this->view->surveytype=$typeList;
			$this->view->semester_list=$dbSem->getCountableSemester();
			//echo var_dump($this->view->semester);exit;
			//$this->view->servqual=$dbServqual->getData();
				
			if(isset($ses_survey->semester)){
				$this->view->semester = $ses_survey->semester;
			}
				
				
		}else
		if($step==2){ //STEP 2
				
			//College
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
				
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->faculty=$formData['IdCollege'];
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>3),'default',true));
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$this->view->facultylist=$dbCollege->fngetCollegemasterDetails();
				
			if (isset($ses_survey->faculty)){
	
				$this->view->faculty= $ses_survey->faculty;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
	
		}else
		if ($step==3){ //STEP 3 Program
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
			}
				
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->program=$formData['IdProgram'];
				if ($ses_survey->program=='All')
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>7),'default',true));
				else
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>4),'default',true));
			}
			$dbProgram=new GeneralSetup_Model_DbTable_Program();
			$this->view->programlist=$dbProgram->getProgram($ses_survey->faculty);
			//echo var_dump($this->view->programlist);exit;
			if (isset($ses_survey->program)){
	
				$this->view->program= $ses_survey->program;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
	
			//echo var_dump($ses_servqual_setup->grp_question);exit;
				
		}else
		if ($step==4){ //STEP 4 Branch
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>3),'default',true));
			}
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				$ses_survey->branch=$formData['IdBranch'];
				//if ($ses_survey->branch=='All')
				//	$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>7),'default',true));
				//else
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>5),'default',true));
			}
			$program=$ses_survey->program;
			$dbProgram=new GeneralSetup_Model_DbTable_Branchofficevenue();
			$this->view->branchlist=$dbProgram->fnGetAllBranchList();
	
			if (isset($ses_survey->branch)){
	
				$this->view->branch= $ses_survey->branch;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
	
			//echo var_dump($ses_servqual_setup->grp_question);exit;
	
		}else
		if ($step==5){ //STEP 5 Subjects
	
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>3),'default',true));
			}
			if(!isset($ses_survey->branch)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>4),'default',true));
			}
				
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
	
	
				if (!isset($formData['subject'])) {
					$ses_survey->selectedsubjects==array();
					//echo var_dump($ses_survey);exit;
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>7),'default',true));
						
				} else
				{
					$ses_survey->selectedsubjects=$formData['subject'];
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>6),'default',true));
						
				}
			}
				
			//get subject in course group based on Program and Branch
			$idprogram=$ses_survey->program;
			$idbranch=$ses_survey->branch;
			$idsemester=$ses_survey->semester;
			//echo $idprogram.'-'.$idsemester.' branh'.$idbranch;exit;
			$dbProgram=new GeneralSetup_Model_DbTable_CourseGroup();
			$this->view->subjectlist=$dbProgram->getCountStudentCourseList($idprogram, $idbranch, $idsemester);
	
			if (isset($ses_survey->selectedsubjects)){
	
				$this->view->selectedsubjects= $ses_survey->selectedsubjects;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
	
			//echo var_dump($ses_servqual_setup->grp_question);exit;
	
		}
		else
		if ($step==6){ //STEP 6 Groups in Subject
				
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>3),'default',true));
			}
			if(!isset($ses_survey->branch)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>4),'default',true));
			}
			if(!isset($ses_survey->selectedsubjects)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>4),'default',true));
			}
			if ($this->getRequest()->isPost()) {
	
				$formData = $this->getRequest()->getPost();
				$ses_survey->selectedsubjectsgroup=$formData['IdCourseTaggingGroup'];
				//echo var_dump($ses_survey->selectedsubjectsgroup);echo '--';echo var_dump($ses_survey->selectedsubjects);exit;
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>7),'default',true));
					
			}
				
			//get subject in course group based on Program and Branch
			$idprogram=$ses_survey->program;
			$idbranch=$ses_survey->branch;
			$idsemester=$ses_survey->semester;
			$subjects = $ses_survey->selectedsubjects;
			//echo var_dump($subjects);exit;
			$dbProgram=new GeneralSetup_Model_DbTable_CourseGroup();
			$dbSubject = new GeneralSetup_Model_DbTable_Subjectprerequisites();
			foreach ($subjects as $key=>$subject) {
				//
				$subjectsGroup[$key]=$dbSubject->fnviewSubject($subject);
				//echo var_dump($subjectsGroup);exit;
				$grps=$dbProgram->getCountStudentBySetOfCourse($idprogram, $idbranch, $idsemester, $subject);
				$subjectsGroup[$key]['Groups']=$grps;
					
			}
			//echo var_dump($subjectsGroup);exit;
	
			$this->view->subjectgroupslist=$subjectsGroup;
			$ses_survey->subjectgroupslist=$subjectsGroup;
			if (isset($ses_survey->selectedsubjectsgroup)){
					
				$this->view->selectedsubjectsgroup= $ses_survey->selectedsubjectsgroup;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
				
			//echo var_dump($ses_servqual_setup->grp_question);exit;
				
		}
			
	
	
		else if ($step==7 ) {
			if(!isset($ses_survey->semester)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>1),'default',true));
			}
			if(!isset($ses_survey->faculty)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>2),'default',true));
			}
			if(!isset($ses_survey->program)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>3),'default',true));
			}
			if(!isset($ses_survey->branch)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'new-target', 'step'=>4),'default',true));
			}
			if ($this->getRequest()->isPost()) {
				//echo "ok";exit;
				$formData = $this->getRequest()->getPost();
				//echo var_dump($formData);exit;
				//if (isset($formData['submit'])) {
				$this->saveTargetSetup();
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'target','id'=>$ses_survey->idsurvey),'default',true));
				//	}
			}
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$dbSem = new GeneralSetup_Model_DbTable_Semestermaster();
			$dbProgram = new GeneralSetup_Model_DbTable_Program();
			$dbBranch = new GeneralSetup_Model_DbTable_Branchofficevenue();
			//$this->view->faculty = $dbCollege->getCollege($ses_survey->faculty);
			$this->view->survey = $ses_survey->survey;
		//	echo var_dump($ses_survey->survey);exit;
			$this->view->program= $dbProgram->fngetProgramData($ses_survey->program);
			$this->view->branch = $dbBranch->getData($ses_survey->branch);
			$this->view->semester = $dbSem->fnGetSemestermaster($ses_survey->semester);
			$this->view->subjects = $ses_survey->subjectgroupslist;
			$this->view->groups = $ses_survey->selectedsubjectsgroup;
			//echo var_dump($this->view->groups);exit;
		}
	
	}
	
	
	
	private function saveSurveySetup(){
	  
	  //data from configuration screen
	$ses_survey = new Zend_Session_Namespace('survey');
	   
	  if(
	      !isset($ses_survey->survey) ||
	      !isset($ses_survey->faculty) 
	       ){
	  
	    throw new Exception("No data of survey ");
	    exit;
	  }
	  //save Survey
	  $survey=$ses_survey->survey;
	  $db=new Servqual_Model_DbTable_Survey();
	  if ($survey['IdSurvey']=='') {
	  	$survey['update_date']=date('D-m-Y');
	    $id = $db->insertData($survey);
	  }
	   else {
	   	
	   		//echo var_dump($survey);exit;
	   		$id=$survey['IdSurvey'];
	   		unset($survey['IdSurvey']);
	   		$db->updateData($survey,$id );
	   }
	  //save Target
	  $db=new Servqual_Model_DbTable_SurveyTarget();
	  $subjects = $ses_survey->selectedsubjectsgroup;
	  //echo var_dump($dimensions);exit;
	  $target['IdSurvey']=$id;
	  $target['IdSemester']=$ses_survey->semester;
	  $target['IdCollege']=$ses_survey->faculty;
	  $target['IdProgram']=$ses_survey->program;
	  $target['IdBranch']=$ses_survey->branch;
	  
	  $db->deleteData($id);
	  if (count($subjects)>0) {
	  	foreach ($subjects as $key=>$subject) {
	  		$target['IdSubject']=$key;
	  		foreach ($subject as  $grp) {
	  	
	  			$target['IdGroup']=$grp;
	   			$db->insertData($target);
	  	
	  		}
	  	}
	  } else {
	  	$target['IdSubject']=null;
	  	$target['IdGroup']=null;
	  	$db->insertData($target);
	  }
	  
	  
	 }
	 private function saveTargetSetup(){
	 	 
	 	//data from configuration screen
	 	$ses_survey = new Zend_Session_Namespace('survey');
	 
	 	if(
	 	!isset($ses_survey->semester) ||
	 	!isset($ses_survey->faculty)
	 	){
	 		 
	 		throw new Exception("No data of survey ");
	 		exit;
	 	}
	 	//save Survey
	 	/*$survey=$ses_survey->survey;
	 	$db=new Servqual_Model_DbTable_Survey();
	 	if ($survey['IdSurvey']=='') {
	 		$survey['update_date']=date('D-m-Y');
	 		$id = $db->insertData($survey);
	 	}
	 	else {
	 		//echo var_dump($survey);exit;
	 		$id=$survey['IdSurvey'];
	 		unset($survey['IdSurvey']);
	 		$db->updateData($survey,$id );
	 	}*/
	 	//save Target
	 	$db=new Servqual_Model_DbTable_SurveyTarget();
	 	$subjects = $ses_survey->selectedsubjectsgroup;
	 	//echo var_dump($dimensions);exit;
	 	$target['IdSurvey']=$ses_survey->idsurvey;
	 	$target['IdSemester']=$ses_survey->semester;
	 	$target['IdCollege']=$ses_survey->faculty;
	 	$target['IdProgram']=$ses_survey->program;
	 	$target['IdBranch']=$ses_survey->branch;
	 	// echo var_dump($subjects);exit;
	 	//$db->deleteData($id);
	 	if (count($subjects)>0) {
	 		foreach ($subjects as $key=>$subject) {
	 			$target['IdSubject']=$key;
	 			foreach ($subject as  $grp) {
	
	 				$target['IdGroup']=$grp;
	 				if (!isset($ses_survey->targets[$grp]))
	 					$db->insertData($target);
	 
	 			}
	 		}
	 	} else {
	 		$target['IdSubject']=null;
	 		$target['IdGroup']=null;
	 		
	 		if (!$db->isTarget($ses_survey->idsurvey, $ses_survey->semester, $ses_survey->program)) {
	 			$db->insertData($target);
	 		}
	 	}
	 	 
	 	 
	 }
	 public function viewSurveyAction(){
	 	
	 	$id=$this->_getParam('id');
	 	$dbServqual=new Servqual_Model_DbTable_Servqual();
	 	$this->view->servqual=$dbServqual->getData($id);
	 	$dbDimension = new Servqual_Model_DbTable_ServqualDimension();
	 	$this->view->dimension_list=$dbDimension->getRows($id);
	 	$this->view->title='Quistioner';
	 	//echo var_dump($this->view->servqual);exit;
	 	
	 	
	 }
	 
	 public function feedbackAction(){
	 	
	 	$idsurvey = $this->getRequest()->getParam('id');
	 	$idtarget = $this->getRequest()->getParam('idtarget');
	 	$idresponden = $this->getRequest()->getParam('idresponden');
	 	$type = $this->getRequest()->getParam('type');
	 	$this->view->idresponden=$idresponden;
	 	$this->view->type=$type;
	 	if ($this->getRequest()->isPost()) {
	 	
	 		$formData = $this->getRequest()->getPost();
	 		//echo var_dump($formData);exit;
	 		//save transaction head
	 		$head['IdSurveyTarget']=$idtarget;
	 		$head['IdSurvey']=$formData['IdSurvey'];
	 		$head['IdProgram']=$formData['IdProgram'];
	 		$head['IdSemester']=$formData['IdSemester'];
	 		if ($type=='0') {
	 			$head['IdSubject']=null;
	 			$head['IdGroupCourse']=null;
	 			$head['expectation']='0';
	 		} else {
	 			$head['IdSubject']=$formData['IdSubject'];
	 			$head['IdGroupCourse']=$formData['IdGroupCourse'];
	 			$head['expectation']='1';
	 		}
	 		$dbTransHead=new Servqual_Model_DbTable_ServqualTransactionHead();
	 		//if ($formData['IdServqualTransaction']=='')
	 		$idtranshead=$dbTransHead->insertData($head);
	 		//else $idtranshead=$dbTransHead->updateData($head, $formData['IdServqualTransaction']);
	 		//save transaction reponden
	 		$respondens["IdServqualTransaction"]=$idtranshead;
	 		
	 		$idresponden = $formData['IdResponden'];
	 		$respondens["IdResponden"]=$idresponden;
	 		$type = $formData['Responden_type'];
	 		$this->view->idresponden=$idresponden;
	 		$this->view->type=$type;
	 		$respondens["Responden_type"]=$type;
	 		$dbTransResponden=new Servqual_Model_DbTable_ServqualTransactionResponden();
	 		$idServqualResponden = $dbTransResponden->insertData($respondens);
	 		//save transaction
	 		$transac['IdTransactionResponden']=$idServqualResponden;
	 		$answers = $formData['answer'];
	 		//echo var_dump($answers);exit;
	 		$dbTransac=new Servqual_Model_DbTable_ServqualTransaction();
	 		foreach ($answers as $key=>$score) {
				$transac['question_id'] = $key;
				$transac['real_score']=$score;
				if (isset($question['Remark'])) $transac['Remark']=$question['Remark'];
				
				$dbTransac->insertData($transac);
				
	 		}
	 		//update complete
	 		$response['Complete']='1';
	 		$dbTransResponden->updateData($response, $idServqualResponden);
	 		//echo "jj";exit;
	 		$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'dispatcher','id'=>$idresponden,'type'=>'student' ),'default',true));
	 		//echo var_dump($formData);exit;
	 	}
	 	
	 	$dbStudent=new Registration_Model_DbTable_Studentregistration();
	 	$dbSurvey=new Servqual_Model_DbTable_Survey();
	 	$dbSurveytarget=new Servqual_Model_DbTable_SurveyTarget();
	 	$responden=$dbStudent->getData($idresponden);
	 	if (isset($responden)) {
	 		$this->view->responden=$responden;
	 	}
	 	$survey = $dbSurvey->getData($idsurvey,$type);
	 	$this->view->survey = $survey;
	 	//echo $type;
	 //	echo var_dump($survey);exit;
	 	$this->view->surveytarget=$dbSurveytarget->getDataDetail($idtarget,$type);
	 	//echo var_dump($this->view->surveytarget);exit;
	 	$dbServqual = new Servqual_Model_DbTable_Servqual();
	 	$this->view->headQuistioner = $dbServqual->getRows($survey['IdServqual']);
	 	$dbQuisioner=new Servqual_Model_DbTable_ServqualDetail();
	 	$this->view->quistioner = $dbQuisioner->getDetailQuetioner($survey['IdServqual']);
	 	if ($type=='0') 
	 		$this->view->title = 'Harapan Pemangku Kepentingan';
	 	else 
	 		$this->view->title = 'Penilaian Pemangku Kepentingan';
	 }
	 
	 public function listOfRespondenceAction() {
	 	$this->view->title='List of Respondence';
	 	$form = new Servqual_Form_SearchStudent();
	 	if ($this->getRequest()->isPost()) {
	 		$db=new Servqual_Model_DbTable_Survey();
	 		$formData = $this->getRequest()->getPost();
	 		$this->view->listofrespondence=$db->SearchRespondence($formData);
	 	}
	 	$this->view->form=$form;
	 }
	 
	 public function listOfTargetAction(){
	 	$idresponden = $this->getRequest()->getParam('id');
	 	//get Open Survey
	 	$dbSurvey = new Servqual_Model_DbTable_Survey();
	 	$survey= $dbSurvey->getSurvey($idresponden);
	 	$this->view->idresponden = $idresponden;
	 	$this->view->surveytarget=$survey;
	 }
	 
	 public function dispatcherAction(){
	 	$idresponden = $this->getRequest()->getParam('id');
	 	$type = $this->getRequest()->getParam('type');
	 	$dbsurveyresult = new Servqual_Model_DbTable_ServqualTransactionHead();
	 	$dbSubreg = new Examination_Model_DbTable_StudentRegistrationSubject();
	 	$dbSurvey = new Servqual_Model_DbTable_Survey();
	 	$dbRegis = new Examination_Model_DbTable_StudentRegistration();
	 	$dbSurveyTarget=new Servqual_Model_DbTable_SurveyTarget();
	 	$dbSurveSchedule= new Servqual_Model_DbTable_SurveySchedule();
	 	//echo $type;exit;
	 	if ($type=='student') {
	 		//get student info
	 		$student=$dbRegis->getStudentInfo($idresponden);
	 		$idprogram=$student['IdProgram'];
	 		//get active semester
	 		//echo var_dump($student);exit;
	 		$semesters=$dbSurveSchedule->getActiveSemester($idprogram);
	 		if (isset($semesters) && $semesters) {
	 			//echo $idprogram;exit;
	 			$idsemester=$semesters['IdSemester'];
	 			if ($idsemester!=null) {
	 				
			 		//is expected survey done, if no do this first
		 			$survey=$dbSurveSchedule->isOpen($idprogram,$idsemester,'0');
		 			
		 			if (isset($survey) && $survey!=array()) {
		 				$idsurvey=$survey['IdSurvey'];
		 				//echo var_dump($survey);exit;
			 			if (!$dbsurveyresult->isExpectedDone($idresponden,$idsemester,$idprogram)) {
			 				$targets=$dbSurveyTarget->getTarget($idsurvey, $idsemester, $idprogram);
			 				$idtarget=$targets['IdSurveyTarget'];
			 				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'feedback','id'=>$idsurvey, 'idtarget'=>$idtarget,'idresponden'=>$idresponden,'type'=>'0'),'default',true));
			 			}
			 			
			 		}		 			
			 		//get data subject
			 		//echo $idsemester;exit;
			 		$survey=$dbSurveSchedule->isOpen($idprogram,$idsemester,'1');
			 		if ($survey && isset($survey) && $survey!=array()) {
			 			$idsurvey=$survey['IdSurvey'];
			 			$subjects = $dbSubreg->getCourseRegisteredBySemesterOri($idresponden,$idsemester);
			 			//echo var_dump($subjects);exit;
			 			foreach ($subjects as $subject) {
			 				//cek should be surveyed
			 				
			 				if ( $dbSurveyTarget->isTarget($idsurvey, $idsemester, $idprogram, $subject['IdSubject'],$subject['IdCourseTaggingGroup'])) {
			 					//get target
			 					$targets=$dbSurveyTarget->getTarget($idsurvey, $idsemester, $idprogram, $subject['IdSubject'],$subject['IdCourseTaggingGroup']);
			 					$idtarget=$targets['IdSurveyTarget'];
			 					if (!$dbsurveyresult->isRealityDone($idresponden,$idsemester,$idtarget,$idprogram)) {	
			 						
			 						$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'feedback', 'id'=>$idsurvey, 'idtarget'=>$idtarget,'idresponden'=>$idresponden,'type'=>'1'),'default',true));
			 					}
			 				}
			 			
			 			}
			 		}
	 			}
	 		}
	 		
	 		
	 	}
	 	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal','action'=>'index'),'default',true));
	 	
	 }
	 
	 public function surveyScheduleAction(){
	 	
	 	$form = new Servqual_Form_SurveySchedule();
	 	$dbSurveSchedule= new Servqual_Model_DbTable_SurveySchedule();
	 	$dbSurvey = new Servqual_Model_DbTable_Survey();
	 	$dbProg=new GeneralSetup_Model_DbTable_Program();
	 	$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
	 	$this->view->form=$form;
	 	if ($this->getRequest()->isPost()) {
	 		
	 		$formData = $this->getRequest()->getPost();
	 		//echo var_dump($formData);exit;
	 		if (isset($formData['IdSemesterd'])) {
	 			//echo var_dump($formData);exit;
	 			$data['IdSurvey']=$formData['IdSurvey'];
	 			$data['IdProgram']=$formData['IdProgramd'];
	 			$data['IdSemester']=$formData['IdSemesterd'];
	 			$data['survey_start']=$formData['survey_start'];
	 			$data['survey_stop']=$formData['survey_stop'];
	 			$dbSurveSchedule->insertData($data);
	 		} else {
	 			$semester=$formData['IdSemester'];
	 			$program=$formData['IdProgram'];
	 			$this->view->survey_data = $dbSurveSchedule->getDataPerSemester($semester, $program);
	 		}
	 	}
	 	$this->view->semester_list=$dbSem->getCountableSemester();
	 	$this->view->program_list=$dbProg->getAllProgram();
	 	$this->view->survey_list=$dbSurvey->getData();
	 	$this->view->title='Survey Schedule Setup';
	 
	 	
	 }
	 
	 public function calculateSurveyAction(){
	 	$form = new Servqual_Form_SurveyResult();
	 	$dbSurveSchedule= new Servqual_Model_DbTable_SurveySchedule();
	 	$dbSurvey = new Servqual_Model_DbTable_Survey();
	 	$dbTransHead=new Servqual_Model_DbTable_ServqualTransactionHead();
	 	$dbResultHead = new Servqual_Model_DbTable_ServqualResultHead();
	 	$dbResult = new Servqual_Model_DbTable_ServqualResult();
	 	$this->view->form=$form;
	 	$this->view->title='Calculate Survey Result';
	 	if ($this->getRequest()->isPost()) {
	 		$formData = $this->getRequest()->getPost();
	 		$idprogram=$formData['IdProgram'];
	 		$idsemester=$formData['IdSemester'];
	 		$idsurvey=$formData['IdSurvey'];
	 		$surveys=$dbTransHead->surveyResult($idsurvey, $idsemester, $idprogram);
	 		$idTransaction=null;
	 		foreach ($surveys as $survey) {
	 			//insert data rekap
	 			//echo var_dump($survey);exit;
	 			if ($idTransaction!=$survey['IdServqualTransaction']) {
	 				//insert head
	 				if ($idTransaction!=null) {
	 					$head=$dbResultHead->getMean($idTransaction);
	 					if ($head && $head['n_of_respondens']>0) {
		 					$data['mean_head']=$head['sum_of_mean']/$head['n_of_respondens'];
		 					$data['n_of_respondens']=$head['n_of_respondens'];
		 					$dbResultHead->updateData($data, $idHead);
	 					}
	 				}
	 				$idTransaction=$survey['IdServqualTransaction'];
	 				$data=array();
	 				$data['IdServqualTransaction']=$idTransaction;
	 				$idHead=$dbResultHead->insertData($data);
	 			}
	 			$dataResult=array();
	 			$dataResult['IdHead'] =$idHead;
	 			$dataResult['question_id']=$survey['question_id'];
	 			$dataResult['mean']=$survey['mean'];
	 			$dataResult['n_of_respondens']=$survey['n_of_respondens'];
	 			$dbResult->insertData($dataResult);
	 			
	 		}
	 		//calculate last group of  Head result
	 		if ($idTransaction!=null) {
	 			//echo $idTransaction;exit;
	 			$head=$dbResultHead->getMean($idTransaction);
	 			if ($head && $head['n_of_respondens']>0) {
		 			$data['mean_head']=$head['sum_of_mean']/$head['n_of_respondens'];
		 			$data['n_of_respondens']=$head['n_of_respondens'];
		 			$dbResultHead->updateData($data, $idHead);
	 			}
	 		}
	 	
	 	}
	 	//get calculated Head
	 	if ($idsemester!=null && $idprogram!=null)
	 		$this->view->survey_data=$dbResultHead->getData($idsemester,$idprogram);
	 	
	 }
}
?>