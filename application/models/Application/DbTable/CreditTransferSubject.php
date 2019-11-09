<?php



class App_Model_Application_DbTable_CreditTransferSubject extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_credit_transfer_subject';
	protected $_primary = "idCTSubject";
		
	public function getDataByApplyId($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($this->_name)
		->where('idApply=?',$id);
		$row=$db->fetchAll($select);
		return $row;
	}
	
	public function getDataByApplyIdPdf($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('a'=>$this->_name),array('SubCodeAsal'=>'SubjectCode','SubjectNameAsal'=>'SubjectName','CreditHourAsal'=>'sks','GradeAsal'=>'Grade'))
		->joinLeft(array('dest'=>'tbl_credit_transfer_subject_result'),'a.idCTSubject=dest.idApply',array('dest.*','SubCode'=>'dest.SubjectCode','CreditHours'=>'dest.sks'))
		->where('a.idApply=?',$id);
		$row=$db->fetchAll($select);
		return $row;
	}
	  
	 
	
	public function addData($postData){
		
		$auth = Zend_Auth::getInstance();
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$db->insert($this->_name,$postData);
	}
	
	public function updateData($postData,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update($this->_name,$postData, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteDataBySubcode($idapply,$code){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where='idApply='.$idapply.' and SubjectCode="'.$code.'"';
		$db->delete($this->_name,$where);
	}
	public function isIn($idapply,$subjectcode) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
		->from($this->_name)
		->where('idApply=?',$idapply)
		->where('SubjectCode=?',$subjectcode);
		$row=$db->fetchRow($select);
		return $row;
	}

}

