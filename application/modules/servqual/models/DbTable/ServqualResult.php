<?php
class Servqual_Model_DbTable_ServqualResult extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_result';
	protected $_primary ='IdServqualResult';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdServqualResult='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$idhead=$lobjFormData['IdHead'];
		$questionid=$lobjFormData['question_id'];
		$id=$this->isInData($idhead, $questionid);
		if ($id) {
			$this->updateData($lobjFormData, $id['IdServqualResult']);
		} else
			$db->insert($this->_name,$lobjFormData);
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualResult='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name));
				//->join(array('smt'=>'tbl_semestermaster'),'sq.idSemester=smt.IdSemesterMaster',array('smt.SemesterName','smt.SemesterCode'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqualResult=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function isInData($idHead, $question_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('smt'=>'tbl_semestermaster'),'sq.idSemester=smt.IdSemesterMaster',array('smt.SemesterName','smt.SemesterCode'));
		->where('sq.IdHead=?',$idHead)
		->where('sq.question_id=?',$question_id);
		$row=$db->fetchRow($select);
		return $row;
	
	}
	
     
}