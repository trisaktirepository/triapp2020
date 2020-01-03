<?php

class  App_Model_Registration_DbTable_ApplyDefer extends Zend_Db_Table_Abstract {

	protected $_name = 'semester_defer_requests';
	protected $_primary = "id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getDeferHistory($idstd){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('a'=>$this->_name))
					  ->join(array('b'=>'tbl_semestermaster'),'a.IdSemesterMain=b.IdSemesterMaster')
					  ->join(array('c'=>'tbl_record_reason_defer'),'c.IdRecordResonDefer=a.request_type')
					  ->joinleft(array('d'=>'tbl_definationms'),'d.IdDefinition=a.status',array('ApprovalStatus'=>'d.BahasaIndonesia'))
					  ->where("IdStudentRegistration = ?",$idstd);
		$row = $db->fetchAll($select);	
		return $row; 
	}
	
	public function isIn($idstd,$semester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("a.IdStudentRegistration = ?",$idstd)
		->where("a.IdSemesterMain=?",$semester);
		$row = $db->fetchRow($select);
		return $row;
	}
	
}

?>