<?php
class Servqual_Model_DbTable_ServqualTransaction extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_transaction';
	protected $_primary ='IdTransaction';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdTransaction='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$lobjFormData['user_id']=$auth->getIdentity()->registration_id;
		$lobjFormData['update_date']=date("Y-m-d H:i:s");
		$ans=$this->isInDatabase($lobjFormData['IdTransactionResponden'], $lobjFormData['question_id']);
		if (!$ans) {
			$db->insert($this->_name,$lobjFormData);
			//echo $lastInsertId.'===insertedTrans';
		} else 
		{
			unset($lobjFormData['question_id']);
			unset($lobjFormData['IdTransactionResponden']);
			$this->updateData($lobjFormData, $ans['IdTransaction']);
		}
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdTransaction='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq.'=>$this->_name))
				->join(array('smt'=>'tbl_semestermaster'),'sq.idSemester=smt.IdSemesterMaster',array('smt.SemesterName','smt.SemesterCode'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdTransaction=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function isInDatabase($idresponden,$idquestion) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdTransactionResponden=?',$idresponden)
		->where('sq.question_id=?',$idquestion);
		$row=$db->fetchRow($select);
		return $row;
	
	}
     
}