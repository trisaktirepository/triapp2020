<?php
class Servqual_Model_DbTable_ServqualResultHead extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_result_head';
	protected $_primary ='IdHead';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdHead='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$idtransaction=$lobjFormData['IdServqualTransaction'];
		$transac=$this->isInData($idtransaction);
		if ($transac) {
			$this->updateData($lobjFormData,$transac['IdHead']);
			return $transac['IdHead'];
		} else {
			$db->insert($this->_name,$lobjFormData);
			$lastInsertId = $this->getAdapter()->lastInsertId();
			return $lastInsertId;
		}
		
	}
	
	
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdHead='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($IdSemester,$IdProgram){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('sth'=>'tbl_servqual_transaction_head'),'sq.IdServqualTransaction=sth.IdServqualTransaction')
				->join(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=sth.IdSemester')
				->join(array('pr'=>'tbl_program'),'pr.IdProgram=sth.IdProgram')
				->join(array('sr'=>'tbl_servqual_survey'),'sth.IdSurvey=sr.IdSurvey')
				->joinLeft(array('sb'=>'tbl_subjectmaster'),'sb.IdSubject=sth.IdSubject')
				->where('sth.IdProgram=?',$IdProgram)
				->where('sth.IdSemester=?',$IdSemester);
				
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function getMean($IdHead){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name),array())
		->join(array('sr'=>'tbl_servqual_result'),'sq.IdHead=sr.IdHead',array('sum_of_mean'=>'SUM(mean*sr.n_of_respondens)','n_of_respondens'=>'SUM(sr.n_of_respondens)'))
		->where('sr.IdHead=?',$IdHead)
		->group('sq.IdHead');
		$row=$db->fetchRow($select);
	//	echo var_dump($row);exit;
		return $row;
	
	}
	public function isInData($idServqualTransaction){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('IdServqualTransaction=?',$idServqualTransaction);
		
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
	
     
}