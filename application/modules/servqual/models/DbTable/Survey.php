<?php
class Servqual_Model_DbTable_Survey extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_servqual_survey';
	protected $_primary ='IdSurvey';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$lobjFormData['update_date']=date('d-m-yyy');
			$where = 'IdSurvey='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$lobjFormData['active']='1';
		//$lobjFormData['active_date']=date('d-m-yyy');
		$db->insert($this->_name,$lobjFormData);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdSurvey='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null,$type=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
				//->join(array('pr'=>'tbl_program'),'pr.IdProgram=sq.Idprogram')
				->joinLeft(array('smt'=>'tbl_semestermaster'),'sq.IdSemester=smt.IdSemesterMaster',array('SemesterName'=>'smt.SemesterMainName'))
				->where('sq.active="1"');
		if ($type!=null)
			$select->where('DefinitionCode=?',$type);
		if ($idserqual!=null) {
			$select->where('IdSurvey=?',$idserqual);
			$row=$db->fetchRow($select);
		}
		else
			$row=$db->fetchAll($select);
		return $row;
		
	}
	
	
	public function getRows($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name));
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdSurvey=?',$idserqual);
		}
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
	
	public function isOpen($semester,$program){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdSemester=?',$semester)
		->where('sq.IdProgram=?',$program)
		->where('sq.survey_start <=CURDATE() and sq.survey_stop >= CURDATE()')
		->where('sq.active="1"');
		
		$row=$db->fetchRow($select);
		if ($row) return true; else return false;
	}
	
	public function getSurvey($id=null,$sem=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey',array('IdTarget'=>'IdSurveyTarget'))
		->join(array('srs'=>'tbl_studentregsubjects'),'st.IdGroup=srs.IdCourseTaggingGroup',array())
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('TargetName'=>'CONCAT(sm.ShortName," ",sm.BahasaIndonesia)'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
		->where('srs.IdStudentRegistration=?',$id);
		$select->where('st.IdSemester=?',$sem);
		//echo $select;exit;
		$row=$db->fetchAll($select);
		if(!$row) {
			//get from subject list
			$select = $db->select()
			->from(array('sq'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
			->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey')
			->join(array('srs'=>'tbl_studentregsubjects'),'st.IdGroup=srs.IdCourseTaggingGroup',array('IdTarget'=>'srs.IdCourseTaggingGroup'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('TargetName'=>'CONCAT(sm.ShortName," ",sm.BahasaIndonesia)'))
			->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
			//->where('sq.active="1"')
			->where('st.IdGroup is null')
			->where('srs.IdStudentRegistration=?',$id);
			 $select->where('st.IdSemester=?',$sem);
			$row=$db->fetchAll($select);
			
		}
		
		if(!$row) {
			//get from program
			$select = $db->select()
			->from(array('sr'=>'tbl_studentregistration'))
					->where('sr.IdStudentRegistration=?',$id);
			$row=$db->fetchRow($select);
			$idprogram=$row['IdProgram'];
			$select = $db->select()
			->from(array('sq'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
			->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey',array('IdTarget'=>'IdSurveyTarget'))
			 
			->where('st.IdProgram=?',$idprogram);
			$select->where('st.IdSemester=?',$sem);
			$row=$db->fetchAll($select);
			//echo $select;exit;
		}
		 
		return $row;
	
	}
	
	public function SearchRespondence($post) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('std'=>'tbl_studentregistration'),array('IdStudentRegistration','Identity'=>'registrationId'))
		->join(array('stp'=>'student_profile'),'std.IdApplication=stp.appl_id',array('Name'=>'CONCAT(appl_fname," ",appl_lname)'));
		
		if ($post['IdIntake']!=null){
			$select->where('std.IdIntake=?',$post['IdIntake']);
		}
		if (isset($post['student_id'])){
			$select->where('std.registrationId=?',$post['student_id']);
		}
		if ($post['IdProgram']!=null){
			$select->where('std.IdProgram=?',$post['IdProgram']);
		}
		$row=$db->fetchAll($select);
		return $row;
		
	}
     
	public function isAnyOpenSurvey($idstudentregistration,$semester=null) {
		$dbSurveSchedule= new Servqual_Model_DbTable_SurveySchedule();
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sr'=>'tbl_studentregistration'))
		->where('sr.IdStudentRegistration=?',$idstudentregistration);
		$row=$db->fetchRow($select);
		if (!$row) return false;
		$idprogram=$row['IdProgram'];
		//is open schedulle
		$semester=$dbSurveSchedule->getActiveSemester($idprogram);
		
		if ($semester){
			$semester=$semester['IdSemester'];
			 
			//get traget
			$targets=$this->getSurvey($idstudentregistration,$semester);
			$dbSurveyHead=new Servqual_Model_DbTable_ServqualTransactionHead();
			echo var_dump($targets);exit;
			foreach ($targets as $target) {
				//echo var_dump($target);
				$idSurveyTarget=$target['IdTarget'];
				//echo $target['type_survey'];exit;
				if ($target['type_survey']==649) {
					
					$row=$dbSurveyHead->isExpectedDone($idstudentregistration, $semester,$idprogram);
					//echo $semester;echo  var_dump($row);exit;
					if (!$row ) {
						//echo "expect";exit;
						return true;
					} 
				} else {
						$row=$dbSurveyHead->isRealityDone($idstudentregistration, $semester, $idSurveyTarget,$idprogram);
						
						if (!$row ) {
							//echo 'reality';exit;
							return true;
						}
					}
				}
		}
		 
		return false;
	}
	
	public function dispatcher($idresponden,$type){
		//$idresponden = $this->getRequest()->getParam('id');
		//$type = $this->getRequest()->getParam('type');
		$dbsurveyresult = new Servqual_Model_DbTable_ServqualTransactionHead();
		$dbSubreg = new Examination_Model_DbTable_StudentRegistrationSubject();
		$dbSurvey = new Servqual_Model_DbTable_Survey();
		$dbRegis = new Examination_Model_DbTable_StudentRegistration();
		$dbSurveyTarget=new Servqual_Model_DbTable_SurveyTarget();
		$dbSurveSchedule= new Servqual_Model_DbTable_SurveySchedule();
		 
		if ($type=='student') {
			//get student info
			 
			$student=$dbRegis->getStudentInfo($idresponden);
			$idprogram=$student['IdProgram'];
			//get active semester
			//echo $idprogram;exit;
			$semesters=$dbSurveSchedule->getActiveSemester($idprogram);
			//echo var_dump($semesters);exit;
			if (isset($semesters) && $semesters) {
				//echo $idprogram;exit;
				$idsemester=$semesters['IdSemester'];
				if ($idsemester!=null) {
	
					//is expected survey done, if no do this first
					$survey=$dbSurveSchedule->isOpen($idprogram,$idsemester,'0');
					if (isset($survey) && $survey!=array()) {
						$idsurvey=$survey['IdSurvey'];
						//echo var_dump($survey);exit;
						if (!$dbsurveyresult->isExpectedDone($idresponden,$idsemester,$idprogram,$idsurvey)) {
							$targets=$dbSurveyTarget->getTarget($idsurvey, $idsemester, $idprogram);
							//echo var_dump($targets);exit;
							$idtarget=$targets['IdSurveyTarget'];
							//echo $this->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'feedback','id'=>$idsurvey, 'idtarget'=>$idtarget,'idresponden'=>$idresponden,'type'=>'0'));exit;
							$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
							
							 
							$redirector->gotoUrl('/servqual/survey/feedback/id/'.$idsurvey.'/idtarget/'.$idtarget.'/idresponden/'.$idresponden.'/type/'.'0') ;
							//echo "ok";exit;
							//$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'feedback','id'=>$idsurvey, 'idtarget'=>$idtarget,'idresponden'=>$idresponden,'type'=>'0'),'default',true));
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
								//echo 'taget'.$subject ;
								if (!$dbsurveyresult->isRealityDone($idresponden,$idsemester,$idtarget,$idprogram)) {
									$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
										
									
									$redirector->gotoUrl('/servqual/survey/feedback/id/'.$idsurvey.'/idtarget/'.$idtarget.'/idresponden/'.$idresponden.'/type/'.'1') ;
									//$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'feedback', 'id'=>$idsurvey, 'idtarget'=>$idtarget,'idresponden'=>$idresponden,'type'=>'1'),'default',true));
								}
							}
								
						}
					}
				}
			}
	
	
		}
		//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal','action'=>'index'),'default',true));
		return false;
	}
}