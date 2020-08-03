<?php
class Servqual_Model_DbTable_SurveySchedule extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_survey_schedule';
	protected $_primary ='IdServqualSurveySchedule';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			//$lobjFormData['update_date']=date('d-m-yyy');
			$where = 'IdSurvey='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$schedule=$this->isScheduleSet($lobjFormData['IdSurvey'], $lobjFormData['IdSemester'], $lobjFormData['IdProgram']);
		if (!$schedule) {
			$db->insert($this->_name,$lobjFormData);
			$lastInsertId = $this->getAdapter()->lastInsertId();
			return $lastInsertId;
		}
		else {
			$id=$schedule['IdServqualSurveySchedule'];
			$db->update($this->_name,$lobjFormData,$id);
		}
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualSurveySchedule='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('smt'=>'tbl_semestermaster'),'sq.IdSemester=smt.IdSemesterMaster',array('SemesterName'=>'smt.SemesterMainName'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqualSurveySchedule=?',$idserqual);
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
			$select->where('IdServqualSurveySchedule=?',$idserqual);
		}
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
	public function getDataPerSemester($sem,$prog){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('ss'=>'tbl_servqual_survey'),'ss.IdSurvey=sq.IdSurvey',array('SurveyName'))
		->join(array('pr'=>'tbl_program'),'pr.IdProgram=sq.IdProgram',array('ProgramCode','ProgramName'=>'ArabicName'))
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sq.IdSemester',array('SemesterName'=>'SemesterMainName'))
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->where('sq.IdProgram=?',$prog)
		->where('sq.IdSemester=?',$sem);
		
		
		$row=$db->fetchAll($select);
		return $row;
	
	}
	public function isScheduleSet($survey,$sem,$prog){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->where('sq.IdProgram=?',$prog)
		->where('sq.IdSurvey=?',$survey)
		
		->where('sq.IdSemester=?',$sem);
	
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
	
	public function getActiveSemester($prog){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name)) 
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->where('sq.IdProgram=?',$prog)
		->where('sq.Survey_start<=CURDATE() and sq.Survey_stop>=CURDATE()');
	
		$row=$db->fetchRow($select);
	 
		return $row;
	
	}
	
	public function isOpen($idprogram,$semester,$type){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sqs'=>$this->_name))
		->join(array('ss'=>'tbl_servqual_survey'),'sqs.IdSurvey=ss.IdSurvey')
		->join(array('def'=>'tbl_definationms'),'ss.type_survey=def.IdDefinition',array('DefinitionCode','surveytype'=>'def.BahasaIndonesia'))
		->where('sqs.IdSemester=?',$semester)
		->where('sqs.IdProgram=?',$idprogram)
		->where('def.DefinitionCode=?',$type)
		->where('sqs.Survey_start <=CURDATE() and sqs.Survey_stop >= CURDATE()');
		$row=$db->fetchRow($select);
		 
		return $row;
	
	}
	
	public function getSurvey($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey',array('IdTarget'=>'IdSurveyTarget'))
		->join(array('srs'=>'tbl_studentregsubjects'),'st.IdGroup=srs.IdCourseTaggingGroup',array())
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('TargetName'=>'CONCAT(sm.ShortName," ",sm.BahasaIndonesia)'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
		->where('sq.active="1"')
		->where('srs.IdStudentRegistration=?',$id);
		$row=$db->fetchAll($select);
		if(!$row) {
			//get from subject list
			$select = $db->select()
			->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
			->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey')
			->join(array('srs'=>'tbl_studentregsubjects'),'st.IdGroup=srs.IdCourseTaggingGroup',array('IdTarget'=>'srs.IdCourseTaggingGroup'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('TargetName'=>'CONCAT(sm.ShortName," ",sm.BahasaIndonesia)'))
			->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
			->where('sq.active="1"')
			->where('st.IdGroup is null')
			->where('srs.IdStudentRegistration=?',$id);
			$row=$db->fetchAll($select);
		}
		if(!$row) {
			//get from program
			$select = $db->select()
			->from(array('sq'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
			->join(array('st'=>'tbl_servqual_survey_target'),'st.IdSurvey=sq.IdSurvey')
			->join(array('srs'=>'tbl_studentregsubjects'),'st.IdGroup=srs.IdCourseTaggingGroup',array('IdTarget'=>'srs.IdCourseTaggingGroup'))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('TargetName'=>'CONCAT(sm.ShortName," ",sm.BahasaIndonesia)'))
			->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
			->where('sq.active="1"')
			->where('st.IdSubject is null')
			->where('srs.IdStudentRegistration=?',$id);
			$row=$db->fetchAll($select);
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
     
}