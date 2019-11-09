<?php
class Servqual_Model_DbTable_ServqualTransactionHead extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_transaction_head';
	protected $_primary ='IdServqualTransaction';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdServqualTransaction='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		//echo var_dump($auth->getIdentity());exit;
		$lobjFormData['user_id']=$auth->getIdentity()->registration_id;
		$lobjFormData['update_date']=date("Y-m-d H:i:s");
		$head=$this->isInDatabase($lobjFormData['IdSurveyTarget'],$lobjFormData['IdSurvey'], $lobjFormData['IdProgram'], $lobjFormData['IdSemester'],$lobjFormData['expectation'],$lobjFormData['IdSubject'],$lobjFormData['IdGroupCourse']);
		if (!$head) { 
			$db->insert($this->_name,$lobjFormData);
			$lastInsertId = $this->getAdapter()->lastInsertId();
			//echo $lastInsertId.'===inserted';
		}
		else
		{
			$lastInsertId=$head['IdServqualTransaction'];
			$this->updateData($lobjFormData,$lastInsertId );
		}
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualTransaction='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('smt'=>'tbl_semestermaster'),'sq.IdSemester=smt.IdSemesterMaster',array('smt.SemesterName','smt.SemesterCode'))
				->joinLeft(array('pr'=>'tbl_program'),'sq.IdProgram=pr.IdProgram',array('pr.ProgramCode','pr.ProgramName'))
				->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=sq.IdSubject');
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqualTransaction=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function isInDatabase($idsurveytarget,$idsurvey,$idprogram,$idsemester,$type,$idsubject,$idgrp) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdSurveyTarget=?',$idsurveytarget)
		->where('sq.IdSurvey=?',$idsurvey)
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdProgram=?',$idprogram)
		->where('sq.expectation=?',$type);
		if ($idsubject!=null)
			$select->where('sq.IdSubject=?',$idsubject);
		if ($idgrp!=null)
			$select->where('sq.IdGroupCourse=?',$idgrp);
		$row=$db->fetchRow($select);
		return $row;
		
	}
	public function surveyResult($idsurvey,$idsemester,$program) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('sq'=>$this->_name),array())
		->join(array('sqr'=>'tbl_servqual_transaction_responden'),'sq.IdServqualTransaction=sqr.IdServqualTransaction',array())
		->join(array('st'=>'tbl_servqual_transaction'),'st.IdTransactionResponden=sqr.IdSurveyResponden',array('n_of_respondens'=>'count( distinct IdResponden)','mean'=>'AVG(real_score)','sq.IdServqualTransaction','st.question_id'))
		->where('sq.IdProgram=?',$program)
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdSurvey=?',$idsurvey)
		->group('sq.IdServqualTransaction','st.question_id');
		$row=$db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
		
	
	}
	public function isExpectedDone($idstudentregistration,$idsemester,$idprogram=null,$idsurvey=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdProgram=?',$idprogram)
		->where('sq.expectation="0"');
		if ($idsurvey!=null) $select->where('sq.IdSurvey=?',$idsurvey);
		$row=$db->fetchRow($select);
		//echo var_dump($targets);exit;
		if ($row) {
			$select = $db->select()
		 	->from(array('sqr'=>'tbl_servqual_transaction_responden'))
			->where('sqr.IdResponden=?',$idstudentregistration)
			->where('sqr.IdServqualTransaction=?',$row['IdServqualTransaction'])
			->where('sqr.Complete= "1"');
			 $row=$db->fetchRow($select);
			// echo var_dump($row);echo $idstudentregistration;exit;
			 return $row;
			//echo var_dump($row);echo $idstudentregistration."=".$idsemester;exit;
			 
		}
		 
		return false;
	
	}
	
	public function isRealityDone($idstudentregistration,$idsemester,$idSurveyTarget,$idprogram) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdSurveyTarget=?',$idSurveyTarget)
		->where('sq.IdProgram=?',$idprogram)
		->where('sq.expectation="1"');
		$row=$db->fetchRow($select);
		if ($row) {
			$select = $db->select()
			->from(array('sqr'=>'tbl_servqual_transaction_responden'))
			->where('sqr.IdResponden=?',$idstudentregistration)
			->where('sqr.IdServqualTransaction=?',$row['IdServqualTransaction'])
			->where('sqr.Complete= "1"');
			$row=$db->fetchRow($select);
			return $row;
		}
		return array();
	
	
	}
	/*
	public function isRealityDone($idstudentregistration,$idsemester,$idSurveyTarget) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('sqr'=>'tbl_servqual_transaction_responden'),'sq.IdServqualTransaction=sqr.IdServqualTransaction')
		->where('sqr.IdResponden=?',$idstudentregistration)
		->where('sq.IdSemester=?',$idsemester)
		->where('sq.IdSurveyTarget=?',$idSurveyTarget)
		->where('sqr.Complete= "1"')
		->where('sq.expectation="1"');
		$row=$db->fetchRow($select);
		//echo var_dump($row);exit;
		return $row;
	
	
	}*/
}