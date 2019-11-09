<?php
class Servqual_Model_DbTable_SurveyTarget extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_survey_target';
	protected $_primary ='IdSurveyTarget';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$lobjFormData['update_date']=date('d-m-yyy');
			$where = 'IdSurveyTarget='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		//$lobjFormData['active']='1';
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
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
				->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdSurveyTarget=?',$idserqual);
			$row=$db->fetchRow($select);
		}
		else
		$row=$db->fetchAll($select);
		return $row;
		
	}
	
	public function isTarget($idsurvey,$idsemester,$idprogram,$idsubject=null,$idgroup=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->join(array('sqr'=>'tbl_servqual_survey'),'sq.IdSurvey=sqr.IdSurvey',array())
		->where('sq.IdSurvey=?',$idsurvey)
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdProgram=?',$idprogram)
		->where('sqr.active="1"');
		if ($idsubject!=null) $select->where('sq.IdSubject=?',$idsubject);
		if ($idgroup!=null) $select->where('sq.IdGroup=?',$idgroup);
		//echo $select;;
		$row=$db->fetchRow($select);
		if ($row) return true; else return false;
	
	}
public function getTarget($idsurvey,$idsemester,$idprogram,$idsubject=null,$idgroup=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdSurvey=?',$idsurvey)
		->where('sq.IdProgram=?',$idprogram)
		->where('sq.IdSemester=?',$idsemester);

		if ($idsubject!=null) {
			$select->join(array('sm'=>'tbl_subjectmaster'),'sq.IdSubject=sm.IdSubject');
			$select->where('sq.IdSubject=?',$idsubject);
		}
		if ($idgroup!=null) {
			$select->join(array('ct'=>'tbl_course_tagging_group'),'ct.IdCourseTaggingGroup=sq.IdGroup');
			$select->where('sq.IdGroup=?',$idgroup);
		}
	
		$row=$db->fetchRow($select);
         
		return $row;
	
	}
	
	public function getDataDetail($idserqual=null,$type=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=sq.IdSemester',array('SemesterName'=>'SemesterMainName'))
		->join(array('sqr'=>'tbl_servqual_survey'),'sq.IdSurvey=sqr.IdSurvey',array())
		->join(array('def'=>'tbl_definationms'),'sqr.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sq.IdSubject=sm.IdSubject',array('SubjectCode'=>'ShortName','SubjectName'=>'SubjectName','sks'=>'credithours'))
		->joinLeft(array('pr'=>'tbl_program'),'pr.IdProgram=sq.IdProgram',array('ProgramName'=>'ArabicName'))
		->joinLeft(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=sq.IdGroup',array('GroupName','GroupCode'));
		//->where('sq.active="1"');
		if ($type!=null)
			$select->where('DefinitionCode=?',$type);
		if ($idserqual!=null) {
			$select->where('IdSurveyTarget=?',$idserqual);
			$row=$db->fetchRow($select);
		}
		else
			$row=$db->fetchAll($select);
		return $row;
	
	}
	public function getDataBySurvey($idsurvey=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->distinct()
		->from(array('sq'=>$this->_name))
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sq.IdSemester')
		->joinLeft(array('sb'=>'tbl_subjectmaster'),'sq.IdSubject=sb.IdSubject',array('SubCode','SubjectName'=>'BahasaIndonesia','CreditHours'))
		->joinLeft(array('grp'=>'tbl_course_tagging_group'),'sq.IdGroup=grp.IdCourseTaggingGroup',array('GroupCode','GroupName'))
		->join(array('pr'=>'tbl_program'),'sq.IdProgram=pr.IdProgram',array('ProgramName'=>"ArabicName"))
		->order('sb.BahasaIndonesia');
		
		if ($idsurvey!=null) {
			$select->where('IdSurvey=?',$idsurvey);
			//$row=$db->fetchRow($select);
		}
		//else
		$row=$db->fetchAll($select);
		return $row;
	
	}
	public function getDataProgramBySurvey($idsurvey=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->distinct()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.type_survey=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'));
		//->where('sq.active="1"');
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sq.IdSemester',array('SemesterMainName'))
		//->joinLeft(array('sb'=>'tbl_subjectmaster'),'sq.IdSubject=sb.IdSubject',array('SubCode','SubjectName'=>'BahasaIndonesia'))
		//->joinLeft(array('grp'=>'tbl_course_tagging_group'),'sq.IdGroup=grp.IdCourseTaggingGroup',array('GroupCode','GroupName'))
		->join(array('pr'=>'tbl_program'),'sq.IdProgram=pr.IdProgram',array('ProgramName'=>"ArabicName"));
		
		if ($idsurvey!=null) {
			$select->where('IdSurvey=?',$idsurvey);
			//$row=$db->fetchRow($select);
		}
		//else
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
			$select->where('IdSurveyTarget=?',$idserqual);
		}
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
     
}