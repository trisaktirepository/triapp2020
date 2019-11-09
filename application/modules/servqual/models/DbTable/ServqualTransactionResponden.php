<?php
class Servqual_Model_DbTable_ServqualTransactionResponden extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_servqual_transaction_responden';
	protected $_primary ='IdSurveyResponden';
	
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdSurveyResponden='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$lobjFormData['userId']=$auth->getIdentity()->registration_id;
		$lobjFormData['update_date']=date("Y-m-d H:i:s");
		$respondens=$this->isInDatabase($lobjFormData['IdServqualTransaction'], $lobjFormData['IdResponden']);
		if (!$respondens) { 
			$db->insert($this->_name,$lobjFormData);
			$lastInsertId = $this->getAdapter()->lastInsertId();
			//echo $lastInsertId.'===insertedresp';
		}
		else 
		{
			
			$lastInsertId=$respondens['IdServqualTransaction'];
		}
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdSurveyResponden='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name));
				//->join(array('smt'=>'tbl_semestermaster'),'sq.idSemester=smt.IdSemesterMaster',array('smt.SemesterName','smt.SemesterCode'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdSurveyResponden=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function isInDatabase($idsurveyhead,$idresponden) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->where('sq.IdServqualTransaction=?',$idsurveyhead)
		->where('sq.IdResponden=?',$idresponden);
		
		$row=$db->fetchRow($select);
		return $row;
	
	}
     
}